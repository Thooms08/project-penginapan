<?php

namespace Modules\DataMaster\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Carbon\Carbon;

class VisitorDataController extends Controller
{
    /**
     * Default date range: 30 days ago → 30 days ahead (covers both past and upcoming bookings).
     */
    private function parseDates(Request $request): array
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->addDays(30)->endOfDay();

        // Prevent reversed range
        if ($from->gt($to)) [$from, $to] = [$to, $from];

        return [$from, $to];
    }

    /**
     * GET /admin/visitor-data
     * Main page — visitor analytics.
     */
    public function index(Request $request)
    {
        [$from, $to] = $this->parseDates($request);

        // ── Chart data: daily check-in count within range ──────────────
        $chartRaw = Booking::selectRaw('DATE(check_in_date) as day, COUNT(*) as total')
            ->whereIn('booking_status', [
                Booking::STATUS_CHECKED_IN,
                Booking::STATUS_CHECKED_OUT,
                Booking::STATUS_CONFIRMED,
            ])
            ->whereBetween('check_in_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Fill every day in range (including zeros)
        $chartLabels = [];
        $chartData   = [];
        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m-d');
            $chartLabels[] = $cursor->locale('id')->isoFormat('D MMM');
            $chartData[]   = $chartRaw->has($key) ? (int)$chartRaw[$key]->total : 0;
            $cursor->addDay();
        }

        // ── Summary stats ──────────────────────────────────────────────
        $totalCheckins = array_sum($chartData);

        $avgNights = Booking::whereIn('booking_status', [
                Booking::STATUS_CHECKED_IN,
                Booking::STATUS_CHECKED_OUT,
                Booking::STATUS_CONFIRMED,
            ])
            ->whereBetween('check_in_date', [$from->toDateString(), $to->toDateString()])
            ->avg('nights');

        $uniqueVisitors = Booking::whereIn('booking_status', [
                Booking::STATUS_CHECKED_IN,
                Booking::STATUS_CHECKED_OUT,
                Booking::STATUS_CONFIRMED,
            ])
            ->whereBetween('check_in_date', [$from->toDateString(), $to->toDateString()])
            ->distinct('user_id')
            ->count('user_id');

        // ── Visitor table data ─────────────────────────────────────────
        $visitors = $this->buildVisitorStats($from, $to);

        return view('datamaster::Admin.visitor-data', [
            'from'           => $from->format('Y-m-d'),
            'to'             => $to->format('Y-m-d'),
            'fromLabel'      => $from->locale('id')->isoFormat('D MMMM YYYY'),
            'toLabel'        => $to->locale('id')->isoFormat('D MMMM YYYY'),
            'chartLabels'    => $chartLabels,
            'chartData'      => $chartData,
            'totalCheckins'  => $totalCheckins,
            'avgNights'      => round((float)$avgNights, 1),
            'uniqueVisitors' => $uniqueVisitors,
            'visitors'       => $visitors,
        ]);
    }

    /**
     * Build per-visitor stats for the table.
     */
    private function buildVisitorStats(Carbon $from, Carbon $to): \Illuminate\Support\Collection
    {
        // Aggregate booking stats per user within range
        // Jam CI/CO diambil dari checked_in_at/checked_out_at (DATETIME),
        // bukan check_in_date/check_out_date yang bertipe DATE.
        $stats = Booking::selectRaw('
                user_id,
                COUNT(*)                                        AS checkin_count,
                ROUND(AVG(nights), 1)                          AS avg_nights,
                MIN(TIME(checked_in_at))                       AS min_ci,
                MAX(TIME(checked_in_at))                       AS max_ci,
                MIN(TIME(checked_out_at))                      AS min_co,
                MAX(TIME(checked_out_at))                      AS max_co
            ')
            ->whereIn('booking_status', [
                Booking::STATUS_CHECKED_IN,
                Booking::STATUS_CHECKED_OUT,
                Booking::STATUS_CONFIRMED,
            ])
            ->whereBetween('check_in_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('user_id')
            ->orderByDesc('checkin_count')
            ->get();

        if ($stats->isEmpty()) return collect();

        $userIds = $stats->pluck('user_id')->toArray();

        // Load users with their profile in one query (LEFT JOIN — profile boleh kosong)
        $users = DB::table('users')
            ->leftJoin('profile_users', 'users.id', '=', 'profile_users.user_id')
            ->whereIn('users.id', $userIds)
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'profile_users.wa        as wa',
                'profile_users.city      as city',
                'profile_users.province  as province',
                'profile_users.country   as country',
                'profile_users.foto      as foto'
            )
            ->get()
            ->keyBy('id');

        return $stats->map(function ($s, $index) use ($users) {
            $user = $users->get($s->user_id);

            // Format jam range CI & CO dari checked_in_at / checked_out_at
            // Jika null (belum pernah CI/CO via sistem), tampilkan '—'
            $ciFrom = $s->min_ci ? substr($s->min_ci, 0, 5) : null;
            $ciTo   = $s->max_ci ? substr($s->max_ci, 0, 5) : null;
            $coFrom = $s->min_co ? substr($s->min_co, 0, 5) : null;
            $coTo   = $s->max_co ? substr($s->max_co, 0, 5) : null;

            // Hapus '00:00' karena artinya timestamp belum diisi
            $ciFrom = ($ciFrom && $ciFrom !== '00:00') ? $ciFrom : null;
            $ciTo   = ($ciTo   && $ciTo   !== '00:00') ? $ciTo   : null;
            $coFrom = ($coFrom && $coFrom !== '00:00') ? $coFrom : null;
            $coTo   = ($coTo   && $coTo   !== '00:00') ? $coTo   : null;

            $ciLabel = $ciFrom
                ? ($ciTo && $ciTo !== $ciFrom ? "{$ciFrom} – {$ciTo}" : $ciFrom)
                : '—';

            $coLabel = $coFrom
                ? ($coTo && $coTo !== $coFrom ? "{$coFrom} – {$coTo}" : $coFrom)
                : '—';

            // Asal: build from available profile fields
            $asalParts = array_filter([
                $user?->city,
                $user?->province,
                $user?->country,
            ]);
            $asalShort = collect($asalParts)->first() ?? '—';
            $asalFull  = implode(', ', $asalParts) ?: '—';

            return (object) [
                'rank'          => $index + 1,
                'user_id'       => $s->user_id,
                'name'          => $user?->name ?? 'Tamu #' . $s->user_id,
                'email'         => $user?->email ?? '—',
                'wa'            => $user?->wa ?? '—',
                'foto'          => ($user && $user->foto) ? asset($user->foto) : null,
                'asal_short'    => $asalShort,
                'asal_full'     => $asalFull,
                'city'          => $user?->city ?? '—',
                'province'      => $user?->province ?? '—',
                'country'       => $user?->country ?? '—',
                'checkin_count' => (int) $s->checkin_count,
                'avg_nights'    => (float) $s->avg_nights,
                'ci_label'      => $ciLabel,
                'co_label'      => $coLabel,
            ];
        });
    }
}

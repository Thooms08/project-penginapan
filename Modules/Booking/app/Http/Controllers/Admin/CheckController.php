<?php

namespace Modules\Booking\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\CheckInOutSetting;
use Carbon\Carbon;

class CheckController extends Controller
{
    /**
     * Display the Check In & Out management page.
     */
    public function index()
    {
        // Today's check-in and check-out times (Eloquent collections for Blade)
        $todayCheckIns  = CheckInOutSetting::todayCheckIns();
        $todayCheckOuts = CheckInOutSetting::todayCheckOuts();

        // All settings as Eloquent collection (for @foreach in Blade)
        $allSettings = CheckInOutSetting::orderBy('date')->orderBy('type')->orderBy('time')->get();

        // Pre-serialised arrays for inline JS — avoids Blade/arrow-function conflicts
        $jsAllSettings = $allSettings->map(function ($s) {
            return [
                'id'    => $s->id,
                'date'  => $s->date->format('Y-m-d'),
                'short' => $s->short_date,
                'type'  => $s->type,
                'time'  => $s->formatted_time,
                'notes' => $s->notes,
            ];
        })->values()->toArray();

        $jsTodayCheckIns = $todayCheckIns->map(function ($s) {
            return [
                'id'    => $s->id,
                'time'  => $s->formatted_time,
                'notes' => $s->notes,
            ];
        })->values()->toArray();

        $jsTodayCheckOuts = $todayCheckOuts->map(function ($s) {
            return [
                'id'    => $s->id,
                'time'  => $s->formatted_time,
                'notes' => $s->notes,
            ];
        })->values()->toArray();

        return view('booking::Admin.check', compact(
            'todayCheckIns',
            'todayCheckOuts',
            'allSettings',
            'jsAllSettings',
            'jsTodayCheckIns',
            'jsTodayCheckOuts'
        ));
    }

    /**
     * Store multiple check-in/out settings at once.
     * Accepts an array of rows, each with: date, type, time, notes.
     * Existing duplicate (date+type+time) rows are silently skipped (upsert).
     */
    public function store(Request $request)
    {
        $request->validate([
            'rows'              => 'required|array|min:1',
            'rows.*.date'       => 'required|date_format:Y-m-d',
            'rows.*.type'       => 'required|in:check_in,check_out',
            'rows.*.time'       => 'required|date_format:H:i',
            'rows.*.notes'      => 'nullable|string|max:500',
        ], [
            'rows.required'             => 'Minimal satu baris pengaturan harus diisi.',
            'rows.*.date.required'      => 'Tanggal wajib diisi.',
            'rows.*.date.date_format'   => 'Format tanggal tidak valid.',
            'rows.*.type.required'      => 'Tipe (Check-In/Check-Out) wajib dipilih.',
            'rows.*.type.in'            => 'Tipe tidak valid.',
            'rows.*.time.required'      => 'Jam wajib diisi.',
            'rows.*.time.date_format'   => 'Format jam tidak valid.',
        ]);

        $saved = 0;
        $updated = 0;

        DB::transaction(function () use ($request, &$saved, &$updated) {
            foreach ($request->rows as $row) {
                $existing = CheckInOutSetting::where('date', $row['date'])
                    ->where('type', $row['type'])
                    ->where('time', $row['time'] . ':00')
                    ->first();

                if ($existing) {
                    $existing->update([
                        'notes'      => $row['notes'] ?? null,
                        'updated_by' => Auth::id(),
                    ]);
                    $updated++;
                } else {
                    CheckInOutSetting::create([
                        'date'       => $row['date'],
                        'type'       => $row['type'],
                        'time'       => $row['time'] . ':00',
                        'notes'      => $row['notes'] ?? null,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                    $saved++;
                }
            }
        });

        $message = '';
        if ($saved > 0)   $message .= $saved . ' data berhasil disimpan. ';
        if ($updated > 0) $message .= $updated . ' data diperbarui.';

        return redirect()
            ->route('admin.check.index')
            ->with('success', trim($message) ?: 'Pengaturan berhasil disimpan.');
    }

    /**
     * Delete a single setting record.
     */
    public function destroy(CheckInOutSetting $check)
    {
        $check->delete();

        return redirect()
            ->route('admin.check.index')
            ->with('success', 'Data pengaturan berhasil dihapus.');
    }

    /**
     * Return all settings as JSON (for AJAX / modal data).
     */
    public function all()
    {
        $rows = CheckInOutSetting::orderBy('date')->orderBy('type')->orderBy('time')->get()
            ->map(function ($s) {
                return [
                    'id'             => $s->id,
                    'date'           => $s->date->format('Y-m-d'),
                    'short_date'     => $s->short_date,
                    'formatted_date' => $s->formatted_date,
                    'type'           => $s->type,
                    'time'           => $s->formatted_time,
                    'notes'          => $s->notes,
                ];
            });

        return response()->json(['data' => $rows]);
    }

    /**
     * Return today's settings as JSON.
     */
    public function today()
    {
        $checkIns  = CheckInOutSetting::todayCheckIns()->map(fn($s) => [
            'id'   => $s->id,
            'time' => $s->formatted_time,
            'notes'=> $s->notes,
        ]);
        $checkOuts = CheckInOutSetting::todayCheckOuts()->map(fn($s) => [
            'id'   => $s->id,
            'time' => $s->formatted_time,
            'notes'=> $s->notes,
        ]);

        return response()->json([
            'date'       => Carbon::today()->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'check_ins'  => $checkIns,
            'check_outs' => $checkOuts,
        ]);
    }
}

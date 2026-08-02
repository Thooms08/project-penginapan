<?php

namespace Modules\Booking\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\CheckInOutSetting;
use Modules\Booking\Models\SurchargeSetting;
use Modules\Booking\Models\Booking;
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

        // Surcharge settings
        $surcharges   = SurchargeSetting::allForAdmin();
        $jsSurcharges = $surcharges->map(function ($s) {
            return [
                'id'             => $s->id,
                'type'           => $s->type,
                'type_label'     => $s->type_label,
                'threshold'      => $s->formatted_threshold,
                'fee_type'       => $s->fee_type,
                'fee_type_label' => $s->fee_type_label,
                'fee_amount'     => $s->fee_amount,
                'formatted_fee'  => $s->formatted_fee,
                'label'          => $s->auto_label,
                'description'    => $s->description,
                'is_active'      => $s->is_active,
            ];
        })->values()->toArray();

        // Tamu yang sedang check-in (booking_status = checked_in)
        $checkedInGuests = Booking::with(['user', 'room'])
            ->where('booking_status', Booking::STATUS_CHECKED_IN)
            ->orderBy('check_in_date')
            ->get();

        // Tamu yang sudah check-out (semua waktu, bukan hanya hari ini)
        $checkedOutGuests = Booking::with(['user', 'room'])
            ->where('booking_status', Booking::STATUS_CHECKED_OUT)
            ->orderByDesc('checked_out_at')
            ->limit(50)
            ->get();

        // Tamu yang check-out hari ini (check_out_date = today, status checked_in atau checked_out)
        $checkingOutToday = Booking::with(['user', 'room'])
            ->whereDate('check_out_date', Carbon::today())
            ->whereIn('booking_status', [Booking::STATUS_CHECKED_IN, Booking::STATUS_CHECKED_OUT])
            ->orderBy('check_out_date')
            ->get();

        return view('booking::Admin.check', compact(
            'todayCheckIns',
            'todayCheckOuts',
            'allSettings',
            'jsAllSettings',
            'jsTodayCheckIns',
            'jsTodayCheckOuts',
            'surcharges',
            'jsSurcharges',
            'checkedInGuests',
            'checkedOutGuests',
            'checkingOutToday'
        ));
    }

    /**
     * Mark a booking as checked-out.
     * POST /admin/check/checkout/{booking}
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->booking_status !== Booking::STATUS_CHECKED_IN) {
            return redirect()->route('admin.check.index')
                ->with('error', 'Tamu ini tidak sedang check-in.');
        }

        $booking->update([
            'booking_status' => Booking::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
        ]);

        return redirect()->route('admin.check.index')
            ->with('success', "Tamu {$booking->user->name} berhasil di-check-out pada " . now()->format('H:i') . '.');
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

    // ══════════════════════════════════════════════════════
    //  SURCHARGE SETTINGS — Early Check-In / Late Check-Out
    // ══════════════════════════════════════════════════════

    /**
     * Store a new surcharge setting.
     */
    public function storeSurcharge(Request $request)
    {
        $request->validate([
            'type'           => 'required|in:early_checkin,late_checkout',
            'threshold_time' => 'required|date_format:H:i',
            'fee_type'       => 'required|in:fixed,percent',
            'fee_amount'     => 'required|integer|min:0',
            'label'          => 'nullable|string|max:120',
            'description'    => 'nullable|string|max:500',
        ], [
            'type.required'           => 'Tipe surcharge wajib dipilih.',
            'type.in'                 => 'Tipe tidak valid.',
            'threshold_time.required' => 'Jam batas wajib diisi.',
            'threshold_time.date_format' => 'Format jam tidak valid (H:i).',
            'fee_type.required'       => 'Jenis biaya wajib dipilih.',
            'fee_type.in'             => 'Jenis biaya tidak valid.',
            'fee_amount.required'     => 'Nilai biaya wajib diisi.',
            'fee_amount.integer'      => 'Nilai biaya harus berupa angka.',
            'fee_amount.min'          => 'Nilai biaya tidak boleh negatif.',
        ]);

        // Validasi tambahan: percent tidak boleh > 100
        if ($request->fee_type === 'percent' && $request->fee_amount > 100) {
            return back()
                ->withErrors(['fee_amount' => 'Persentase tidak boleh melebihi 100%.'])
                ->withInput();
        }

        SurchargeSetting::create([
            'type'           => $request->type,
            'threshold_time' => $request->threshold_time . ':00',
            'fee_type'       => $request->fee_type,
            'fee_amount'     => $request->fee_amount,
            'label'          => $request->label ?: null,
            'description'    => $request->description ?: null,
            'is_active'      => true,
            'created_by'     => Auth::id(),
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('admin.check.index')
            ->with('success', 'Pengaturan biaya tambahan berhasil disimpan.');
    }

    /**
     * Update an existing surcharge setting.
     */
    public function updateSurcharge(Request $request, SurchargeSetting $surcharge)
    {
        $request->validate([
            'type'           => 'required|in:early_checkin,late_checkout',
            'threshold_time' => 'required|date_format:H:i',
            'fee_type'       => 'required|in:fixed,percent',
            'fee_amount'     => 'required|integer|min:0',
            'label'          => 'nullable|string|max:120',
            'description'    => 'nullable|string|max:500',
        ]);

        if ($request->fee_type === 'percent' && $request->fee_amount > 100) {
            return back()
                ->withErrors(['fee_amount' => 'Persentase tidak boleh melebihi 100%.'])
                ->withInput();
        }

        $surcharge->update([
            'type'           => $request->type,
            'threshold_time' => $request->threshold_time . ':00',
            'fee_type'       => $request->fee_type,
            'fee_amount'     => $request->fee_amount,
            'label'          => $request->label ?: null,
            'description'    => $request->description ?: null,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('admin.check.index')
            ->with('success', 'Pengaturan biaya tambahan berhasil diperbarui.');
    }

    /**
     * Toggle active/inactive status for a surcharge.
     */
    public function toggleSurcharge(SurchargeSetting $surcharge)
    {
        $surcharge->update([
            'is_active'  => ! $surcharge->is_active,
            'updated_by' => Auth::id(),
        ]);

        $status = $surcharge->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.check.index')
            ->with('success', "Biaya tambahan \"{$surcharge->auto_label}\" berhasil {$status}.");
    }

    /**
     * Delete a surcharge setting.
     */
    public function destroySurcharge(SurchargeSetting $surcharge)
    {
        $label = $surcharge->auto_label;
        $surcharge->delete();

        return redirect()
            ->route('admin.check.index')
            ->with('success', "Biaya tambahan \"{$label}\" berhasil dihapus.");
    }
}

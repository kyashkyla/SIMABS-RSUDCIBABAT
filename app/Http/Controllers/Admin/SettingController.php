<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan.
     */
    public function edit()
    {
        return Inertia::render('Auth/Admin/Setting/Index', [
            'jamKerja' => [
                'workStart' => Setting::get('work_start', '07:00'),
                'workEnd' => Setting::get('work_end', '15:00'),
                'lateTolerance' => Setting::get('late_tolerance_minutes', '15'),
            ],
            'notifikasi' => [
                'notifyAdminNewRequest' => (bool) Setting::get('notify_admin_new_request', '1'),
                'notifyEmployeeDecision' => (bool) Setting::get('notify_employee_decision', '1'),
            ],
        ]);
    }

    /**
     * Simpan pengaturan Jam Kerja & Toleransi Keterlambatan.
     */
    public function updateJamKerja(Request $request)
    {
        $validated = $request->validate([
            'work_start' => 'required|date_format:H:i',
            'work_end' => 'required|date_format:H:i|after:work_start',
            'late_tolerance' => 'required|integer|min:0|max:120',
        ]);

        Setting::set('work_start', $validated['work_start'], 'Jam Masuk', 'jam_kerja');
        Setting::set('work_end', $validated['work_end'], 'Jam Pulang', 'jam_kerja');
        Setting::set('late_tolerance_minutes', $validated['late_tolerance'], 'Toleransi Keterlambatan (menit)', 'jam_kerja');

        return back()->with('success', 'Pengaturan jam kerja berhasil disimpan.');
    }

    /**
     * Simpan pengaturan Notifikasi Sistem (KF-12).
     */
    public function updateNotifikasi(Request $request)
    {
        $validated = $request->validate([
            'notify_admin_new_request' => 'boolean',
            'notify_employee_decision' => 'boolean',
        ]);

        Setting::set('notify_admin_new_request', $validated['notify_admin_new_request'] ? '1' : '0', group: 'notifikasi');
        Setting::set('notify_employee_decision', $validated['notify_employee_decision'] ? '1' : '0', group: 'notifikasi');

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan.');
    }

    /**
     * Ubah password akun admin yang sedang login.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password admin berhasil diperbarui.');
    }
}
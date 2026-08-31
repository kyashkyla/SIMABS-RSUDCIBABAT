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
                'workStartPagi' => Setting::get('work_start_pagi', '07:00'),
                'workEndPagi' => Setting::get('work_end_pagi', '15:00'),
                
                'workStartSiang' => Setting::get('work_start_siang', '15:00'),
                'workEndSiang' => Setting::get('work_end_siang', '23:00'),
                
                'workStartMalam' => Setting::get('work_start_malam', '23:00'),
                'workEndMalam' => Setting::get('work_end_malam', '07:00'),
                
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
            'work_start_pagi' => 'required|date_format:H:i',
            'work_end_pagi' => 'required|date_format:H:i',
            'work_start_siang' => 'required|date_format:H:i',
            'work_end_siang' => 'required|date_format:H:i',
            'work_start_malam' => 'required|date_format:H:i',
            'work_end_malam' => 'required|date_format:H:i',
            'late_tolerance' => 'required|integer|min:0|max:120',
        ]);

        Setting::set('work_start_pagi', $validated['work_start_pagi'], 'Jam Masuk Pagi', 'jam_kerja');
        Setting::set('work_end_pagi', $validated['work_end_pagi'], 'Jam Pulang Pagi', 'jam_kerja');

        Setting::set('work_start_siang', $validated['work_start_siang'], 'Jam Masuk Siang', 'jam_kerja');
        Setting::set('work_end_siang', $validated['work_end_siang'], 'Jam Pulang Siang', 'jam_kerja');

        Setting::set('work_start_malam', $validated['work_start_malam'], 'Jam Masuk Malam', 'jam_kerja');
        Setting::set('work_end_malam', $validated['work_end_malam'], 'Jam Pulang Malam', 'jam_kerja');

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
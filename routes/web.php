<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
});

// Profile (pegawai & admin yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
| Halaman login admin dibiarkan publik (guest), sisanya WAJIB login
| dan berperan sebagai admin (role:admin). Sebelumnya seluruh rute admin
| tidak memiliki middleware sama sekali sehingga bisa diakses tanpa login.
*/

Route::get('/admin/login', function () {
    return Inertia::render('Auth/Admin/AdminLogin');
})->middleware('guest')->name('admin.login');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Data Pegawai (KF-03)
    Route::get('/pegawai', [EmployeeController::class, 'index'])->name('employees');
    Route::get('/pegawai/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/pegawai', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/pegawai/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/pegawai/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/pegawai/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/pegawai/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/pegawai/{employee}/delete', [EmployeeController::class, 'deleteConfirm'])->name('employees.delete');

    Route::get('/pegawai/{employee}/reset-password', [EmployeeController::class, 'resetPasswordForm'])
        ->name('employees.reset-password');
    Route::put('/pegawai/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])
        ->name('employees.reset-password.update');

    // Reset perangkat login pegawai (KF-02 / UC-09)
    Route::put('/pegawai/{employee}/reset-device', [EmployeeController::class, 'resetDevice'])
        ->name('employees.reset-device');

    // Persetujuan Absensi Alternatif (KF-07)
    Route::get('/persetujuan-absensi', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::get('/persetujuan-absensi/{persetujuan_absensi}', [ApprovalController::class, 'show'])->name('approvals.show');
    Route::put('/persetujuan-absensi/{persetujuan_absensi}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::put('/persetujuan-absensi/{persetujuan_absensi}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');

    // Riwayat Absensi (KF-10)
    Route::get('/absensi', [AdminAttendanceController::class, 'index'])->name('attendance.history');

    // Dashboard & Laporan (KF-11)
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/laporan/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');

    // Pengaturan Sistem (lokasi & radius absensi - KF-04/KNF-06, jam kerja, notifikasi, password admin)
    Route::get('/pengaturan', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/pengaturan/lokasi', [SettingController::class, 'updateLokasi'])->name('settings.lokasi');
    Route::put('/pengaturan/jam-kerja', [SettingController::class, 'updateJamKerja'])->name('settings.jam-kerja');
    Route::put('/pengaturan/notifikasi', [SettingController::class, 'updateNotifikasi'])->name('settings.notifikasi');
    Route::put('/pengaturan/password', [SettingController::class, 'updatePassword'])->name('settings.password');

    // Notifikasi Sistem (KF-12)
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Pegawai
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/pegawai/dashboard', [PegawaiDashboardController::class, 'index'])
        ->name('pegawai.dashboard');
});

require __DIR__.'/auth.php';

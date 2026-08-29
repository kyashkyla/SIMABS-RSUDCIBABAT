<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Pegawai\AttendanceController as PegawaiAttendanceController;
use App\Http\Controllers\Pegawai\DashboardController as PegawaiDashboardController;
use App\Http\Controllers\Pegawai\ProfileController as PegawaiProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\OfficeLocationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Landing');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin
Route::get('/admin/login', function () {
    return Inertia::render('Auth/Admin/AdminLogin');
})->name('admin.login');

Route::get('/admin/dashboard', function () {
    return Inertia::render('Auth/Admin/Dashboard');
})->name('admin.dashboard');

Route::get('/admin/pegawai', [EmployeeController::class, 'index'])
    ->name('admin.employees');

Route::get('/admin/pegawai/create', [EmployeeController::class, 'create'])
    ->name('admin.employees.create');

Route::post('/admin/pegawai', [EmployeeController::class, 'store'])
    ->name('admin.employees.store');

Route::get('/admin/pegawai/{employee}', [EmployeeController::class, 'show'])
    ->name('admin.employees.show');

Route::get('/admin/pegawai/{employee}/edit', [EmployeeController::class, 'edit'])
    ->name('admin.employees.edit');

Route::put('/admin/pegawai/{employee}', [EmployeeController::class, 'update'])
    ->name('admin.employees.update');

Route::delete('/admin/pegawai/{employee}', [EmployeeController::class, 'destroy'])
    ->name('admin.employees.destroy');

Route::get('/admin/pegawai/{employee}/delete', [EmployeeController::class, 'deleteConfirm'])
    ->name('admin.employees.delete');
    
Route::get('/admin/pegawai/{employee}/reset-password', function (\App\Models\Employee $employee) {
    return Inertia::render('Admin/Employee/ResetPassword', [
        'employee' => $employee->load('user'),
    ]);
})->name('admin.employees.reset-password');

Route::put('/admin/pegawai/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])
    ->name('admin.employees.reset-password.update');
    
Route::get('/admin/pegawai/{employee}/reset-password', [EmployeeController::class, 'resetPasswordForm'])
    ->name('admin.employees.reset-password');

Route::put('/admin/pegawai/{employee}/reset-password', [EmployeeController::class, 'resetPassword'])
    ->name('admin.employees.reset-password.update');

    // admin-persetujuan
Route::get('/admin/persetujuan-absensi', function () {
    return Inertia::render('Auth/Admin/Approval/Index');
})->name('admin.approvals.index');

Route::get('/admin/persetujuan-absensi/{id}', function ($id) {
    return Inertia::render('Auth/Admin/Approval/Show', [
        'id' => $id,
    ]);
})->name('admin.approvals.show');

    // admin-riwayat
Route::get('/admin/absensi', function () {
    return Inertia::render('Auth/Admin/Attendance/History');
})->name('admin.attendance.history');

    // admin-laporan
Route::get('/admin/laporan', function () {
    return Inertia::render('Auth/Admin/Report/Index');
})->name('admin.reports.index');

    // admin-pengaturan lokasi & radius absensi
Route::get('/admin/pengaturan', [OfficeLocationController::class, 'index'])
    ->name('admin.settings.index');

Route::post('/admin/pengaturan/lokasi', [OfficeLocationController::class, 'store'])
    ->name('admin.settings.lokasi.store');

Route::put('/admin/pengaturan/lokasi/{location}', [OfficeLocationController::class, 'update'])
    ->name('admin.settings.lokasi.update');

Route::delete('/admin/pengaturan/lokasi/{location}', [OfficeLocationController::class, 'destroy'])
    ->name('admin.settings.lokasi.destroy');

Route::patch('/admin/pengaturan/lokasi/{location}/toggle-aktif', [OfficeLocationController::class, 'toggleActive'])
    ->name('admin.settings.lokasi.toggle-aktif');

// Pegawai
Route::middleware(['auth', 'role:pegawai'])->group(function () {

   Route::get('/pegawai/dashboard', [PegawaiDashboardController::class, 'index'])
    ->name('pegawai.dashboard');

    // Profile Saya pegawai
    Route::get('/pegawai/profile', [PegawaiProfileController::class, 'show'])
        ->name('pegawai.profile');

    Route::get('/pegawai/absensi', [PegawaiAttendanceController::class, 'index'])
        ->name('pegawai.absensi');

    Route::get('/pegawai/absensi/lokasi', [PegawaiAttendanceController::class, 'locationConfig'])
        ->name('pegawai.absensi.lokasi');

    Route::post('/pegawai/absensi/validasi-lokasi', [PegawaiAttendanceController::class, 'validateLocation'])
        ->name('pegawai.absensi.validasi-lokasi');

    Route::post('/pegawai/absensi/simpan', [PegawaiAttendanceController::class, 'store'])
        ->name('pegawai.absensi.simpan');

    Route::get('/pegawai/riwayat-absensi', [PegawaiAttendanceController::class, 'riwayat'])
        ->name('pegawai.riwayat');
        
    Route::post('/pegawai/absensi/pulang', [PegawaiAttendanceController::class, 'storeCheckOut'])
    ->name('pegawai.absensi.pulang');

    Route::get('/pegawai/laporan', [PegawaiAttendanceController::class, 'laporan'])
        ->name('pegawai.laporan');

    Route::get('/pegawai/pengaturan', [PegawaiProfileController::class, 'edit'])
        ->name('pegawai.pengaturan');

    Route::post('/pegawai/pengaturan', [PegawaiProfileController::class, 'update'])
        ->name('pegawai.pengaturan.update');

});

require __DIR__.'/auth.php';
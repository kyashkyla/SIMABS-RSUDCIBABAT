<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Dashboard pegawai (placeholder dasar).
     * NOTE: lengkapi dengan data absensi milik pegawai yang sedang login
     * sesuai kebutuhan modul Pegawai (di luar cakupan modul Admin).
     */
    public function index()
    {
        $employee = auth()->user()->employee;

        return Inertia::render('Dashboard', [
            'employee' => $employee,
        ]);
    }
}

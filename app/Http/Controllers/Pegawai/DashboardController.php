<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('employee');

        if (!$user->employee) {
            abort(404, 'Data pegawai tidak ditemukan.');
        }

        return Inertia::render('Auth/pegawai/Dashboard', [
            'user' => $user,
            'employee' => $user->employee,
        ]);
    }
}
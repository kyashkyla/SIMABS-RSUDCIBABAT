<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlternativeRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
            'selfie_photo' => 'required|image|max:2048',
            'id_card_photo' => 'required|image|max:2048',
        ]);

        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        $selfiePath = $request->file('selfie_photo')->store('attendance-requests/selfie', 'public');
        $idCardPath = $request->file('id_card_photo')->store('attendance-requests/idcard', 'public');

        AttendanceRequest::create([
            'employee_id' => $employee->id,
            'reason' => $validated['reason'],
            'selfie_photo' => $selfiePath,
            'id_card_photo' => $idCardPath,
            'status' => 'pending',
        ]);

        return redirect()->route('pegawai.dashboard')->with('success', 'Pengajuan berhasil dikirim. Menunggu persetujuan admin.');
    }
}

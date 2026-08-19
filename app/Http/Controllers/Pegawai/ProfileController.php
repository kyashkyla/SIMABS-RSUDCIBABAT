<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Halaman "Profil Saya" (read-only, data lengkap kepegawaian).
     */
    public function show(): Response
    {
        $user = Auth::user()->load('employee');
        $employee = $user->employee;

        return Inertia::render('Auth/pegawai/Profile', [
            'pegawai' => $this->formatPegawai($user, $employee),
        ]);
    }

    /**
     * Halaman "Pengaturan" (email & foto bisa diedit, sisanya read-only).
     */
    public function edit(): Response
    {
        $user = Auth::user()->load('employee');
        $employee = $user->employee;

        return Inertia::render('Auth/pegawai/Pengaturan', [
            'pegawai' => $this->formatPegawai($user, $employee),
        ]);
    }

    /**
     * Update email & foto profil pegawai. Field lain (nama, NIP, jabatan,
     * unit, dst) sengaja tidak bisa diubah dari sini karena hanya admin
     * yang berhak mengubah data kepegawaian.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user()->load('employee');
        $employee = $user->employee;

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPG atau PNG.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        // Samakan email di tabel users dan employees supaya konsisten
        $user->update(['email' => $validated['email']]);

        $employeeData = ['email' => $validated['email']];

        if ($request->hasFile('photo')) {
            // Hapus foto lama supaya storage tidak menumpuk file lama
            if ($employee?->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $employeeData['photo'] = $request->file('photo')
                ->store('employee-photos', 'public');
        }

        if ($employee) {
            $employee->update($employeeData);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Susun data pegawai untuk dikirim ke halaman Vue.
     */
    private function formatPegawai($user, $employee): array
    {
        return [
            'id' => $employee?->id,
            'nama' => $employee?->name ?? $user->name,
            'nip' => $employee?->nip,
            'nik' => $employee?->nik,
            'email' => $user->email,
            'no_hp' => $employee?->phone,
            'jabatan' => $employee?->position,
            'unit' => $employee?->department,
            'status' => $employee?->status,
            'shift' => $employee?->shift,
            'username' => $user->username,
            'foto' => $employee?->photo ? Storage::url($employee->photo) : null,
        ];
    }
}
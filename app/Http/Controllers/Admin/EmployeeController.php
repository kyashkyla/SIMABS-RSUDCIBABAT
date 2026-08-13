<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar pegawai
     */
    public function index()
    {
        $employees = Employee::with('user')
            ->latest()
            ->get();

        return Inertia::render('Auth/Admin/Employee/Index', [
            'employees' => $employees,
        ]);
    }

    /**
     * Form tambah pegawai
     */
    public function create()
    {
        return Inertia::render('Auth/Admin/Employee/Create');
    }

    /**
     * Menyimpan pegawai + membuat akun login otomatis
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip',
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:50|unique:employees,nik',
            'email' => 'required|email|max:255|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|string',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status' => 'required|string',
            'start_date' => 'required|date',
            'shift' => 'required|string',
        ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | 1. Membuat username otomatis
            |--------------------------------------------------------------------------
            */

            $baseUsername = strtolower(
                preg_replace('/[^a-zA-Z0-9]/', '', $validated['name'])
            );

            $username = $baseUsername;
            $counter = 1;

            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Password awal pegawai
            |--------------------------------------------------------------------------
            */

            $initialPassword = 'pegawai123';

            /*
            |--------------------------------------------------------------------------
            | 3. Membuat akun User
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name' => $validated['name'],
                'username' => $username,
                'email' => $validated['email'],
                'password' => Hash::make($initialPassword),
                'role' => 'pegawai',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 4. Membuat data Employee
            |--------------------------------------------------------------------------
            */

            Employee::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'name' => $validated['name'],
                'nik' => $validated['nik'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'],
                'position' => $validated['position'],
                'department' => $validated['department'],
                'status' => $validated['status'],
                'start_date' => $validated['start_date'],
                'shift' => $validated['shift'],
                'face_id_registered' => false,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | 5. Kembali ke daftar pegawai
        |--------------------------------------------------------------------------
        */

        return redirect('/admin/pegawai')
            ->with('success', 'Data pegawai dan akun login berhasil dibuat.');
    }

    /**
     * Detail pegawai
     */
    public function show(Employee $employee)
    {
        return Inertia::render('Auth/Admin/Employee/Show', [
            'employee' => $employee->load('user'),
        ]);
    }

    /**
     * Form edit pegawai
     */
    public function edit(Employee $employee)
    {
        return Inertia::render('Auth/Admin/Employee/Edit', [
            'employee' => $employee,
        ]);
    }

    /**
     * Update data pegawai
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:50|unique:employees,nip,' . $employee->id,
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:50|unique:employees,nik,' . $employee->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'position' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Nonaktif',
            'start_date' => 'nullable|date',
            'shift' => 'required|in:Pagi,Siang,Malam',
        ]);

        $employee->update($validated);

        return redirect()
            ->route('admin.employees.show', $employee->id)
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /**
     * Hapus pegawai
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect('/admin/pegawai')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }

    /**
     * Halaman konfirmasi hapus
     */
    public function deleteConfirm(Employee $employee)
    {
        return Inertia::render('Auth/Admin/Employee/Delete', [
            'employee' => $employee,
        ]);
    }

    public function resetPasswordForm(Employee $employee)
    {
        return Inertia::render('Auth/Admin/Employee/ResetPassword', [
            'employee' => $employee->load('user'),
        ]);
    }

    /**
     * Reset / ganti password pegawai
     */
    public function resetPassword(Request $request, Employee $employee)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        if (!$employee->user) {
            return back()->withErrors([
                'password' => 'Pegawai belum memiliki akun login.',
            ]);
        }

        $employee->user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('admin.employees.show', $employee->id)
            ->with('success', 'Password pegawai berhasil diubah.');
    }
}
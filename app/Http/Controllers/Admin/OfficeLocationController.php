<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OfficeLocationController extends Controller
{
    /**
     * Halaman Pengaturan Admin (/admin/pengaturan).
     * Menampilkan daftar lokasi & radius presensi yang bisa diatur admin,
     * jadi pegawai tidak perlu (dan tidak bisa) mengubah radius secara manual.
     */
    public function index(): Response
    {
        $locations = OfficeLocation::latest()->get();

        return Inertia::render('Auth/Admin/Settings/Index', [
            'locations' => $locations,
        ]);
    }

    /**
     * Menyimpan lokasi presensi baru beserta radiusnya.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        OfficeLocation::create($validated);

        return redirect('/admin/pengaturan')
            ->with('success', 'Lokasi presensi baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui koordinat & radius sebuah lokasi presensi.
     */
    public function update(Request $request, OfficeLocation $location): RedirectResponse
    {
        $validated = $this->validated($request);

        $location->update($validated);

        return redirect('/admin/pengaturan')
            ->with('success', 'Radius lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus lokasi presensi.
     */
    public function destroy(OfficeLocation $location): RedirectResponse
    {
        $location->delete();

        return redirect('/admin/pengaturan')
            ->with('success', 'Lokasi presensi berhasil dihapus.');
    }

    /**
     * Aktif/nonaktifkan sebuah lokasi presensi tanpa perlu buka form edit.
     */
    public function toggleActive(OfficeLocation $location): RedirectResponse
    {
        $location->update([
            'is_active' => !$location->is_active,
        ]);

        return redirect('/admin/pengaturan')
            ->with('success', 'Status lokasi presensi berhasil diperbarui.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:5', 'max:5000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
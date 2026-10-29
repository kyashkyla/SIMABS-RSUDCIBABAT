/*
| Daftar Departemen (UNIT KERJA) & saran Jabatan (PROFESI) untuk form pegawai.
|
| Dipakai bersama oleh form Tambah & Edit Pegawai, jadi cukup ubah di sini
| kalau ada unit baru / nama unit yang berbeda dari struktur RSUD Cibabat.
|
| Prinsipnya:
|   - Departemen = DI MANA pegawai bertugas (unit/poli/instalasi).
|   - Jabatan    = APA profesinya (dokter, perawat, apoteker, dst).
|   Contoh: Jabatan "Dokter Gigi"  -> Departemen "Poli Gigi".
|           Jabatan "Dokter Spesialis Anak" -> Departemen "Poli Anak".
|
| Catatan: nilai yang sudah ada di database (IGD, Radiologi, Farmasi,
| Rawat Inap, Administrasi) sengaja dipertahankan persis sama.
*/

export const departmentGroups = [
    {
        label: 'Pelayanan Medis',
        items: [
            'IGD',
            'Rawat Inap',
            'ICU',
            'Kamar Operasi',
            'Kamar Bersalin (VK)',
        ],
    },
    {
        label: 'Rawat Jalan (Poliklinik)',
        items: [
            'Poli Umum',
            'Poli Gigi',
            'Poli Anak',
            'Poli Penyakit Dalam',
            'Poli Bedah',
            'Poli Kebidanan & Kandungan',
            'Poli Saraf',
            'Poli Mata',
            'Poli THT',
            'Poli Kulit & Kelamin',
            'Poli Jantung',
        ],
    },
    {
        label: 'Penunjang Medis',
        items: [
            'Radiologi',
            'Laboratorium',
            'Farmasi',
            'Rehabilitasi Medik',
            'Gizi',
        ],
    },
    {
        label: 'Umum & Administrasi',
        items: [
            'Administrasi',
            'Rekam Medis',
            'Keuangan',
            'Kepegawaian (SDM)',
            'IPSRS (Sarana & Prasarana)',
            'Keamanan',
            'Kebersihan',
        ],
    },
]

export const departmentList = departmentGroups.flatMap((g) => g.items)

// Saran saja (datalist): admin tetap boleh mengetik jabatan lain,
// mis. "Dokter Spesialis Mata".
export const positionSuggestions = [
    'Dokter Umum',
    'Dokter Gigi',
    'Dokter Spesialis Anak',
    'Dokter Spesialis Penyakit Dalam',
    'Dokter Spesialis Bedah',
    'Dokter Spesialis Kebidanan & Kandungan',
    'Dokter Spesialis Saraf',
    'Dokter Spesialis Mata',
    'Dokter Spesialis THT',
    'Dokter Spesialis Radiologi',
    'Perawat',
    'Perawat Gigi',
    'Bidan',
    'Apoteker',
    'Asisten Apoteker',
    'Radiografer',
    'Analis Laboratorium',
    'Fisioterapis',
    'Ahli Gizi',
    'Perekam Medis',
    'Staff Administrasi',
    'Teknisi',
    'Satpam',
    'Petugas Kebersihan',
]
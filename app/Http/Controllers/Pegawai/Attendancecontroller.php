<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\OfficeLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    /**
     * Halaman utama absensi (/pegawai/absensi).
     * Kirim data pegawai (termasuk foto referensi wajah) ke Vue.
     */
    public function index(): Response
    {
        $employee = Auth::user()->employee;

        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        return Inertia::render('Auth/pegawai/Absensi', [
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'photo_url' => $employee->photo ? Storage::url($employee->photo) : null,
            ],
            'todayAttendance' => $employee->todayAttendance(),
        ]);
    }

    /**
     * GET /pegawai/absensi/lokasi
     * Info lokasi kantor aktif, dipakai untuk menggambar peta & lingkaran radius.
     * Nilai ini SEPENUHNYA diatur oleh admin (tabel office_locations).
     */
    public function locationConfig(): JsonResponse
    {
        $office = OfficeLocation::active()->first();

        abort_if(!$office, 404, 'Lokasi kantor belum diatur oleh admin.');

        return response()->json([
            'name' => $office->name,
            'latitude' => $office->latitude,
            'longitude' => $office->longitude,
            'radius_meters' => $office->radius_meters,
        ]);
    }

    /**
     * POST /pegawai/absensi/validasi-lokasi
     * Menerima koordinat device, hitung jarak ke kantor, kembalikan valid/tidak.
     * Validasi jarak SELALU dihitung ulang di server (jangan percaya
     * hasil hitungan dari browser) supaya tidak bisa dimanipulasi.
     */
    public function validateLocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $office = $this->resolveOffice($data['latitude'], $data['longitude']);
        abort_if(!$office, 404, 'Lokasi kantor belum diatur oleh admin.');

        $distance = $office->distanceInMetersFrom($data['latitude'], $data['longitude']);

        return response()->json([
            'valid' => $distance <= $office->radius_meters,
            'distance_meters' => (int) round($distance),
            'radius_meters' => $office->radius_meters,
            'office' => [
                'name' => $office->name,
                'latitude' => $office->latitude,
                'longitude' => $office->longitude,
                'radius_meters' => $office->radius_meters,
            ],
        ]);
    }

    /**
     * POST /pegawai/absensi/simpan
     * Menyimpan absen MASUK. Ditolak kalau di luar jendela waktu absen
     * masuk yang diatur admin (config/attendance.php).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'method' => ['required', 'in:face,otp,alternative'],
            'photo' => ['nullable', 'string'],
        ]);

        $this->assertWithinWindow('check_in');

        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        // Sudah absen masuk hari ini -> tidak boleh menimpa data.
        $existing = $employee->todayAttendance();
        if ($existing && $existing->check_in_at) {
            return response()->json([
                'message' => 'Anda sudah melakukan absen masuk hari ini.',
            ], 422);
        }

        $office = $this->resolveOffice($data['latitude'], $data['longitude']);
        abort_if(!$office, 404, 'Lokasi kantor belum diatur oleh admin.');

        $distance = $office->distanceInMetersFrom($data['latitude'], $data['longitude']);

        if ($distance > $office->radius_meters) {
            return response()->json([
                'message' => 'Lokasi Anda berada di luar radius kantor. Absensi ditolak.',
            ], 422);
        }

        $photoPath = null;

        if (!empty($data['photo'])) {
            $photoPath = $this->storeCapturedPhoto($data['photo'], $employee->id, 'masuk');
        }

        $lateAfter = $this->windowTime('late_after');
        $status = now()->format('H:i:s') > $lateAfter->format('H:i:s') ? 'terlambat' : 'hadir';

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'check_in_at' => now(),
                'check_in_lat' => $data['latitude'],
                'check_in_lng' => $data['longitude'],
                'check_in_distance_meters' => (int) round($distance),
                'check_in_method' => $data['method'],
                'check_in_photo' => $photoPath,
                'status' => $status,
            ]
        );

        return response()->json([
            'message' => 'Absen masuk berhasil dicatat.',
            'attendance' => [
                'id' => $attendance->id,
                'check_in_at' => $attendance->check_in_at->format('H:i'),
                'status' => $attendance->status,
                'distance_meters' => $attendance->check_in_distance_meters,
            ],
        ]);
    }

    /**
     * POST /pegawai/absensi/pulang
     * Menyimpan absen PULANG. Ditolak kalau di luar jendela waktu absen
     * pulang, atau kalau pegawai belum absen masuk hari ini.
     */
    public function storeCheckOut(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'method' => ['required', 'in:face,otp,alternative'],
            'photo' => ['nullable', 'string'],
        ]);

        $this->assertWithinWindow('check_out');

        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        $attendance = $employee->todayAttendance();

        if (!$attendance || !$attendance->check_in_at) {
            return response()->json([
                'message' => 'Anda belum melakukan absen masuk hari ini, tidak bisa absen pulang.',
            ], 422);
        }

        if ($attendance->check_out_at) {
            return response()->json([
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
            ], 422);
        }

        $office = $this->resolveOffice($data['latitude'], $data['longitude']);
        abort_if(!$office, 404, 'Lokasi kantor belum diatur oleh admin.');

        $distance = $office->distanceInMetersFrom($data['latitude'], $data['longitude']);

        if ($distance > $office->radius_meters) {
            return response()->json([
                'message' => 'Lokasi Anda berada di luar radius kantor. Absensi ditolak.',
            ], 422);
        }

        $photoPath = null;

        if (!empty($data['photo'])) {
            $photoPath = $this->storeCapturedPhoto($data['photo'], $employee->id, 'pulang');
        }

        $attendance->update([
            'check_out_at' => now(),
            'check_out_lat' => $data['latitude'],
            'check_out_lng' => $data['longitude'],
            'check_out_distance_meters' => (int) round($distance),
            'check_out_method' => $data['method'],
            'check_out_photo' => $photoPath,
        ]);

        return response()->json([
            'message' => 'Absen pulang berhasil dicatat.',
            'attendance' => [
                'id' => $attendance->id,
                'check_out_at' => $attendance->check_out_at->format('H:i'),
                'distance_meters' => $attendance->check_out_distance_meters,
            ],
        ]);
    }

    /**
     * GET /pegawai/riwayat-absensi
     * Riwayat absensi pegawai yang login, diambil langsung dari database
     * (bukan data dummy lagi) — 100 absensi terbaru, terbaru di atas.
     */
    public function riwayat(): Response
    {
        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        $histories = $employee->attendances()
            ->orderByDesc('attendance_date')
            ->limit(100)
            ->get()
            ->map(fn (Attendance $attendance) => [
                'attendance_date' => $attendance->attendance_date->toDateString(),
                'check_in_at' => $attendance->check_in_at?->format('H:i'),
                'check_out_at' => $attendance->check_out_at?->format('H:i'),
                'method' => $attendance->check_in_method,
                'status' => $attendance->status,
            ])
            ->values();

        return Inertia::render('Auth/pegawai/RiwayatAbsensi', [
            'histories' => $histories,
        ]);
    }

    /**
     * GET /pegawai/laporan
     * Laporan bulanan pegawai. Defaultnya bulan & tahun berjalan, tapi bisa
     * dinavigasi ke bulan lain lewat query string ?bulan=&tahun=. Ringkasan
     * SELALU dihitung ulang per bulan yang dipilih, jadi otomatis "reset"
     * setiap bulan berganti — bulan baru mulai dari 0 lagi tanpa perlu
     * job/cron apapun.
     */
    public function laporan(Request $request): Response
    {
        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        $today = Carbon::today();

        $tahun = (int) $request->query('tahun', $today->year);
        $bulan = (int) $request->query('bulan', $today->month);

        // Jaga-jaga input query aneh (mis. bulan=15) supaya tidak error.
        $periode = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $tahun = $periode->year;
        $bulan = $periode->month;

        $awalBulan = $periode->copy()->startOfMonth();
        $akhirBulan = $periode->copy()->endOfMonth();

        $attendances = $employee->attendances()
            ->whereBetween('attendance_date', [
                $awalBulan->toDateString(),
                $akhirBulan->toDateString(),
            ])
            ->orderByDesc('attendance_date')
            ->get();

        $hadir = $attendances->where('status', 'hadir')->count();
        $terlambat = $attendances->where('status', 'terlambat')->count();
        $izin = $attendances->where('status', 'alternatif')->count();

        // Untuk bulan yang sedang berjalan, hari yang dihitung cuma sampai
        // hari ini. Untuk bulan yang sudah lewat, dihitung sampai akhir
        // bulan itu. Bulan yang belum terjadi otomatis 0 (belum ada apa-apa).
        if ($periode->isSameMonth($today) && $periode->year === $today->year) {
            $hariBerjalan = $today->day;
        } elseif ($periode->lessThan($today->copy()->startOfMonth())) {
            $hariBerjalan = $akhirBulan->day;
        } else {
            $hariBerjalan = 0;
        }

        $tidakHadir = max($hariBerjalan - ($hadir + $terlambat + $izin), 0);

        $bulanDepan = $periode->copy()->addMonthNoOverflow();
        $bisaKeBulanDepan = $bulanDepan->lessThanOrEqualTo($today->copy()->startOfMonth());

        return Inertia::render('Auth/pegawai/Laporan', [
            'rows' => $attendances->map(fn (Attendance $attendance) => [
                'attendance_date' => $attendance->attendance_date->toDateString(),
                'check_in_at' => $attendance->check_in_at?->format('H:i'),
                'check_out_at' => $attendance->check_out_at?->format('H:i'),
                'method' => $attendance->check_in_method,
                'status' => $attendance->status,
            ])->values(),
            'summary' => [
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'tidak_hadir' => $tidakHadir,
            ],
            'periode' => [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'bulan_sebelumnya' => $periode->copy()->subMonthNoOverflow()->format('Y-m'),
                'bulan_berikutnya' => $bulanDepan->format('Y-m'),
                'bisa_ke_bulan_berikutnya' => $bisaKeBulanDepan,
            ],
        ]);
    }

    /**
     * Cari lokasi kantor aktif yang paling relevan untuk sebuah titik GPS.
     * RSUD Cibabat bisa punya lebih dari satu titik/gerbang (diatur admin
     * di /admin/pengaturan), masing-masing dengan radiusnya sendiri.
     * Pegawai dianggap valid kalau posisinya masuk radius SALAH SATU
     * titik aktif — bukan cuma titik pertama di database.
     */
    private function resolveOffice(float $lat, float $lng): ?OfficeLocation
    {
        $offices = OfficeLocation::active()->get();

        if ($offices->isEmpty()) {
            return null;
        }

        // Kalau ada titik yang benar-benar mencakup posisi pegawai, pakai itu.
        $withinRadius = $offices->first(
            fn (OfficeLocation $office) => $office->isWithinRadius($lat, $lng)
        );

        if ($withinRadius) {
            return $withinRadius;
        }

        // Tidak ada yang mencakup -> pakai titik terdekat, supaya info jarak
        // & peta di halaman absensi tetap masuk akal (bukan lokasi acak).
        return $offices->sortBy(
            fn (OfficeLocation $office) => $office->distanceInMetersFrom($lat, $lng)
        )->first();
    }

    /**
     * Ambil jam mulai/selesai jendela absensi ('check_in' atau 'check_out')
     * dari config/attendance.php, sebagai objek Carbon di HARI INI.
     */
    private function windowTime(string $key): Carbon
    {
        if ($key === 'late_after') {
            return Carbon::parse(config('attendance.late_after'));
        }

        return Carbon::parse(config("attendance.{$key}.start"));
    }

    /**
     * Tolak request kalau waktu sekarang di luar jendela absensi
     * ('check_in' atau 'check_out'). Ini dicek SELALU di server —
     * jangan cuma andalkan validasi di frontend/Vue, supaya jam device
     * pegawai yang dimanipulasi tidak bisa menembus batas.
     */
    private function assertWithinWindow(string $type): void
    {
        $start = Carbon::parse(config("attendance.{$type}.start"));
        $end = Carbon::parse(config("attendance.{$type}.end"));
        $now = now();

        if ($now->lt($start) || $now->gt($end)) {
            $label = $type === 'check_in' ? 'absen masuk' : 'absen pulang';

            abort(response()->json([
                'message' => "Anda tidak dapat melakukan {$label} karena sudah bukan jamnya.",
                'window' => [
                    'start' => $start->format('H:i'),
                    'end' => $end->format('H:i'),
                ],
            ], 422));
        }
    }

    /**
     * Simpan foto hasil capture kamera (data URL base64) ke storage publik.
     */
    private function storeCapturedPhoto(string $base64, int $employeeId, string $type = 'masuk'): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            abort(422, 'Format foto tidak valid.');
        }

        $extension = $matches[1] === 'jpeg' ? 'jpg' : $matches[1];
        $content = base64_decode(substr($base64, strpos($base64, ',') + 1));

        $filename = 'attendance-photos/'
            . $employeeId . '-' . $type . '-' . now()->format('Ymd-His') . '-' . Str::random(6)
            . '.' . $extension;

        Storage::disk('public')->put($filename, $content);

        return $filename;
    }
}
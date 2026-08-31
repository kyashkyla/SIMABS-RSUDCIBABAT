<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\OfficeLocation;
use App\Support\ShiftSchedule;
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

        $activeRequest = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('approval_type', 'permanen')
                  ->orWhere(function ($q2) {
                      $q2->where('approval_type', 'sementara')
                         ->whereDate('decided_at', now()->toDateString());
                  });
            })
            ->latest('decided_at')
            ->first();
            
        $pendingRequest = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->exists();

        $approvalStatus = 'none';
        if ($pendingRequest) {
            $approvalStatus = 'pending';
        } elseif ($activeRequest) {
            $approvalStatus = $activeRequest->approval_type == 'permanen' ? 'permanent' : 'temporary';
        }

        return Inertia::render('Auth/pegawai/Absensi', [
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'photo_url' => $employee->photo ? Storage::url($employee->photo) : null,
            ],
            'todayAttendance' => $employee->todayAttendance(),
            'approvalStatus' => $approvalStatus,
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
     * masuk shift pegawai yang bersangkutan (Pagi/Siang/Malam), yang
     * jamnya dihitung dari config/attendance.php.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'method' => ['required', 'in:face,otp,alternative'],
            'photo' => ['nullable', 'string'],
            'otp_code' => ['nullable', 'string', 'size:6'],
        ]);

        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        if ($data['method'] === 'otp') {
            abort_if(empty($data['otp_code']), 422, 'Kode OTP wajib diisi.');
            
            $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_' . $employee->id);
            if (!$cachedOtp || $cachedOtp !== strtoupper($data['otp_code'])) {
                return response()->json([
                    'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
                ], 422);
            }
            
            $activeRequest = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('approval_type', 'permanen')
                      ->orWhere(function ($q2) {
                          $q2->where('approval_type', 'sementara')
                             ->whereDate('decided_at', now()->toDateString());
                      });
                })
                ->exists();
                
            if (!$activeRequest) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin absensi OTP aktif.',
                ], 422);
            }
            
            // Hapus OTP setelah berhasil digunakan
            \Illuminate\Support\Facades\Cache::forget('otp_' . $employee->id);
        }

        // Jendela absen masuk mengikuti shift pegawai ini, bukan jam kantor
        // umum -> shift Pagi/Siang/Malam masing-masing punya jamnya sendiri.
        $this->assertWithinCheckInWindow($employee);

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

        $lateAfter = ShiftSchedule::lateAfter($employee->shift, now()->toDateString());
        $status = now()->gt($lateAfter) ? 'terlambat' : 'hadir';

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
     * pulang shift pegawai, atau kalau pegawai belum absen masuk hari ini.
     *
     * Jendela absen pulang dihitung dari TANGGAL ABSEN MASUK pegawai
     * (bukan tanggal hari ini), supaya shift Malam (23:00 - 07:00) yang
     * absen pulangnya baru terjadi dini hari besok tetap dihitung benar.
     */
    public function storeCheckOut(Request $request): JsonResponse
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'method' => ['required', 'in:face,otp,alternative'],
            'photo' => ['nullable', 'string'],
            'otp_code' => ['nullable', 'string', 'size:6'],
        ]);

        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        if ($data['method'] === 'otp') {
            abort_if(empty($data['otp_code']), 422, 'Kode OTP wajib diisi.');
            
            $cachedOtp = \Illuminate\Support\Facades\Cache::get('otp_' . $employee->id);
            if (!$cachedOtp || $cachedOtp !== strtoupper($data['otp_code'])) {
                return response()->json([
                    'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
                ], 422);
            }
            
            $activeRequest = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('approval_type', 'permanen')
                      ->orWhere(function ($q2) {
                          $q2->where('approval_type', 'sementara')
                             ->whereDate('decided_at', now()->toDateString());
                      });
                })
                ->exists();
                
            if (!$activeRequest) {
                return response()->json([
                    'message' => 'Anda tidak memiliki izin absensi OTP aktif.',
                ], 422);
            }
            
            \Illuminate\Support\Facades\Cache::forget('otp_' . $employee->id);
        }

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

        $this->assertWithinCheckOutWindow($employee, $attendance->attendance_date->toDateString());

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
     * Tolak request absen MASUK kalau waktu sekarang di luar jendela absen
     * masuk shift pegawai ini. Ini dicek SELALU di server — jangan cuma
     * andalkan validasi di frontend/Vue, supaya jam device pegawai yang
     * dimanipulasi tidak bisa menembus batas.
     */
    private function assertWithinCheckInWindow(Employee $employee): void
    {
        $window = ShiftSchedule::checkInWindow($employee->shift, now()->toDateString());

        $this->assertWithinWindow($window, 'absen masuk');
    }

    /**
     * Tolak request absen PULANG kalau waktu sekarang di luar jendela absen
     * pulang shift pegawai ini. $attendanceDate = tanggal absen masuknya,
     * supaya shift yang melewati tengah malam (Malam) tetap dihitung benar.
     */
    private function assertWithinCheckOutWindow(Employee $employee, string $attendanceDate): void
    {
        $window = ShiftSchedule::checkOutWindow($employee->shift, $attendanceDate);

        $this->assertWithinWindow($window, 'absen pulang');
    }

    /**
     * @param  array{start: Carbon, end: Carbon}  $window
     */
    private function assertWithinWindow(array $window, string $label): void
    {
        $now = now();

        if ($now->lt($window['start']) || $now->gt($window['end'])) {
            abort(response()->json([
                'message' => "Anda tidak dapat melakukan {$label} karena sudah bukan jamnya.",
                'window' => [
                    'start' => $window['start']->format('H:i'),
                    'end' => $window['end']->format('H:i'),
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
    public function requestOtp(Request $request): JsonResponse
    {
        $employee = Auth::user()->employee;
        abort_if(!$employee, 404, 'Data pegawai tidak ditemukan.');

        $activeRequest = \App\Models\AttendanceRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('approval_type', 'permanen')
                  ->orWhere(function ($q2) {
                      $q2->where('approval_type', 'sementara')
                         ->whereDate('decided_at', now()->toDateString());
                  });
            })
            ->exists();

        if (!$activeRequest) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin absensi OTP aktif.',
            ], 422);
        }

        $otpCode = strtoupper(\Illuminate\Support\Str::random(6));
        \Illuminate\Support\Facades\Cache::put('otp_' . $employee->id, $otpCode, now()->addMinutes(3));

        $request->user()->notify(new \App\Notifications\OtpNotification($otpCode));

        return response()->json([
            'message' => 'Kode OTP berhasil dikirim ke notifikasi Anda.',
        ]);
    }
}
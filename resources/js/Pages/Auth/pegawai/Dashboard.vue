<script setup>
import PegawaiLayout from './PegawaiLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed, reactive, ref, onMounted, onUnmounted } from 'vue'

import {
    CheckCircleIcon,
    ClockIcon,
    CalendarDaysIcon,
    MapPinIcon,
    ArrowPathIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    user: Object,
    employee: Object,
    todayAttendance: {
        type: Object,
        default: null,
    },
    monthlySummary: {
        type: Object,
        default: () => ({
            hadir: 0,
            terlambat: 0,
            izin: 0,
            tidak_hadir: 0,
            hari_berjalan: 0,
            tingkat_kehadiran: 0,
        }),
    },
    recentActivities: {
        type: Array,
        default: () => [],
    },
})

// =====================================================
// STATUS ABSENSI HARI INI (dari database, bukan manual)
// =====================================================

const statusHariIniLabel = computed(() => {
    if (!props.todayAttendance) return 'Belum Absen'
    if (props.todayAttendance.status === 'terlambat') return 'Terlambat'
    if (props.todayAttendance.status === 'alternatif') return 'Izin'
    return 'Hadir'
})

const jamMasukLabel = computed(() => props.todayAttendance?.check_in_at ?? '—')

const jamMasukKeterangan = computed(() =>
    props.todayAttendance
        ? 'Terhitung dari absensi masuk'
        : 'Anda belum melakukan absensi hari ini'
)

const methodLabelMap = {
    face: 'Face Recognition',
    otp: 'OTP Code',
    alternative: 'Absensi Alternatif',
}

const statusBadge = {
    hadir: { text: 'Hadir', class: 'hadir-badge' },
    terlambat: { text: 'Terlambat', class: 'terlambat-badge' },
    alternatif: { text: 'Izin', class: 'izin-badge' },
}

// =====================================================
// RINGKASAN BULAN (persentase progress bar, aman dari bagi 0)
// =====================================================

const summaryPercent = computed(() => {
    const total = props.monthlySummary.hari_berjalan || 1
    const s = props.monthlySummary

    return {
        hadir: Math.round((s.hadir / total) * 100),
        terlambat: Math.round((s.terlambat / total) * 100),
        izin: Math.round((s.izin / total) * 100),
        tidak_hadir: Math.round((s.tidak_hadir / total) * 100),
    }
})

// =====================================================
// AKTIVITAS TERKINI (5 absensi terakhir dari database)
// =====================================================

const aktivitas = computed(() =>
    props.recentActivities.map((item) => {
        const tanggal = new Date(`${item.attendance_date}T00:00:00`)
        const hariIni = new Date()
        const kemarin = new Date()
        kemarin.setDate(hariIni.getDate() - 1)

        let labelWaktu
        if (tanggal.toDateString() === hariIni.toDateString()) {
            labelWaktu = 'Hari ini'
        } else if (tanggal.toDateString() === kemarin.toDateString()) {
            labelWaktu = 'Kemarin'
        } else {
            labelWaktu = tanggal.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
            })
        }

        return {
            ...item,
            labelWaktu,
            methodLabel: methodLabelMap[item.method] ?? (item.method ?? '—'),
            badge: statusBadge[item.status] ?? { text: item.status, class: 'hadir-badge' },
            iconClass: item.status === 'terlambat' ? 'warning' : 'success',
        }
    })
)

// Inisial buat avatar placeholder, contoh "Ahmad Fauzi" -> "AF"
const initials = computed(() => {
    const name = props.employee?.name || props.user?.name || ''
    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map(word => word[0])
        .join('')
        .toUpperCase()
})

// =====================================================
// JAM & TANGGAL BERJALAN (otomatis, tidak manual lagi)
// =====================================================

const now = ref(new Date())
let clockInterval = null

// Format: "13.32"
// Shift pegawai (Pagi / Siang / Malam) + rentang jamnya, dari data pegawai.
const shiftLabel = computed(() => props.employee?.shift || null)

const shiftRange = computed(() => {
    const d = props.employee?.shift_details
    if (!d?.start || !d?.end) return ''
    return `${d.start} - ${d.end}${d.overnight ? ' (+1 hari)' : ''}`
})

const jamSekarang = computed(() => {
    return now.value
        .toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        .replace(':', '.')
})

// Format: "Kamis, 27 Agustus 2026"
const tanggalSekarang = computed(() => {
    return now.value.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
})

// Format: "Agustus 2026" (buat judul "Ringkasan Bulan")
const bulanTahunSekarang = computed(() => {
    return now.value.toLocaleDateString('id-ID', {
        month: 'long',
        year: 'numeric',
    })
})

// =====================================================
// DETEKSI LOKASI (GPS asli perangkat -> validasi radius di server)
// =====================================================
//
// Sebelumnya kartu lokasi di dashboard ini cuma teks statis. Sekarang:
// 1. Ambil koordinat GPS asli dari browser.
// 2. Kirim ke endpoint validasi yang SAMA dengan halaman absensi
//    (pegawai.absensi.validasi-lokasi), jadi hasilnya selalu konsisten.
// 3. Alamat diambil dari reverse geocoding (server), jadi berubah otomatis
//    mengikuti posisi pegawai saat dashboard dibuka.
// Peta sengaja tidak ditampilkan di dashboard.

const lokasi = reactive({
    status: 'checking', // checking | valid | invalid | error
    address: '',
    distance: null,
    message: '',
})

let sedangMendeteksi = false
let terakhirDicek = 0

const formatJarak = (meter) => {
    if (meter === null || meter === undefined) return ''
    if (meter >= 1000) return `${(meter / 1000).toFixed(1).replace('.', ',')} km`
    return `${meter} m`
}

const ambilPosisi = () => {
    return new Promise((resolve, reject) => {
        if (!window.isSecureContext) {
            reject(new Error('GPS hanya bisa dipakai lewat HTTPS (atau localhost).'))
            return
        }

        if (!('geolocation' in navigator)) {
            reject(new Error('Perangkat/browser ini tidak mendukung GPS.'))
            return
        }

        navigator.geolocation.getCurrentPosition(
            (position) => resolve({
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            }),
            (err) => {
                if (err.code === err.PERMISSION_DENIED) {
                    reject(new Error('Izin lokasi ditolak. Aktifkan izin lokasi untuk browser ini.'))
                } else if (err.code === err.POSITION_UNAVAILABLE) {
                    reject(new Error('Posisi GPS tidak dapat ditentukan.'))
                } else if (err.code === err.TIMEOUT) {
                    reject(new Error('Waktu mendeteksi lokasi habis. Coba lagi.'))
                } else {
                    reject(new Error('Gagal mendeteksi lokasi.'))
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0,
            }
        )
    })
}

const deteksiLokasi = async () => {
    if (sedangMendeteksi) return

    sedangMendeteksi = true
    lokasi.status = 'checking'
    lokasi.message = ''

    try {
        const { lat, lng } = await ambilPosisi()

        // Dijalankan paralel. allSettled supaya kalau alamat gagal dimuat,
        // status "di area / di luar area" tetap tampil.
        const [validasi, alamat] = await Promise.allSettled([
            window.axios.post(route('pegawai.absensi.validasi-lokasi'), {
                latitude: lat,
                longitude: lng,
            }),
            window.axios.post(route('pegawai.dashboard.alamat'), {
                latitude: lat,
                longitude: lng,
            }),
        ])

        lokasi.address =
            alamat.status === 'fulfilled' && alamat.value.data?.address
                ? alamat.value.data.address
                : `Koordinat ${lat.toFixed(5)}, ${lng.toFixed(5)}`

        if (validasi.status === 'fulfilled') {
            const hasil = validasi.value.data

            lokasi.distance = hasil.distance_meters
            lokasi.status = hasil.valid ? 'valid' : 'invalid'
        } else {
            lokasi.status = 'error'
            lokasi.message = validasi.reason?.response?.status === 404
                ? 'Lokasi kantor belum diatur oleh admin.'
                : 'Gagal memvalidasi lokasi. Coba lagi.'
        }
    } catch (err) {
        lokasi.status = 'error'
        lokasi.message = err.message || 'Gagal mendeteksi lokasi.'
    } finally {
        terakhirDicek = Date.now()
        sedangMendeteksi = false
    }
}

// Teks yang ditampilkan di kartu lokasi, tergantung statusnya.
const lokasiTampil = computed(() => {
    if (lokasi.status === 'checking') {
        return {
            title: 'Mendeteksi lokasi...',
            sub: 'Izinkan akses lokasi pada browser Anda',
            pill: 'Memeriksa',
            pillClass: 'checking',
        }
    }

    if (lokasi.status === 'error') {
        return {
            title: lokasi.message,
            sub: '',
            pill: 'Tidak Terdeteksi',
            pillClass: 'error',
        }
    }

    // Yang ditampilkan selalu lokasi pegawai saat ini (alamat dari GPS),
    // bukan nama titik kantor di pengaturan.
    if (lokasi.status === 'valid') {
        return {
            title: lokasi.address,
            sub: 'Dalam radius lokasi absensi',
            pill: 'Di Area RS',
            pillClass: 'in',
        }
    }

    return {
        title: lokasi.address,
        sub: `Berjarak ${formatJarak(lokasi.distance)} dari lokasi absensi terdekat`,
        pill: 'Di Luar Area RS',
        pillClass: 'out',
    }
})

// Cek ulang otomatis kalau tab dibuka kembali (mis. pegawai pindah tempat).
const saatTabKembali = () => {
    if (document.visibilityState === 'visible' && Date.now() - terakhirDicek > 30000) {
        deteksiLokasi()
    }
}

onMounted(() => {
    // Update tiap detik supaya jam berjalan real-time, tanggal otomatis
    // ikut berganti begitu lewat tengah malam
    clockInterval = setInterval(() => {
        now.value = new Date()
    }, 1000)

    // Lokasi & alamat dideteksi setiap dashboard dibuka
    deteksiLokasi()
    document.addEventListener('visibilitychange', saatTabKembali)
})

onUnmounted(() => {
    if (clockInterval) {
        clearInterval(clockInterval)
    }

    document.removeEventListener('visibilitychange', saatTabKembali)
})
</script>

<template>
    <PegawaiLayout>
        <div class="dashboard-page">

            <!-- ============================= -->
            <!-- HEADER DASHBOARD -->
            <!-- ============================= -->
            <div class="dashboard-header">
                <div>
                    <h1>Selamat Datang, {{ employee?.name || user?.name }} 👋</h1>
                    <p>
                        Semangat bekerja untuk kesehatan masyarakat Cimahi
                    </p>

                    <!-- Badge shift pegawai -->
                    <div
                        v-if="shiftLabel"
                        class="shift-badge"
                        :class="`shift-${shiftLabel.toLowerCase()}`"
                    >
                        <ClockIcon />
                        <strong>Shift {{ shiftLabel }}</strong>
                        <span>{{ shiftRange }}</span>
                    </div>
                </div>

                <div class="current-time">
                    <strong>{{ jamSekarang }}</strong>
                    <span>{{ tanggalSekarang }}</span>
                </div>
            </div>


            <!-- ============================= -->
            <!-- LOCATION -->
            <!-- ============================= -->
            <div class="location-card" :class="`is-${lokasi.status}`">

                <div class="location-icon">
                    <ExclamationTriangleIcon v-if="lokasi.status === 'error'" />
                    <MapPinIcon v-else />
                </div>

                <div class="location-info">
                    <span>Lokasi terdeteksi</span>

                    <strong>{{ lokasiTampil.title }}</strong>

                    <small v-if="lokasiTampil.sub">{{ lokasiTampil.sub }}</small>
                </div>

                <button
                    type="button"
                    class="location-refresh"
                    title="Perbarui lokasi"
                    :disabled="lokasi.status === 'checking'"
                    @click="deteksiLokasi"
                >
                    <ArrowPathIcon :class="{ spin: lokasi.status === 'checking' }" />
                </button>

                <div class="location-status" :class="lokasiTampil.pillClass">
                    {{ lokasiTampil.pill }}
                </div>

            </div>


            <!-- ============================= -->
            <!-- STAT CARDS -->
            <!-- ============================= -->
            <div class="stats-grid">

                <!-- Status -->
                <div class="stat-card status-card">

                    <div class="stat-card-header">
                        <span>Status Hari Ini</span>

                        <div class="stat-icon white-icon">
                            <CheckCircleIcon />
                        </div>
                    </div>

                    <div class="stat-main">
                        {{ statusHariIniLabel }}
                    </div>

                    <p>{{ jamMasukKeterangan }}</p>

                </div>


                <!-- Jam Masuk -->
                <div class="stat-card">

                    <div class="stat-card-header">
                        <span>Jam Masuk ({{ employee?.shift_details?.start ?? '07:00' }})</span>

                        <div class="stat-icon">
                            <ClockIcon />
                        </div>
                    </div>

                    <div class="stat-value" :class="{ empty: !todayAttendance }">
                        {{ jamMasukLabel }}
                    </div>

                    <p>{{ todayAttendance ? 'Absensi masuk tercatat' : 'Batas toleransi: ' + (employee?.shift_details?.late_after ?? '-') }}</p>

                </div>


                <!-- Jam Keluar -->
                <div class="stat-card">

                    <div class="stat-card-header">
                        <span>Jam Keluar ({{ employee?.shift_details?.end ?? '15:00' }})</span>

                        <div class="stat-icon">
                            <ClockIcon />
                        </div>
                    </div>

                    <div class="stat-value" :class="{ empty: !todayAttendance?.check_out_at }">
                        {{ todayAttendance?.check_out_at ?? '—' }}
                    </div>

                    <p>{{ todayAttendance?.check_out_at ? 'Absensi keluar tercatat' : 'Belum absen keluar' }}</p>

                </div>


                <!-- Kehadiran -->
                <div class="stat-card">

                    <div class="stat-card-header">
                        <span>Kehadiran Bulan Ini</span>

                        <div class="stat-icon">
                            <CalendarDaysIcon />
                        </div>
                    </div>

                    <div class="stat-value">
                        {{ monthlySummary.hadir + monthlySummary.terlambat }}/{{ monthlySummary.hari_berjalan }}
                    </div>

                    <p>{{ monthlySummary.tingkat_kehadiran }}% tingkat kehadiran</p>

                </div>

            </div>


            <!-- ============================= -->
            <!-- CONTENT GRID -->
            <!-- ============================= -->
            <div class="content-grid">

                <!-- ========================= -->
                <!-- LEFT COLUMN -->
                <!-- ========================= -->
                <div class="left-column">

                    <!-- AKSI CEPAT -->
                    <div class="card">

                        <h3>Aksi Cepat</h3>

                        <!-- Mulai Absensi -->
                        <Link
                            v-if="!(todayAttendance?.check_in_at && todayAttendance?.check_out_at)"
                            :href="route('pegawai.absensi')"
                            class="attendance-button"
                        >
                            <ClockIcon />
                            <span>{{ todayAttendance?.check_in_at ? 'Absen Pulang' : 'Mulai Absensi' }}</span>
                        </Link>

                        <div v-else class="attendance-done">
                            <CheckCircleIcon />
                            <span>Anda sudah menyelesaikan absensi hari ini.</span>
                        </div>


                        <!-- Tombol lainnya -->
                        <div class="quick-buttons">

                            <Link
                                :href="route('pegawai.riwayat')"
                                class="quick-button"
                            >
                                Riwayat
                            </Link>

                            <Link
                                :href="route('pegawai.laporan')"
                                class="quick-button"
                            >
                                Laporan
                            </Link>

                        </div>

                    </div>


                    <!-- RINGKASAN BULAN -->
                    <div class="card summary-card">

                        <h3>Ringkasan {{ bulanTahunSekarang }}</h3>

                        <!-- Hadir -->
                        <div class="summary-item">

                            <div class="summary-label">
                                <span>Hadir</span>
                                <strong>{{ monthlySummary.hadir }} hari</strong>
                            </div>

                            <div class="progress">
                                <div
                                    class="progress-fill hadir"
                                    :style="{ width: summaryPercent.hadir + '%' }"
                                ></div>
                            </div>

                        </div>


                        <!-- Terlambat -->
                        <div class="summary-item">

                            <div class="summary-label">
                                <span>Terlambat</span>
                                <strong>{{ monthlySummary.terlambat }} hari</strong>
                            </div>

                            <div class="progress">
                                <div
                                    class="progress-fill terlambat"
                                    :style="{ width: summaryPercent.terlambat + '%' }"
                                ></div>
                            </div>

                        </div>


                        <!-- Izin -->
                        <div class="summary-item">

                            <div class="summary-label">
                                <span>Izin</span>
                                <strong>{{ monthlySummary.izin }} hari</strong>
                            </div>

                            <div class="progress">
                                <div
                                    class="progress-fill izin"
                                    :style="{ width: summaryPercent.izin + '%' }"
                                ></div>
                            </div>

                        </div>


                        <!-- Tidak Hadir -->
                        <div class="summary-item">

                            <div class="summary-label">
                                <span>Tidak Hadir</span>
                                <strong>{{ monthlySummary.tidak_hadir }} hari</strong>
                            </div>

                            <div class="progress">
                                <div
                                    class="progress-fill tidak-hadir"
                                    :style="{ width: summaryPercent.tidak_hadir + '%' }"
                                ></div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- ========================= -->
                <!-- RIGHT COLUMN -->
                <!-- ========================= -->
                <div class="right-column">

                    <!-- AKTIVITAS TERKINI -->
                    <div class="card activity-card">

                        <div class="card-title-row">

                            <h3>Aktivitas Terkini</h3>

                            <Link
                                :href="route('pegawai.riwayat')"
                                class="see-all"
                            >
                                Lihat Semua
                            </Link>

                        </div>


                        <!-- Aktivitas absensi terakhir (dari database) -->
                        <div
                            v-for="item in aktivitas"
                            :key="item.attendance_date"
                            class="activity-item"
                        >

                            <div class="activity-icon" :class="item.iconClass">
                                <ClockIcon />
                            </div>

                            <div class="activity-info">

                                <strong>
                                    Absensi Masuk
                                </strong>

                                <span>
                                    {{ item.labelWaktu }} · {{ item.methodLabel }}
                                </span>

                            </div>

                            <div class="activity-time">

                                <strong>
                                    {{ item.check_in_at ?? '—' }}
                                </strong>

                                <span class="badge" :class="item.badge.class">
                                    {{ item.badge.text }}
                                </span>

                            </div>

                        </div>

                        <p v-if="aktivitas.length === 0" class="empty-activity">
                            Belum ada aktivitas absensi.
                        </p>

                    </div>


                    <!-- PROFILE CARD -->
                    <div class="profile-card">

                        <div class="profile-photo">
                            <img
                                v-if="employee?.photo_url"
                                :src="employee.photo_url"
                                alt="Foto profil"
                                class="profile-photo-img"
                            />

                            <div v-else class="profile-placeholder">
                                {{ initials }}
                            </div>
                        </div>

                        <div class="profile-info">

                            <strong>
                                {{ employee?.name || user?.name }}
                            </strong>

                            <span>
                                NIP: {{ employee?.nip || '-' }}
                            </span>

                            <div class="profile-tags">

                                <span v-if="employee?.department">
                                    {{ employee.department }}
                                </span>

                                <span v-if="employee?.position">
                                    {{ employee.position }}
                                </span>

                            </div>

                        </div>

                        <div class="verification">

                            <strong>
                                <span
                                    class="verification-dot"
                                    :class="{ off: !employee?.face_verified }"
                                ></span>
                                {{ employee?.face_verified ? 'Wajah Terverifikasi' : 'Wajah Belum Terdaftar' }}
                            </strong>

                            <span v-if="!employee?.face_verified">
                                Hubungi admin untuk mendaftarkan foto wajah
                            </span>
                            <span v-else-if="employee?.face_updated_label">
                                Diperbarui {{ employee.face_updated_label }}
                            </span>
                            <span v-else>
                                Foto wajah terdaftar
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </PegawaiLayout>
</template>


<style scoped>

/* ========================================= */
/* GENERAL */
/* ========================================= */

.dashboard-page {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;

    padding: 38px 40px 50px;

    box-sizing: border-box;
}


/* ========================================= */
/* HEADER */
/* ========================================= */

.dashboard-header {
    display: flex;

    align-items: flex-start;
    justify-content: space-between;

    margin-bottom: 30px;
}

.dashboard-header h1 {
    margin: 0;

    font-size: 27px;
    font-weight: 700;

    color: #19343d;
}

.dashboard-header p {
    margin: 7px 0 0;

    color: #74868b;

    font-size: 14px;
}

/* Badge shift di bawah sapaan */
.shift-badge {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;

    margin-top: 12px;
    padding: 6px 12px;

    border-radius: 999px;

    background: #e8f8f5;
    color: #12877a;

    font-size: 13px;
}

.shift-badge svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.shift-badge strong {
    font-weight: 700;
}

.shift-badge span {
    font-weight: 500;
    opacity: .85;
}

.shift-pagi {
    background: #fff6dc;
    color: #a86f00;
}

.shift-siang {
    background: #e8f8f5;
    color: #12877a;
}

.shift-malam {
    background: #e9edff;
    color: #3b4fb8;
}

.current-time {
    display: flex;

    flex-direction: column;

    align-items: flex-end;
}

.current-time strong {
    color: #18ae9e;

    font-size: 32px;

    line-height: 1;
}

.current-time span {
    margin-top: 6px;

    color: #7b898e;

    font-size: 13px;
}


/* ========================================= */
/* LOCATION */
/* ========================================= */

.location-card {
    display: flex;

    align-items: center;

    width: 100%;

    padding: 16px 20px;

    box-sizing: border-box;

    border: 1px solid #dcefeb;

    border-radius: 15px;

    background: #effaf8;

    margin-bottom: 25px;
}

.location-icon {
    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    width: 46px;
    height: 46px;

    margin-right: 14px;

    border-radius: 12px;

    background: white;

    color: #16b29f;
}

.location-icon svg {
    width: 23px;
}

.location-info {
    display: flex;

    flex-direction: column;

    gap: 4px;
}

.location-info span {
    color: #71888b;

    font-size: 12px;
}

.location-info strong {
    color: #29434a;

    font-size: 14px;
}

.location-info {
    min-width: 0;
}

.location-info small {
    color: #71888b;

    font-size: 12px;
}

.location-refresh {
    display: flex;

    align-items: center;
    justify-content: center;

    width: 34px;
    height: 34px;

    margin-left: auto;
    margin-right: 12px;

    flex-shrink: 0;

    border: none;
    border-radius: 10px;

    background: white;

    color: #16b29f;

    cursor: pointer;
}

.location-refresh:disabled {
    cursor: default;
    opacity: 0.6;
}

.location-refresh svg {
    width: 18px;
    height: 18px;
}

.location-status {
    flex-shrink: 0;

    padding: 7px 14px;

    border-radius: 20px;

    background: #d9f8e5;

    color: #2fa968;

    font-size: 12px;

    font-weight: 600;

    white-space: nowrap;
}

.location-status.checking {
    background: #e8eef0;
    color: #6f8387;
}

.location-status.out {
    background: #fde8e6;
    color: #d9483b;
}

.location-status.error {
    background: #fff1d6;
    color: #d98a0b;
}

.location-card.is-invalid {
    border-color: #f6dcd7;
    background: #fff7f5;
}

.location-card.is-invalid .location-icon {
    color: #d9483b;
}

.location-card.is-error {
    border-color: #f5e6c4;
    background: #fffaf0;
}

.location-card.is-error .location-icon {
    color: #d98a0b;
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}


/* ========================================= */
/* STATS */
/* ========================================= */

.stats-grid {
    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 25px;
}

.stat-card {
    min-height: 142px;

    padding: 20px;

    box-sizing: border-box;

    border: 1px solid #e0eceb;

    border-radius: 17px;

    background: white;

    box-shadow:
        0 3px 12px rgba(36, 108, 101, 0.04);
}

.status-card {
    background:
        linear-gradient(
            135deg,
            #18b6a2,
            #1fc76c
        );

    border: none;

    color: white;
}

.stat-card-header {
    display: flex;

    align-items: center;
    justify-content: space-between;
}

.stat-card-header span {
    font-size: 13px;
    color: #6f8387;
}

.stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    border-radius: 11px;
    background: #e8f7f4;
    color: #16b29f;
}

.stat-icon svg {
    width: 20px;
    height: 20px;
}

.white-icon {
    background: rgba(255, 255, 255, 0.22);
    color: white;
}

.status-card .stat-card-header span,
.stat-card.status-card p {
    /* .stat-card.status-card p sengaja lebih spesifik dari `.stat-card p`
       (abu-abu) supaya teks di kartu hijau ini tidak ketimpa jadi
       abu-abu dan tak terbaca. */
    color: rgba(255, 255, 255, 0.95);
}

.stat-main {
    margin-top: 18px;

    font-size: 28px;
    font-weight: 700;
}

.stat-value {
    margin-top: 18px;

    font-size: 28px;
    font-weight: 700;

    color: #19343d;
}

.stat-value.empty {
    color: #b5c2c5;
}

.stat-card p {
    margin: 8px 0 0;

    font-size: 12px;
    color: #84969a;
}


/* ========================================= */
/* CONTENT GRID */
/* ========================================= */

.content-grid {
    display: grid;

    grid-template-columns: minmax(0, 1fr) minmax(0, 1.2fr);

    gap: 20px;
}

.left-column,
.right-column {
    display: flex;
    flex-direction: column;

    gap: 20px;
}

.card {
    padding: 24px;

    box-sizing: border-box;

    border: 1px solid #e0eceb;
    border-radius: 17px;

    background: white;

    box-shadow: 0 3px 12px rgba(36, 108, 101, 0.04);
}

.card h3 {
    margin: 0 0 18px;

    font-size: 17px;
    font-weight: 700;

    color: #19343d;
}


/* ========================================= */
/* AKSI CEPAT */
/* ========================================= */

.attendance-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;

    width: 100%;
    padding: 15px;

    box-sizing: border-box;

    border-radius: 13px;

    background: linear-gradient(135deg, #18b6a2, #1fc76c);

    color: white;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;

    transition: opacity 0.2s, transform 0.2s;
}

.attendance-button:hover {
    opacity: 0.92;
    transform: translateY(-1px);
}

.attendance-button svg {
    width: 20px;
    height: 20px;
}

.attendance-done {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 16px;

    box-sizing: border-box;

    border: 1px solid #d1f2e0;
    border-radius: 13px;

    background: #ecfdf5;

    color: #047857;
    font-size: 14px;
    font-weight: 500;
}

.attendance-done svg {
    flex-shrink: 0;

    width: 20px;
    height: 20px;
}

.quick-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);

    gap: 12px;

    margin-top: 14px;
}

.quick-button {
    padding: 12px;

    border: 1px solid #dcefeb;
    border-radius: 12px;

    background: #f6fbfa;

    color: #29434a;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;

    transition: background 0.2s;
}

.quick-button:hover {
    background: #e8f7f4;
}


/* ========================================= */
/* RINGKASAN BULAN */
/* ========================================= */

.summary-item {
    margin-bottom: 18px;
}

.summary-item:last-child {
    margin-bottom: 0;
}

.summary-label {
    display: flex;
    justify-content: space-between;

    margin-bottom: 8px;

    font-size: 13px;
    color: #6f8387;
}

.summary-label strong {
    color: #19343d;
}

.progress {
    height: 8px;

    overflow: hidden;

    border-radius: 20px;

    background: #eef4f3;
}

.progress-fill {
    height: 100%;

    border-radius: 20px;

    transition: width 0.4s ease;
}

.progress-fill.hadir { background: #1fc76c; }
.progress-fill.terlambat { background: #f5a623; }
.progress-fill.izin { background: #3b9ae1; }
.progress-fill.tidak-hadir { background: #e5534b; }


/* ========================================= */
/* AKTIVITAS TERKINI */
/* ========================================= */

.card-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 18px;
}

.card-title-row h3 {
    margin: 0;
}

.see-all {
    color: #18ae9e;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 14px;

    padding: 14px 0;

    border-bottom: 1px solid #eef4f3;
}

.activity-item:last-of-type {
    border-bottom: none;
}

.activity-icon {
    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    width: 40px;
    height: 40px;

    border-radius: 12px;
}

.activity-icon svg {
    width: 20px;
    height: 20px;
}

.activity-icon.success {
    background: #d9f8e5;
    color: #2fa968;
}

.activity-icon.warning {
    background: #fff1d6;
    color: #d98a0b;
}

.activity-info {
    display: flex;
    flex-direction: column;

    gap: 3px;
}

.activity-info strong {
    font-size: 14px;
    color: #19343d;
}

.activity-info span {
    font-size: 12px;
    color: #84969a;
}

.activity-time {
    display: flex;
    flex-direction: column;
    align-items: flex-end;

    gap: 5px;

    margin-left: auto;
}

.activity-time strong {
    font-size: 14px;
    color: #19343d;
}

.badge {
    padding: 3px 10px;

    border-radius: 20px;

    font-size: 11px;
    font-weight: 600;
}

.hadir-badge { background: #d9f8e5; color: #2fa968; }
.terlambat-badge { background: #fff1d6; color: #d98a0b; }
.izin-badge { background: #e1f0fc; color: #3b9ae1; }

.empty-activity {
    margin: 10px 0 0;

    text-align: center;
    font-size: 13px;
    color: #9aaaad;
}


/* ========================================= */
/* PROFILE CARD */
/* ========================================= */

.profile-card {
    display: flex;
    align-items: center;
    gap: 16px;

    padding: 22px;

    box-sizing: border-box;

    border-radius: 17px;

    background: linear-gradient(135deg, #18b6a2, #1fc76c);

    color: white;
}

.profile-photo {
    flex-shrink: 0;

    width: 64px;
    height: 64px;
}

.profile-photo-img {
    width: 100%;
    height: 100%;

    object-fit: cover;

    border-radius: 50%;
    border: 3px solid rgba(255, 255, 255, 0.5);
}

.profile-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 100%;
    height: 100%;

    border-radius: 50%;

    background: rgba(255, 255, 255, 0.25);

    font-size: 22px;
    font-weight: 700;
}

.profile-info {
    display: flex;
    flex-direction: column;

    gap: 4px;
}

.profile-info strong {
    font-size: 16px;
}

.profile-info > span {
    font-size: 12px;
    opacity: 0.9;
}

.profile-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;

    margin-top: 4px;
}

.profile-tags span {
    padding: 3px 10px;

    border-radius: 20px;

    background: rgba(255, 255, 255, 0.22);

    font-size: 11px;
}

.verification {
    display: flex;
    flex-direction: column;
    align-items: flex-end;

    gap: 4px;

    margin-left: auto;

    text-align: right;
}

.verification strong {
    display: flex;
    align-items: center;
    gap: 6px;

    font-size: 12px;
}

.verification span {
    font-size: 11px;
    opacity: 0.85;
}

.verification-dot {
    width: 8px;
    height: 8px;

    border-radius: 50%;

    background: white;
}

.verification-dot.off {
    background: rgba(255, 255, 255, 0.45);
}


/* ========================================= */
/* RESPONSIVE */
/* ========================================= */

@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .content-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .dashboard-page {
        padding: 24px 4px 40px;
    }

    /* Kartu lokasi: baris 1 = ikon + alamat + tombol refresh,
       baris 2 = status (Di Area RS). Alamat jadi lebar, tidak sempit. */
    .location-card {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: start;
        column-gap: 12px;
        row-gap: 12px;

        padding: 16px;
    }

    .location-icon {
        grid-column: 1;
        grid-row: 1;

        margin-right: 0;
    }

    .location-info {
        grid-column: 2;
        grid-row: 1;

        padding-top: 1px;
    }

    .location-info strong {
        font-size: 15px;
        line-height: 1.35;
    }

    .location-refresh {
        grid-column: 3;
        grid-row: 1;

        margin: 0;
    }

    .location-status {
        grid-column: 2 / -1;
        grid-row: 2;

        justify-self: start;
    }

    .dashboard-header {
        flex-direction: column;
        gap: 14px;
    }

    .current-time {
        align-items: flex-start;
    }

    /* Kartu statistik: 2 kolom kecil-kecil, seperti tombol Aksi Cepat.
       Desktop tidak terpengaruh (aturan ini hanya <= 640px). */
    .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;

        margin-bottom: 18px;
    }

    .stat-card {
        min-height: 0;
        padding: 12px;

        border-radius: 14px;
    }

    .stat-card-header {
        align-items: flex-start;
        gap: 6px;
    }

    .stat-card-header span {
        font-size: 11px;
        line-height: 1.3;
    }

    .stat-icon {
        width: 28px;
        height: 28px;

        flex-shrink: 0;

        border-radius: 9px;
    }

    .stat-icon svg {
        width: 16px;
        height: 16px;
    }

    .stat-main,
    .stat-value {
        margin-top: 10px;

        font-size: 19px;
        line-height: 1.2;
    }

    .stat-card p {
        margin-top: 4px;

        font-size: 11px;
        line-height: 1.35;
    }

    .profile-card {
        flex-wrap: wrap;
    }

    .verification {
        align-items: flex-start;
        margin-left: 0;
        text-align: left;
    }
}

</style>
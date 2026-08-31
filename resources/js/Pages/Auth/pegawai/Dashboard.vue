<script setup>
import PegawaiLayout from './PegawaiLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed, ref, onMounted, onUnmounted } from 'vue'

import {
    CheckCircleIcon,
    ClockIcon,
    CalendarDaysIcon,
    MapPinIcon,
    ChevronRightIcon,
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

onMounted(() => {
    // Update tiap detik supaya jam berjalan real-time, tanggal otomatis
    // ikut berganti begitu lewat tengah malam
    clockInterval = setInterval(() => {
        now.value = new Date()
    }, 1000)
})

onUnmounted(() => {
    if (clockInterval) {
        clearInterval(clockInterval)
    }
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
                </div>

                <div class="current-time">
                    <strong>{{ jamSekarang }}</strong>
                    <span>{{ tanggalSekarang }}</span>
                </div>
            </div>


            <!-- ============================= -->
            <!-- LOCATION -->
            <!-- ============================= -->
            <div class="location-card">

                <div class="location-icon">
                    <MapPinIcon />
                </div>

                <div class="location-info">
                    <span>Lokasi terdeteksi</span>

                    <strong>
                        RSUD Cibabat, Jl. Dustira No. 1, Cimahi
                    </strong>
                </div>

                <div class="location-status">
                    Di Area RS
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
                        
                        <div v-else class="p-4 mb-4 bg-emerald-50 text-emerald-700 text-sm rounded-xl font-medium border border-emerald-100 flex items-center gap-3">
                            <CheckCircleIcon class="w-5 h-5" />
                            Anda sudah menyelesaikan absensi hari ini.
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
                                <span class="verification-dot"></span>
                                Wajah Terverifikasi
                            </strong>

                            <span>
                                Diperbarui 30 Jun 2026
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

.location-status {
    margin-left: auto;

    padding: 7px 14px;

    border-radius: 20px;

    background: #d9f8e5;

    color: #2fa968;

    font-size: 12px;

    font-weight: 600;
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

.stat-card-header > span {
    font-size: 12px;
}

.stat-icon {
    display: flex;

    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    border-radius: 12px;

    background: #effbf8;

    color: #16b3a2;
}

.stat-icon svg {
    width: 19px;
}

.white-icon {
    background: rgba(255,255,255,.16);

    color: white;
}

.stat-main {
    margin-top: 18px;

    font-size: 29px;

    font-weight: 600;
}

.stat-value {
    margin-top: 17px;

    color: #18aa9b;

    font-size: 28px;

    line-height: 1;

    font-weight: 500;
}

.stat-value.empty {
    color: #7b8689;
}

.stat-card p {
    margin: 8px 0 0;

    color: #7b8c90;

    font-size: 12px;
}

.status-card p {
    color: rgba(255,255,255,.75);
}


/* ========================================= */
/* CONTENT */
/* ========================================= */

.content-grid {
    display: grid;

    grid-template-columns:
        0.75fr 1.55fr;

    gap: 20px;
}

.left-column,
.right-column {
    display: flex;

    flex-direction: column;

    gap: 20px;
}

.card {
    padding: 22px;

    box-sizing: border-box;

    border: 1px solid #e0eceb;

    border-radius: 17px;

    background: white;

    box-shadow:
        0 3px 12px rgba(36, 108, 101, 0.04);
}

.card h3 {
    margin: 0 0 17px;

    color: #294149;

    font-size: 15px;

    font-weight: 700;
}


/* ========================================= */
/* AKSI CEPAT */
/* ========================================= */

.attendance-button {
    display: flex;

    align-items: center;
    justify-content: center;

    width: 100%;
    height: 45px;

    gap: 8px;

    border-radius: 11px;

    background:
        linear-gradient(
            90deg,
            #18b7a4,
            #20c76b
        );

    color: white;

    font-size: 13px;

    font-weight: 600;

    text-decoration: none;

    transition: .2s ease;
}

.attendance-button:hover {
    transform: translateY(-1px);

    opacity: .93;
}

.attendance-button svg {
    width: 18px;
}

.quick-buttons {
    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 8px;

    margin-top: 9px;
}

.quick-button {
    display: flex;

    align-items: center;
    justify-content: center;

    height: 38px;

    border: 1px solid #e0ebea;

    border-radius: 9px;

    background: white;

    color: #738083;

    font-size: 12px;

    text-decoration: none;

    cursor: pointer;

    transition: all .2s ease;
}

.quick-button:hover {
    background: #effaf7;

    border-color: #20b5a2;

    color: #20a998;
}


/* ========================================= */
/* RINGKASAN */
/* ========================================= */

.summary-item {
    margin-bottom: 15px;
}

.summary-item:last-child {
    margin-bottom: 0;
}

.summary-label {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 6px;

    color: #78888c;

    font-size: 12px;
}

.summary-label strong {
    color: #53656a;

    font-weight: 500;
}

.progress {
    width: 100%;

    height: 6px;

    overflow: hidden;

    border-radius: 20px;

    background: #edf5f4;
}

.progress-fill {
    height: 100%;

    border-radius: 20px;
}

.progress-fill.hadir {
    background: #20c66b;
}

.progress-fill.terlambat {
    background: #f3a914;
}

.progress-fill.izin {
    background: #18b4a3;
}

.progress-fill.tidak-hadir {
    background: #d9e3e3;
}


/* ========================================= */
/* ACTIVITY */
/* ========================================= */

.activity-card {
    min-height: 330px;
}

.card-title-row {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 10px;
}

.card-title-row h3 {
    margin: 0;
}

.see-all {
    color: #1eaa9d;

    font-size: 12px;

    font-weight: 600;

    text-decoration: none;
}

.see-all:hover {
    text-decoration: underline;
}

.activity-item {
    display: flex;

    align-items: center;

    min-height: 66px;

    padding: 8px 0;

    border-bottom: 1px solid #edf2f2;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    display: flex;

    align-items: center;
    justify-content: center;

    width: 38px;
    height: 38px;

    flex-shrink: 0;

    margin-right: 12px;

    border-radius: 11px;
}

.activity-icon svg {
    width: 18px;
}

.activity-icon.success {
    background: #dcf8e6;

    color: #38bf76;
}

.activity-icon.warning {
    background: #fff2c9;

    color: #e9ad18;
}

.activity-info {
    display: flex;

    flex-direction: column;

    gap: 3px;

    min-width: 0;
}

.activity-info strong {
    color: #34474d;

    font-size: 12px;
}

.activity-info span {
    color: #899598;

    font-size: 11px;
}

.activity-time {
    display: flex;

    flex-direction: column;

    align-items: flex-end;

    margin-left: auto;
}

.activity-time strong {
    color: #33464d;

    font-size: 13px;
}

.badge {
    margin-top: 4px;

    padding: 3px 8px;

    border-radius: 20px;

    font-size: 10px;
}

.hadir-badge {
    background: #dcf8e6;

    color: #38b970;
}

.terlambat-badge {
    background: #fff0bf;

    color: #d99a13;
}

.izin-badge {
    background: #dbeeff;

    color: #1c7ed6;
}

.empty-activity {
    padding: 24px 0;

    text-align: center;

    color: #a3adb0;

    font-size: 12px;
}


/* ========================================= */
/* PROFILE CARD */
/* ========================================= */

.profile-card {
    display: flex;

    align-items: center;

    padding: 20px;

    border: 1px solid #e0eceb;

    border-radius: 17px;

    background: white;

    box-shadow:
        0 3px 12px rgba(36, 108, 101, 0.04);
}

.profile-photo {
    margin-right: 13px;
}

.profile-placeholder {
    display: flex;

    align-items: center;
    justify-content: center;

    width: 55px;
    height: 55px;

    border-radius: 14px;

    background:
        linear-gradient(
            135deg,
            #d8f5ef,
            #bcebdc
        );

    color: #16a993;

    font-size: 17px;

    font-weight: 700;
}

.profile-photo-img {
    display: block;

    width: 55px;
    height: 55px;

    border-radius: 14px;

    object-fit: cover;
}

.profile-info {
    display: flex;

    flex-direction: column;

    gap: 4px;
}

.profile-info > strong {
    color: #33464d;

    font-size: 13px;
}

.profile-info > span {
    color: #8a9699;

    font-size: 11px;
}

.profile-tags {
    display: flex;

    gap: 6px;

    margin-top: 3px;
}

.profile-tags span {
    padding: 4px 8px;

    border-radius: 20px;

    background: #e9faf6;

    color: #25ae9e;

    font-size: 10px;
}

.verification {
    display: flex;

    flex-direction: column;

    align-items: flex-end;

    margin-left: auto;

    gap: 5px;
}

.verification strong {
    color: #32b96d;

    font-size: 11px;
}

.verification span:last-child {
    color: #8a9699;

    font-size: 10px;
}

.verification-dot {
    display: inline-block;

    width: 7px;
    height: 7px;

    margin-right: 4px;

    border-radius: 50%;

    background: #27bd6a;
}


/* ========================================= */
/* RESPONSIVE */
/* ========================================= */

@media (max-width: 1100px) {

    .stats-grid {
        grid-template-columns:
            repeat(2, 1fr);
    }

    .content-grid {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 700px) {

    .dashboard-page {
        padding: 25px 18px 40px;
    }

    .dashboard-header {
        flex-direction: column;

        gap: 15px;
    }

    .current-time {
        align-items: flex-start;
    }

    .location-card {
        align-items: flex-start;
    }

    .location-status {
        display: none;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .profile-card {
        align-items: flex-start;
    }

    .verification {
        display: none;
    }

}

</style>
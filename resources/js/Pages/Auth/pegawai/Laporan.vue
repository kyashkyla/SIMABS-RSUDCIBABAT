<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import PegawaiLayout from './PegawaiLayout.vue'

import {
    CheckCircleIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    CalendarDaysIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    rows: {
        type: Array,
        default: () => [],
    },
    summary: {
        type: Object,
        default: () => ({ hadir: 0, terlambat: 0, izin: 0, tidak_hadir: 0 }),
    },
    periode: {
        type: Object,
        required: true,
    },
})

const methodLabel = {
    face: 'Face Recognition',
    otp: 'OTP',
    alternative: 'Alternatif',
}

// Label "Agustus 2026" dari tahun & bulan yang dikirim backend
const labelBulan = computed(() => {
    const tanggal = new Date(props.periode.tahun, props.periode.bulan - 1, 1)
    return tanggal.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })
})

// Baris tabel, tanggal & hari diformat ke Bahasa Indonesia
const rows = computed(() =>
    props.rows.map((item) => {
        const tanggal = new Date(`${item.attendance_date}T00:00:00`)

        return {
            ...item,
            dateShort: tanggal.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
            }),
            dayLabel: tanggal.toLocaleDateString('id-ID', { weekday: 'long' }),
            methodLabel: methodLabel[item.method] ?? (item.method ?? '—'),
        }
    })
)

const totalHari = computed(() => {
    const s = props.summary
    return s.hadir + s.terlambat + s.izin + s.tidak_hadir
})

function pergiKeBulan(periodeString) {
    const [tahun, bulan] = periodeString.split('-')

    router.get(
        route('pegawai.laporan'),
        { tahun, bulan },
        { preserveState: true, preserveScroll: true }
    )
}
</script>

<template>
    <Head title="Laporan Absensi" />

    <PegawaiLayout>

        <div class="page-container">

            <div class="page-header">
                <div>
                    <h1>Laporan Absensi</h1>
                    <p>
                        Lihat seluruh riwayat kehadiran Anda per bulan
                    </p>
                </div>

                <div class="filter-box">
                    <button
                        type="button"
                        class="nav-button"
                        @click="pergiKeBulan(periode.bulan_sebelumnya)"
                    >
                        <ChevronLeftIcon />
                    </button>

                    <CalendarDaysIcon class="calendar-icon" />
                    <span>{{ labelBulan }}</span>

                    <button
                        type="button"
                        class="nav-button"
                        :disabled="!periode.bisa_ke_bulan_berikutnya"
                        @click="pergiKeBulan(periode.bulan_berikutnya)"
                    >
                        <ChevronRightIcon />
                    </button>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="summary-grid">

                <div class="summary-card">
                    <span>Total Kehadiran</span>
                    <strong>{{ summary.hadir }} Hari</strong>
                    <small>Dalam bulan ini</small>
                </div>

                <div class="summary-card">
                    <span>Terlambat</span>
                    <strong>{{ summary.terlambat }} Hari</strong>
                    <small>Perlu diperhatikan</small>
                </div>

                <div class="summary-card">
                    <span>Izin</span>
                    <strong>{{ summary.izin }} Hari</strong>
                    <small>Absensi alternatif</small>
                </div>

                <div class="summary-card">
                    <span>Tidak Hadir</span>
                    <strong>{{ summary.tidak_hadir }} Hari</strong>
                    <small>Tanpa keterangan</small>
                </div>

            </div>

            <!-- TABLE -->
            <div class="card">

                <div class="card-header">
                    <div>
                        <h2>Daftar Riwayat</h2>
                        <p>Riwayat absensi bulan {{ labelBulan }}</p>
                    </div>
                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr v-for="item in rows" :key="item.attendance_date">
                                <td>
                                    <strong>{{ item.dateShort }}</strong>
                                    <span>{{ item.dayLabel }}</span>
                                </td>

                                <td>{{ item.check_in_at ?? '—' }}</td>

                                <td>
                                    <span
                                        class="method"
                                        :class="{ otp: item.method === 'otp' }"
                                    >
                                        {{ item.methodLabel }}
                                    </span>
                                </td>

                                <td>
                                    <span
                                        v-if="item.status === 'hadir'"
                                        class="status success"
                                    >
                                        <CheckCircleIcon />
                                        Hadir
                                    </span>

                                    <span
                                        v-else-if="item.status === 'terlambat'"
                                        class="status late"
                                    >
                                        <ExclamationTriangleIcon />
                                        Terlambat
                                    </span>

                                    <span v-else class="status izin">
                                        <ClockIcon />
                                        Izin
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="rows.length === 0">
                                <td colspan="4" class="empty-row">
                                    Belum ada riwayat absensi di bulan ini.
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </PegawaiLayout>
</template>

<style scoped>

.page-container {
    max-width: 1150px;
    margin: auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.page-header h1 {
    margin: 0;
    color: #263238;
    font-size: 24px;
}

.page-header p {
    margin: 5px 0 0;
    color: #7b898c;
    font-size: 13px;
}

.filter-box {
    display: flex;
    align-items: center;
    gap: 7px;

    padding: 6px 8px;

    background: white;

    border: 1px solid #dfeaea;
    border-radius: 9px;

    color: #536164;
    font-size: 11px;
}

.filter-box .calendar-icon {
    width: 17px;
    color: #20aa97;
}

.nav-button {
    display: flex;
    align-items: center;
    justify-content: center;

    width: 24px;
    height: 24px;

    border: none;
    border-radius: 6px;
    background: transparent;

    color: #536164;
    cursor: pointer;
}

.nav-button:hover:not(:disabled) {
    background: #f0f7f6;
    color: #20a995;
}

.nav-button:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}

.nav-button svg {
    width: 16px;
}

/* SUMMARY */

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.summary-card {
    padding: 16px;

    background: white;

    border: 1px solid #e2eeee;
    border-radius: 14px;
}

.summary-card span,
.summary-card small {
    display: block;
    color: #849093;
    font-size: 10px;
}

.summary-card strong {
    display: block;

    margin: 8px 0 3px;

    color: #23a994;
    font-size: 22px;
}

.card {
    background: white;
    border: 1px solid #e2eeee;
    border-radius: 15px;
    padding: 20px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 18px;
}

.card-header h2 {
    margin: 0;
    color: #354347;
    font-size: 15px;
}

.card-header p {
    margin: 4px 0 0;
    color: #899598;
    font-size: 10px;
}

/* TABLE */

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    padding: 11px;

    background: #f6faf9;

    color: #7b898c;

    text-align: left;

    font-size: 10px;
    font-weight: 600;
}

td {
    padding: 13px 11px;

    border-bottom: 1px solid #edf2f2;

    color: #4c595c;

    font-size: 11px;
}

td strong {
    display: block;
    color: #354347;
}

td span:not(.method):not(.status) {
    color: #919b9d;
    font-size: 9px;
}

.method {
    padding: 5px 8px;

    background: #edf8f6;

    border-radius: 6px;

    color: #20a995;

    font-size: 9px;
}

.method.otp {
    background: #fff5dc;
    color: #c38a00;
}

.status {
    display: inline-flex;
    align-items: center;
    gap: 4px;

    padding: 5px 8px;

    border-radius: 20px;

    font-size: 9px;
    font-weight: 600;
}

.status svg {
    width: 13px;
}

.status.success {
    background: #daf6e4;
    color: #27a666;
}

.status.late {
    background: #fff0bd;
    color: #c58b00;
}

.status.izin {
    background: #dbeeff;
    color: #1c7ed6;
}

.empty-row {
    text-align: center;
    padding: 40px 11px !important;
    color: #a3adb0;
}

@media (max-width: 800px) {

    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 550px) {

    .page-header {
        flex-direction: column;
        gap: 10px;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

}

</style>
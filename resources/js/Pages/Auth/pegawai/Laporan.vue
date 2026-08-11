<script setup>
import PegawaiLayout from './PegawaiLayout.vue'

import {
    CheckCircleIcon,
    ClockIcon,
    ExclamationTriangleIcon,
    CalendarDaysIcon,
} from '@heroicons/vue/24/outline'
</script>

<template>
    <PegawaiLayout>

        <div class="page-container">

            <div class="page-header">
                <div>
                    <h1>Riwayat Absensi</h1>
                    <p>
                        Lihat seluruh riwayat kehadiran Anda
                    </p>
                </div>

                <div class="filter-box">
                    <CalendarDaysIcon />
                    <span>Juli 2026</span>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="summary-grid">

                <div class="summary-card">
                    <span>Total Kehadiran</span>
                    <strong>20 Hari</strong>
                    <small>Dalam bulan ini</small>
                </div>

                <div class="summary-card">
                    <span>Terlambat</span>
                    <strong>2 Hari</strong>
                    <small>Perlu diperhatikan</small>
                </div>

                <div class="summary-card">
                    <span>Izin</span>
                    <strong>1 Hari</strong>
                    <small>Disetujui</small>
                </div>

                <div class="summary-card">
                    <span>Tidak Hadir</span>
                    <strong>0 Hari</strong>
                    <small>Tanpa keterangan</small>
                </div>

            </div>

            <!-- TABLE -->
            <div class="card">

                <div class="card-header">
                    <div>
                        <h2>Daftar Riwayat</h2>
                        <p>Riwayat absensi bulan Juli 2026</p>
                    </div>

                    <button class="export-button">
                        Export
                    </button>
                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>
                                    <strong>30 Jul 2026</strong>
                                    <span>Kamis</span>
                                </td>

                                <td>07:52</td>
                                <td>—</td>

                                <td>
                                    <span class="method">
                                        Face Recognition
                                    </span>
                                </td>

                                <td>
                                    <span class="status success">
                                        <CheckCircleIcon />
                                        Hadir
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>29 Jul 2026</strong>
                                    <span>Rabu</span>
                                </td>

                                <td>07:55</td>
                                <td>16:05</td>

                                <td>
                                    <span class="method">
                                        Face Recognition
                                    </span>
                                </td>

                                <td>
                                    <span class="status success">
                                        <CheckCircleIcon />
                                        Hadir
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>28 Jul 2026</strong>
                                    <span>Selasa</span>
                                </td>

                                <td>08:15</td>
                                <td>16:10</td>

                                <td>
                                    <span class="method otp">
                                        OTP
                                    </span>
                                </td>

                                <td>
                                    <span class="status late">
                                        <ExclamationTriangleIcon />
                                        Terlambat
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>27 Jul 2026</strong>
                                    <span>Senin</span>
                                </td>

                                <td>07:48</td>
                                <td>16:02</td>

                                <td>
                                    <span class="method">
                                        Face Recognition
                                    </span>
                                </td>

                                <td>
                                    <span class="status success">
                                        <CheckCircleIcon />
                                        Hadir
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>24 Jul 2026</strong>
                                    <span>Jumat</span>
                                </td>

                                <td>07:50</td>
                                <td>16:00</td>

                                <td>
                                    <span class="method">
                                        Face Recognition
                                    </span>
                                </td>

                                <td>
                                    <span class="status success">
                                        <CheckCircleIcon />
                                        Hadir
                                    </span>
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

    padding: 9px 13px;

    background: white;

    border: 1px solid #dfeaea;
    border-radius: 9px;

    color: #536164;
    font-size: 11px;
}

.filter-box svg {
    width: 17px;
    color: #20aa97;
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

.export-button {
    padding: 8px 14px;

    border: 1px solid #dce9e7;
    border-radius: 8px;

    background: white;
    color: #20a995;

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
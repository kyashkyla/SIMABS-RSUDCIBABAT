<script setup>
import { ref } from 'vue'
import PegawaiLayout from './PegawaiLayout.vue'

import LocationValidation from '@/Components/Pegawai/LocationValidation.vue'
import FaceRecognition from '@/Components/Pegawai/FaceRecognition.vue'
import AlternativeAttendanceForm from '@/Components/Pegawai/AlternativeAttendanceForm.vue'
import WaitingApproval from '@/Components/Pegawai/WaitingApproval.vue'
import OtpAttendance from '@/Components/Pegawai/OtpAttendance.vue'

const tahap = ref('lokasi')

const approvalType = ref(null)

/*
|--------------------------------------------------------------------------
| Lokasi berhasil
|--------------------------------------------------------------------------
*/

const handleLocationSuccess = () => {
    tahap.value = 'face'
}


/*
|--------------------------------------------------------------------------
| Face Recognition gagal
|--------------------------------------------------------------------------
*/

const handleFaceFailed = () => {
    tahap.value = 'alternatif'
}


/*
|--------------------------------------------------------------------------
| Pengajuan alternatif dikirim
|--------------------------------------------------------------------------
*/

const handleAlternativeSubmitted = () => {
    tahap.value = 'waiting'
}


/*
|--------------------------------------------------------------------------
| Admin melakukan approval
|--------------------------------------------------------------------------
*/

const handleApproved = (type) => {
    approvalType.value = type
    tahap.value = 'otp'
}


/*
|--------------------------------------------------------------------------
| Absensi selesai
|--------------------------------------------------------------------------
*/

const handleAttendanceSuccess = () => {
    tahap.value = 'success'
}
</script>

<template>
    <PegawaiLayout>

        <!-- ========================================================= -->
        <!-- STEP 1 : VALIDASI LOKASI -->
        <!-- ========================================================= -->

        <LocationValidation
            v-if="tahap === 'lokasi'"
            @location-success="handleLocationSuccess"
        />


        <!-- ========================================================= -->
        <!-- STEP 2 : FACE RECOGNITION -->
        <!-- ========================================================= -->

        <FaceRecognition
            v-else-if="tahap === 'face'"
            @failed="handleFaceFailed"
        />


        <!-- ========================================================= -->
        <!-- STEP 3 : PENGAJUAN ABSEN ALTERNATIF -->
        <!-- ========================================================= -->

        <AlternativeAttendanceForm
            v-else-if="tahap === 'alternatif'"
            @submitted="handleAlternativeSubmitted"
        />


        <!-- ========================================================= -->
        <!-- STEP 4 : MENUNGGU APPROVAL ADMIN -->
        <!-- ========================================================= -->

        <WaitingApproval
            v-else-if="tahap === 'waiting'"
            @approved="handleApproved"
        />


        <!-- ========================================================= -->
        <!-- STEP 5 : OTP -->
        <!-- ========================================================= -->

        <OtpAttendance
            v-else-if="tahap === 'otp'"
            :approval-type="approvalType"
            @success="handleAttendanceSuccess"
        />


        <!-- ========================================================= -->
        <!-- STEP 6 : ABSENSI SELESAI -->
        <!-- ========================================================= -->

        <div
            v-else-if="tahap === 'success'"
            class="success-wrapper"
        >

            <div class="success-card">

                <div class="success-icon">
                    ✓
                </div>

                <h2>
                    Absensi Berhasil
                </h2>

                <p>
                    Kehadiran Anda telah berhasil dicatat oleh sistem.
                </p>

                <div class="success-time">
                    <strong>07:52</strong>
                    <span>Selasa, 11 Agustus 2026</span>
                </div>

            </div>

        </div>

    </PegawaiLayout>
</template>

<style scoped>

.success-wrapper {
    width: 100%;
    max-width: 760px;
    margin: 50px auto;
}

.success-card {
    padding: 40px 30px;
    background: white;
    border: 1px solid #e2eeee;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 4px 16px rgba(30, 100, 90, .04);
}

.success-icon {
    width: 70px;
    height: 70px;
    margin: 0 auto 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #daf6e4;
    color: #27aa68;

    font-size: 32px;
    font-weight: bold;
}

.success-card h2 {
    margin: 0;
    color: #354347;
    font-size: 20px;
}

.success-card p {
    margin: 8px 0 20px;
    color: #899598;
    font-size: 11px;
}

.success-time {
    padding: 14px;
    background: #f7faf9;
    border-radius: 10px;
}

.success-time strong {
    display: block;
    color: #20aa96;
    font-size: 25px;
}

.success-time span {
    color: #899598;
    font-size: 9px;
}

</style>
<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'

import PegawaiLayout from './PegawaiLayout.vue'

import LocationValidation from '@/Components/pegawai/LocationValidation.vue'
import FaceRecognition from '@/Components/pegawai/FaceRecognition.vue'
import OtpAttendance from '@/Components/pegawai/OtpAttendance.vue'
import AttendanceSuccess from '@/Components/pegawai/AttendanceSuccess.vue'
import AlternativeAttendanceForm from '@/Components/pegawai/AlternativeAttendanceForm.vue'
import WaitingApproval from '@/Components/pegawai/WaitingApproval.vue'

const props = defineProps({
    employee: {
        type: Object,
        required: true, // { id, name, photo_url }
    },
    todayAttendance: {
        type: Object,
        default: null,
    },
})

const step = ref('location')

// Koordinat device yang sudah tervalidasi di step lokasi, dipakai lagi
// saat menyimpan absensi di step Face Recognition / OTP.
const deviceLocation = ref(null)

/*
|--------------------------------------------------------------------------
| Status Approval
|--------------------------------------------------------------------------
|
| none      = belum pernah mengajukan
| pending   = menunggu admin
| temporary = approval sementara
| permanent = approval permanen
|
*/

const approvalStatus = ref('none')

/*
|--------------------------------------------------------------------------
| Metode Absensi
|--------------------------------------------------------------------------
*/

const attendanceMethod = ref('')

/*
|--------------------------------------------------------------------------
| Setelah lokasi berhasil
|--------------------------------------------------------------------------
|
| Kalau belum ada approval:
| Location → Face Recognition
|
| Kalau sudah ada approval:
| Location → OTP
|
| Approval temporary maupun permanent
| sama-sama menggunakan OTP setelah lokasi.
|
*/

const locationConfirmed = (location) => {
    deviceLocation.value = location

    if (
        approvalStatus.value === 'temporary' ||
        approvalStatus.value === 'permanent'
    ) {
        step.value = 'otp'
    } else {
        step.value = 'face'
    }
}

/*
|--------------------------------------------------------------------------
| Face Recognition Berhasil
|--------------------------------------------------------------------------
*/

const faceSuccess = (attendance) => {
    attendanceMethod.value = 'Face Recognition'
    step.value = 'success'
}

/*
|--------------------------------------------------------------------------
| Face Recognition Gagal
|--------------------------------------------------------------------------
*/

const faceFailed = () => {
    step.value = 'alternative'
}

/*
|--------------------------------------------------------------------------
| Pengajuan Alternatif Berhasil
|--------------------------------------------------------------------------
*/

const alternativeSubmitted = () => {
    step.value = 'waiting'
}

/*
|--------------------------------------------------------------------------
| Approval Admin
|--------------------------------------------------------------------------
|
| Untuk sementara masih simulasi frontend.
| Nanti bagian ini diganti response dari backend/database.
|
*/

const approvalReceived = (type) => {
    approvalStatus.value = type

    /*
    Setelah admin approve, pegawai HARUS
    melakukan deteksi lokasi kembali.
    */

    step.value = 'location'
}

/*
|--------------------------------------------------------------------------
| OTP Berhasil
|--------------------------------------------------------------------------
*/

const otpSuccess = () => {
    attendanceMethod.value = 'OTP'
    step.value = 'success'
}

/*
|--------------------------------------------------------------------------
| Selesai Absensi
|--------------------------------------------------------------------------
*/

const finishAttendance = () => {
    router.visit(route('pegawai.dashboard'))
}
</script>

<template>

    <Head title="Absensi Kehadiran" />

    <PegawaiLayout>

        <div class="min-h-screen bg-[#f5fcfa] px-5 py-6 md:px-8">

            <!-- ===================================================== -->
            <!-- LOCATION -->
            <!-- ===================================================== -->

            <LocationValidation
                v-if="step === 'location'"
                :approval-status="approvalStatus"
                @location-success="locationConfirmed"
            />


            <!-- ===================================================== -->
            <!-- FACE RECOGNITION -->
            <!-- ===================================================== -->

            <FaceRecognition
                v-else-if="step === 'face'"
                :employee="props.employee"
                :device-location="deviceLocation"
                @success="faceSuccess"
                @failed="faceFailed"
            />


            <!-- ===================================================== -->
            <!-- ALTERNATIVE FORM -->
            <!-- ===================================================== -->

            <AlternativeAttendanceForm
                v-else-if="step === 'alternative'"
                @submitted="alternativeSubmitted"
            />


            <!-- ===================================================== -->
            <!-- WAITING APPROVAL -->
            <!-- ===================================================== -->

            <WaitingApproval
                v-else-if="step === 'waiting'"
                @approved="approvalReceived"
            />


            <!-- ===================================================== -->
            <!-- OTP -->
            <!-- ===================================================== -->

            <OtpAttendance
                v-else-if="step === 'otp'"
                :approval-type="approvalStatus"
                @success="otpSuccess"
            />


            <!-- ===================================================== -->
            <!-- SUCCESS -->
            <!-- ===================================================== -->

            <AttendanceSuccess
                v-else-if="step === 'success'"
                :method="attendanceMethod"
                @finish="finishAttendance"
            />

        </div>

    </PegawaiLayout>

</template>
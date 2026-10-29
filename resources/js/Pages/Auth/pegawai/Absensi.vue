<script setup>
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'

import PegawaiLayout from './PegawaiLayout.vue'
import { toast } from '@/lib/toast'

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
    approvalStatus: {
        type: String,
        default: 'none',
    },
    /*
    | Dikirim backend: { state, message, window }
    | state = check_in   -> jam absen masuk, belum absen   (boleh lanjut)
    |         check_out  -> jam absen pulang, belum pulang (boleh lanjut)
    |         completed  -> masuk & pulang sudah           (dicegat)
    |         checked_in -> sudah masuk, jam pulang belum   (dicegat)
    |         closed     -> bukan jam absensi              (dicegat)
    */
    attendancePhase: {
        type: Object,
        default: () => ({ state: 'check_in', message: '', window: null }),
    },
})

// Absen pulang kalau fase dari server = check_out.
const isCheckOut = computed(() => props.attendancePhase?.state === 'check_out')

// Boleh lanjut ke Face / OTP hanya kalau sedang jam absen masuk / pulang
// DAN belum absen di fase itu.
const canAttend = computed(() =>
    ['check_in', 'check_out'].includes(props.attendancePhase?.state)
)

// Halaman selalu mulai dari step Lokasi. Pencegatan terjadi saat pegawai
// menekan tombol "Lanjut" setelah lokasi valid (lihat locationConfirmed).
const step = ref('location')

// Koordinat device yang sudah tervalidasi di step lokasi, dipakai lagi
// saat menyimpan absensi di step Face Recognition / OTP.
const deviceLocation = ref(null)

// Mencegah tombol "Lanjut" diproses dua kali saat status sedang dicek.
const isCheckingPhase = ref(false)

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

const approvalStatus = ref(props.approvalStatus)

/*
|--------------------------------------------------------------------------
| Metode Absensi
|--------------------------------------------------------------------------
*/

const attendanceMethod = ref('')

/*
|--------------------------------------------------------------------------
| Ambil status absensi terbaru dari server
|--------------------------------------------------------------------------
|
| Dipanggil tepat saat "Lanjut" ditekan, supaya status tidak basi
| (mis. halaman dibuka sebelum jam absen, lalu "Lanjut" ditekan setelahnya).
|
*/

const refreshPhase = () =>
    new Promise((resolve) => {
        router.reload({
            only: ['attendancePhase', 'todayAttendance'],
            preserveState: true,
            preserveScroll: true,
            onFinish: () => resolve(),
        })
    })

const blockedTitles = {
    completed: 'Anda Telah Melakukan Absensi',
    checked_in: 'Anda Telah Absen Masuk',
    closed: 'Di Luar Jam Absen',
}

/*
|--------------------------------------------------------------------------
| Setelah lokasi berhasil
|--------------------------------------------------------------------------
|
| Dicegat (muncul notif, tetap di halaman ini) kalau:
| - sudah absen masuk & pulang
| - sudah absen masuk dan jam pulang belum dibuka
| - bukan jam absen masuk / pulang
|
| Kalau lolos:
| Belum ada approval  : Location → Face Recognition
| Sudah ada approval  : Location → OTP
|
*/

const locationConfirmed = async (location) => {
    if (isCheckingPhase.value) return
    isCheckingPhase.value = true

    try {
        await refreshPhase()
    } finally {
        isCheckingPhase.value = false
    }

    if (!canAttend.value) {
        const state = props.attendancePhase?.state

        toast.warning(
            props.attendancePhase?.message ||
                'Anda tidak dapat melakukan absen karena bukan jamnya.',
            { title: blockedTitles[state] || 'Tidak Dapat Absen' }
        )
        return
    }

    deviceLocation.value = location

    if (
        approvalStatus.value === 'temporary' ||
        approvalStatus.value === 'permanent'
    ) {
        step.value = 'otp'
    } else if (approvalStatus.value === 'pending') {
        step.value = 'waiting'
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
| Status approval dicek otomatis ke server oleh komponen WaitingApproval
| (polling tiap 5 detik). Begitu disetujui, event ini terpanggil.
|
*/

const approvalReceived = (type) => {
    approvalStatus.value = type

    /*
    Setelah admin approve, pegawai HARUS
    melakukan deteksi lokasi kembali. Pengecekan jam absen tetap
    terjadi lagi saat tombol "Lanjut" ditekan.
    */

    step.value = 'location'
}

/*
|--------------------------------------------------------------------------
| Pengajuan Ditolak Admin
|--------------------------------------------------------------------------
*/

const approvalRejected = () => {
    approvalStatus.value = 'none'
    router.visit(route('pegawai.dashboard'))
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

        <div class="bg-[#f5fcfa] px-0 py-2 sm:px-5 sm:py-6 md:min-h-screen md:px-8">

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
                :is-check-out="isCheckOut"
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
                @rejected="approvalRejected"
            />


            <!-- ===================================================== -->
            <!-- OTP -->
            <!-- ===================================================== -->

            <OtpAttendance
                v-else-if="step === 'otp'"
                :approval-type="approvalStatus"
                :device-location="deviceLocation"
                :is-check-out="isCheckOut"
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
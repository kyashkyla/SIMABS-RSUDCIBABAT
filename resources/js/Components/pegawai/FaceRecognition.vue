<script setup>
import { ref } from 'vue'

import {
    FaceSmileIcon,
    ArrowPathIcon,
    ShieldCheckIcon,
    ExclamationTriangleIcon,
    CameraIcon,
} from '@heroicons/vue/24/outline'

const emit = defineEmits(['success', 'failed'])

const faceStatus = ref('ready')

/*
|--------------------------------------------------------------------------
| Mulai Face Recognition
|--------------------------------------------------------------------------
*/

const mulaiFaceRecognition = () => {

    faceStatus.value = 'scanning'

    setTimeout(() => {

        /*
        |--------------------------------------------------------------------------
        | SIMULASI
        |--------------------------------------------------------------------------
        |
        | Untuk sekarang dibuat gagal supaya alur:
        |
        | Face Recognition
        |        ↓
        | Gagal
        |        ↓
        | Form Alternatif
        |
        | bisa dites.
        |
        | Nanti ketika backend/face recognition asli sudah dibuat,
        | bagian ini diganti dengan hasil deteksi sebenarnya.
        |
        */

        faceStatus.value = 'failed'

    }, 1800)
}


/*
|--------------------------------------------------------------------------
| Coba Lagi
|--------------------------------------------------------------------------
*/

const cobaLagi = () => {

    faceStatus.value = 'ready'

}


/*
|--------------------------------------------------------------------------
| Ajukan Absensi Alternatif
|--------------------------------------------------------------------------
*/

const ajukanAlternatif = () => {

    emit('failed')

}

</script>


<template>

    <div class="main-card">

        <!-- HEADER -->

        <div class="card-title">

            <div class="title-icon">

                <FaceSmileIcon />

            </div>


            <div>

                <h2>
                    Face Recognition
                </h2>

                <p>
                    Verifikasi wajah untuk mencatat kehadiran.
                </p>

            </div>


            <div class="verified-location">

                <ShieldCheckIcon />

                Lokasi Valid

            </div>

        </div>


        <!-- CAMERA -->

        <div class="face-camera">

            <div class="face-frame">

                <div class="corner top-left"></div>

                <div class="corner top-right"></div>

                <div class="corner bottom-left"></div>

                <div class="corner bottom-right"></div>


                <!-- READY -->

                <FaceSmileIcon
                    v-if="faceStatus === 'ready'"
                    class="face-icon"
                />


                <!-- SCANNING -->

                <ArrowPathIcon
                    v-if="faceStatus === 'scanning'"
                    class="loading-icon"
                />


                <!-- FAILED -->

                <ExclamationTriangleIcon
                    v-if="faceStatus === 'failed'"
                    class="failed-icon"
                />


                <!-- TEXT -->

                <p>

                    <template v-if="faceStatus === 'ready'">

                        Posisikan wajah di tengah

                    </template>


                    <template v-else-if="faceStatus === 'scanning'">

                        Memverifikasi wajah...

                    </template>


                    <template v-else>

                        Wajah tidak berhasil dikenali

                    </template>

                </p>

            </div>

        </div>


        <!-- INSTRUKSI -->

        <div
            v-if="faceStatus === 'ready'"
            class="instruction"
        >

            <CameraIcon />

            <div>

                <strong>
                    Pastikan wajah terlihat jelas
                </strong>

                <span>
                    Pastikan pencahayaan cukup dan wajah berada
                    di dalam area kamera.
                </span>

            </div>

        </div>


        <!-- SEDANG SCAN -->

        <div
            v-if="faceStatus === 'scanning'"
            class="instruction"
        >

            <ArrowPathIcon class="loading-small" />

            <div>

                <strong>
                    Sedang melakukan verifikasi
                </strong>

                <span>
                    Mohon tunggu sampai proses pengenalan wajah selesai.
                </span>

            </div>

        </div>


        <!-- GAGAL -->

        <div
            v-if="faceStatus === 'failed'"
            class="failed-box"
        >

            <ExclamationTriangleIcon />

            <div>

                <strong>
                    Face Recognition tidak berhasil
                </strong>

                <span>
                    Anda dapat mencoba kembali atau mengajukan
                    absensi alternatif kepada Admin.
                </span>

            </div>

        </div>


        <!-- BUTTON MULAI -->

        <button
            v-if="faceStatus !== 'failed'"
            type="button"
            class="primary-button"
            @click="mulaiFaceRecognition"
            :disabled="faceStatus === 'scanning'"
        >

            <FaceSmileIcon />

            {{
                faceStatus === 'scanning'
                    ? 'Memverifikasi...'
                    : 'Mulai Face Recognition'
            }}

        </button>


        <!-- BUTTON GAGAL -->

        <div
            v-if="faceStatus === 'failed'"
            class="actions"
        >

            <button
                type="button"
                class="secondary-button"
                @click="cobaLagi"
            >

                <ArrowPathIcon />

                Coba Lagi

            </button>


            <button
                type="button"
                class="alternative-button"
                @click="ajukanAlternatif"
            >

                Ajukan Absen Alternatif

            </button>

        </div>

    </div>

</template>


<style scoped>

.main-card {
    max-width: 760px;
    margin: auto;
    padding: 25px;

    background: white;

    border: 1px solid #e2eeee;
    border-radius: 16px;

    box-shadow: 0 4px 16px rgba(30, 100, 90, .04);
}

.card-title {
    display: flex;
    align-items: center;
    gap: 11px;
}

.title-icon {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #e8f8f5;
    border-radius: 11px;
}

.title-icon svg {
    width: 22px;
    color: #20a995;
}

.card-title h2 {
    margin: 0;
    color: #354347;
    font-size: 17px;
}

.card-title p {
    margin: 4px 0 0;
    color: #899598;
    font-size: 10px;
}

.verified-location {
    margin-left: auto;

    display: flex;
    align-items: center;
    gap: 5px;

    padding: 6px 9px;

    background: #daf6e4;
    color: #28a968;

    border-radius: 20px;

    font-size: 9px;
    font-weight: 600;
}

.verified-location svg {
    width: 14px;
}

.face-camera {
    display: flex;
    justify-content: center;
    margin: 22px 0;
}

.face-frame {
    position: relative;

    width: 310px;
    height: 270px;

    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;

    background: #f1f8f6;
    border-radius: 15px;
}

.face-icon {
    width: 80px;
    color: #20aa97;
}

.loading-icon {
    width: 55px;
    color: #20aa97;
    animation: spin 1s linear infinite;
}

.failed-icon {
    width: 70px;
    color: #d69a00;
}

.face-frame p {
    margin-top: 14px;
    color: #718083;
    font-size: 10px;
}

.corner {
    position: absolute;
    width: 32px;
    height: 32px;
    border-color: #20ad98;
}

.top-left {
    top: 23px;
    left: 23px;
    border-top: 3px solid;
    border-left: 3px solid;
}

.top-right {
    top: 23px;
    right: 23px;
    border-top: 3px solid;
    border-right: 3px solid;
}

.bottom-left {
    bottom: 23px;
    left: 23px;
    border-bottom: 3px solid;
    border-left: 3px solid;
}

.bottom-right {
    bottom: 23px;
    right: 23px;
    border-bottom: 3px solid;
    border-right: 3px solid;
}

.instruction {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 11px;

    background: #f6faf9;
    border-radius: 9px;
}

.instruction svg {
    width: 20px;
    color: #20aa97;
}

.loading-small {
    animation: spin 1s linear infinite;
}

.instruction strong {
    display: block;
    color: #455356;
    font-size: 10px;
}

.instruction span {
    display: block;
    margin-top: 2px;
    color: #899598;
    font-size: 9px;
}

.failed-box {
    display: flex;
    align-items: center;
    gap: 9px;

    padding: 12px;

    background: #fff7e5;

    border: 1px solid #f5e2ad;

    border-radius: 9px;
}

.failed-box > svg {
    width: 21px;
    color: #d49700;
}

.failed-box strong {
    display: block;
    color: #66532b;
    font-size: 10px;
}

.failed-box span {
    display: block;
    margin-top: 3px;
    color: #927d50;
    font-size: 9px;
}

.primary-button {
    width: 100%;
    height: 43px;

    margin-top: 13px;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;

    border: none;
    border-radius: 9px;

    background: linear-gradient(90deg, #18b7a4, #20c66b);

    color: white;

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;
}

.primary-button svg {
    width: 17px;
}

.primary-button:disabled {
    opacity: .65;
    cursor: not-allowed;
}

.actions {
    display: flex;
    gap: 9px;
    margin-top: 13px;
}

.secondary-button,
.alternative-button {
    flex: 1;

    height: 42px;

    border-radius: 9px;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;
}

.secondary-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    border: 1px solid #dce9e7;

    background: white;
    color: #20a995;
}

.secondary-button svg {
    width: 16px;
}

.alternative-button {
    border: none;
    background: #20ad98;
    color: white;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

</style>
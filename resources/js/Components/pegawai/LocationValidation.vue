<script setup>
import { ref, onMounted } from 'vue'

import {
    MapPinIcon,
    ArrowPathIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    approvalStatus: {
        type: String,
        default: 'none',
    },
})

const emit = defineEmits(['location-success'])

const lokasiStatus = ref('checking')

/*
|--------------------------------------------------------------------------
| Simulasi Deteksi Lokasi
|--------------------------------------------------------------------------
*/

const cekLokasi = () => {

    lokasiStatus.value = 'checking'

    setTimeout(() => {

        lokasiStatus.value = 'valid'

    }, 1500)
}


/*
|--------------------------------------------------------------------------
| Saat halaman dibuka
|--------------------------------------------------------------------------
*/

onMounted(() => {
    cekLokasi()
})


/*
|--------------------------------------------------------------------------
| Lanjut setelah lokasi valid
|--------------------------------------------------------------------------
*/

const lanjut = () => {

    if (lokasiStatus.value !== 'valid') {
        return
    }

    emit('location-success')
}

</script>


<template>

    <div class="page-container">

        <!-- HEADER -->

        <div class="page-header">

            <div>

                <h1>
                    Absensi Kehadiran
                </h1>

                <p>
                    Lakukan absensi sesuai metode yang tersedia.
                </p>

            </div>


            <div class="date-box">

                <strong>
                    07:52
                </strong>

                <span>
                    Selasa, 11 Agustus 2026
                </span>

            </div>

        </div>


        <!-- STEPPER -->

        <div class="stepper">

            <!-- STEP 1 -->

            <div class="step active">

                <span>
                    1
                </span>

                <div>

                    <strong>
                        Lokasi
                    </strong>

                    <small>
                        Validasi wilayah
                    </small>

                </div>

            </div>


            <div class="line"></div>


            <!-- STEP 2 -->

            <div class="step">

                <span>
                    2
                </span>

                <div>

                    <strong>
                        Absensi
                    </strong>

                    <small>
                        {{
                            props.approvalStatus === 'temporary' ||
                            props.approvalStatus === 'permanent'
                                ? 'OTP'
                                : 'Face Recognition'
                        }}
                    </small>

                </div>

            </div>


            <div class="line"></div>


            <!-- STEP 3 -->

            <div class="step">

                <span>
                    3
                </span>

                <div>

                    <strong>
                        Selesai
                    </strong>

                    <small>
                        Kehadiran tercatat
                    </small>

                </div>

            </div>

        </div>


        <!-- MAIN CARD -->

        <div class="main-card">


            <!-- TITLE -->

            <div class="card-title">

                <div class="title-icon">

                    <MapPinIcon />

                </div>


                <div>

                    <h2>
                        Validasi Lokasi
                    </h2>

                    <p>
                        Pastikan Anda berada di wilayah RSUD Cibabat.
                    </p>

                </div>

            </div>


            <!-- MAP -->

            <div class="map-container">

                <div class="map-grid"></div>


                <!-- AREA RUMAH SAKIT -->

                <div class="hospital-area">

                    <div class="hospital-label">
                        RSUD Cibabat
                    </div>

                </div>


                <!-- DEVICE -->

                <div class="device-location">

                    <div class="pulse"></div>

                    <div class="device-dot"></div>

                </div>


                <!-- MAP INFO -->

                <div class="map-info">

                    <MapPinIcon />

                    <div>

                        <strong>
                            Lokasi Perangkat
                        </strong>

                        <span>

                            {{
                                lokasiStatus === 'checking'
                                    ? 'Sedang mendeteksi posisi...'
                                    : 'Lokasi berada dalam wilayah rumah sakit'
                            }}

                        </span>

                    </div>

                </div>

            </div>


            <!-- CHECKING -->

            <div
                v-if="lokasiStatus === 'checking'"
                class="location-status checking"
            >

                <ArrowPathIcon class="spin" />

                <div>

                    <strong>
                        Mendeteksi lokasi...
                    </strong>

                    <span>
                        Mohon tunggu sebentar.
                    </span>

                </div>

            </div>


            <!-- VALID -->

            <div
                v-if="lokasiStatus === 'valid'"
                class="location-status valid"
            >

                <ShieldCheckIcon />

                <div>

                    <strong>
                        Lokasi berhasil diverifikasi
                    </strong>

                    <span>
                        Perangkat berada di dalam wilayah RSUD Cibabat.
                    </span>

                </div>

            </div>


            <!-- TOMBOL LANJUT -->

            <button
                v-if="lokasiStatus === 'valid'"
                type="button"
                class="primary-button"
                @click="lanjut"
            >

                {{
                    props.approvalStatus === 'temporary' ||
                    props.approvalStatus === 'permanent'
                        ? 'Lanjut ke OTP'
                        : 'Lanjut ke Face Recognition'
                }}

            </button>


            <!-- CEK ULANG -->

            <button
                v-if="lokasiStatus === 'valid'"
                type="button"
                class="retry-button"
                @click="cekLokasi"
            >

                <ArrowPathIcon />

                Cek Ulang Lokasi

            </button>

        </div>

    </div>

</template>


<style scoped>

.page-container {
    width: 100%;
    max-width: 1100px;
    margin: auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 18px;
}

.page-header h1 {
    margin: 0;
    color: #263238;
    font-size: 24px;
}

.page-header p {
    margin-top: 5px;
    color: #7b898c;
    font-size: 12px;
}

.date-box {
    text-align: right;
}

.date-box strong {
    display: block;
    color: #21aa96;
    font-size: 23px;
}

.date-box span {
    color: #7b898c;
    font-size: 10px;
}

.stepper {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.step {
    display: flex;
    align-items: center;
    gap: 8px;
}

.step > span {
    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: #e8eeee;
    color: #859194;

    font-size: 10px;
    font-weight: 700;
}

.step.active > span {
    background: #20ad98;
    color: white;
}

.step strong {
    display: block;
    color: #526064;
    font-size: 10px;
}

.step small {
    display: block;
    color: #8c989a;
    font-size: 8px;
}

.line {
    flex: 1;
    height: 1px;
    margin: 0 12px;
    background: #dfe8e6;
}

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

.map-container {
    position: relative;

    height: 350px;

    margin: 20px 0;

    overflow: hidden;

    border-radius: 14px;

    background: #edf4f2;
}

.map-grid {
    position: absolute;
    inset: 0;

    opacity: .4;

    background-image:
        linear-gradient(#d5e5e2 1px, transparent 1px),
        linear-gradient(90deg, #d5e5e2 1px, transparent 1px);

    background-size: 55px 55px;
}

.hospital-area {
    position: absolute;

    left: 50%;
    top: 50%;

    width: 250px;
    height: 190px;

    transform: translate(-50%, -50%);

    border-radius: 50%;

    background: rgba(55, 204, 117, .16);

    border: 2px solid rgba(38, 181, 99, .5);
}

.hospital-label {
    position: absolute;

    left: 50%;
    top: 50%;

    transform: translate(-50%, -50%);

    padding: 7px 11px;

    background: white;

    border-radius: 8px;

    color: #34845c;

    font-size: 10px;
    font-weight: 700;

    box-shadow: 0 3px 10px rgba(0,0,0,.08);
}

.device-location {
    position: absolute;

    left: 54%;
    top: 46%;
}

.pulse {
    position: absolute;

    width: 45px;
    height: 45px;

    left: -22px;
    top: -22px;

    border-radius: 50%;

    background: rgba(31, 177, 105, .18);

    animation: pulse 1.8s infinite;
}

.device-dot {
    position: relative;

    width: 13px;
    height: 13px;

    border-radius: 50%;

    background: #1eaf68;

    border: 3px solid white;
}

.map-info {
    position: absolute;

    left: 15px;
    bottom: 15px;

    display: flex;
    align-items: center;
    gap: 8px;

    padding: 9px 11px;

    background: white;

    border-radius: 9px;
}

.map-info svg {
    width: 17px;
    color: #20aa96;
}

.map-info strong {
    display: block;
    color: #4c595c;
    font-size: 9px;
}

.map-info span {
    display: block;
    margin-top: 2px;
    color: #899598;
    font-size: 8px;
}

.location-status {
    display: flex;
    align-items: center;
    gap: 10px;

    padding: 12px;

    border-radius: 10px;
}

.checking {
    background: #f5faf8;
}

.valid {
    background: #effaf5;
}

.location-status svg {
    width: 21px;
}

.checking svg {
    color: #20aa97;
}

.valid svg {
    color: #23aa69;
}

.location-status strong {
    display: block;
    color: #435154;
    font-size: 10px;
}

.location-status span {
    display: block;
    margin-top: 3px;
    color: #899598;
    font-size: 9px;
}

.primary-button {
    width: 100%;
    height: 43px;

    margin-top: 13px;

    border: none;
    border-radius: 9px;

    background: linear-gradient(
        90deg,
        #18b7a4,
        #20c66b
    );

    color: white;

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;
}

.retry-button {
    width: 100%;

    margin-top: 8px;

    padding: 8px;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;

    border: none;

    background: transparent;

    color: #20a995;

    font-size: 9px;

    cursor: pointer;
}

.retry-button svg {
    width: 14px;
}

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@keyframes pulse {

    0% {
        transform: scale(.8);
        opacity: .8;
    }

    70% {
        transform: scale(1.6);
        opacity: 0;
    }

    100% {
        transform: scale(1.6);
        opacity: 0;
    }

}

</style>
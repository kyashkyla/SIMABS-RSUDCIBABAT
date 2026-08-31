<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

import {
    FaceSmileIcon,
    ArrowPathIcon,
    ShieldCheckIcon,
    ExclamationTriangleIcon,
    CameraIcon,
} from '@heroicons/vue/24/outline'

import { loadScript } from '@/lib/loadExternalAsset'

const props = defineProps({
    employee: {
        type: Object,
        required: true, // { id, name, photo_url }
    },
    deviceLocation: {
        type: Object,
        required: true, // { latitude, longitude }
    },
    isCheckOut: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['success', 'failed'])

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
|
| loading  = memuat model face-api.js & menyalakan kamera
| ready    = kamera siap, menunggu pegawai menekan tombol
| scanning = sedang mendeteksi & mencocokkan wajah
| failed   = wajah tidak cocok / tidak terdeteksi
| no-ref   = pegawai belum punya foto referensi (foto profil kosong)
| cam-error = kamera tidak bisa diakses
|
*/

const faceStatus = ref('loading')
const errorMessage = ref('')

const videoEl = ref(null)
const canvasEl = ref(null)

let mediaStream = null
let referenceDescriptor = null

// Jarak Euclidean maksimum antar descriptor supaya dianggap "wajah sama".
// Semakin kecil = semakin ketat. 0.5-0.6 adalah rentang umum face-api.js.
const MATCH_THRESHOLD = 0.60

const FACEAPI_JS = 'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js'
const MODEL_URL = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights'

/*
|--------------------------------------------------------------------------
| Setup: load face-api.js + model + kamera + descriptor foto referensi
|--------------------------------------------------------------------------
*/

const setup = async () => {
    faceStatus.value = 'loading'
    errorMessage.value = ''

    if (!props.employee?.photo_url) {
        faceStatus.value = 'no-ref'
        return
    }

    try {
        await loadScript(FACEAPI_JS)
        const faceapi = window.faceapi

        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ])

        referenceDescriptor = await computeReferenceDescriptor(faceapi)

        if (!referenceDescriptor) {
            errorMessage.value = 'Wajah tidak terdeteksi pada foto profil Anda. Perbarui foto profil di halaman Pengaturan dengan foto wajah yang jelas.'
            faceStatus.value = 'no-ref'
            return
        }

        await startCamera()

        faceStatus.value = 'ready'
    } catch (err) {
        console.error(err)
        errorMessage.value = err.message || 'Gagal menyiapkan Face Recognition.'
        faceStatus.value = 'cam-error'
    }
}

const computeReferenceDescriptor = async (faceapi) => {
    try {
        const img = await faceapi.fetchImage(props.employee.photo_url)

        const detection = await faceapi
            .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor()

        return detection ? detection.descriptor : null
    } catch (err) {
        console.error('Error computing reference descriptor:', err)
        return null
    }
}

const startCamera = async () => {
    if (!('mediaDevices' in navigator) || !navigator.mediaDevices.getUserMedia) {
        throw new Error('Browser ini tidak mendukung akses kamera.')
    }

    mediaStream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: 320, height: 280 },
        audio: false,
    })

    if (videoEl.value) {
        videoEl.value.srcObject = mediaStream
        await videoEl.value.play()
    }
}

const stopCamera = () => {
    if (mediaStream) {
        mediaStream.getTracks().forEach((track) => track.stop())
        mediaStream = null
    }
}

onMounted(() => {
    setup()
})

onBeforeUnmount(() => {
    stopCamera()
})

/*
|--------------------------------------------------------------------------
| Ambil frame video -> deteksi wajah -> bandingkan -> kirim ke server
|--------------------------------------------------------------------------
*/

const mulaiFaceRecognition = async () => {
    if (faceStatus.value === 'scanning') return

    faceStatus.value = 'scanning'
    errorMessage.value = ''

    const faceapi = window.faceapi

    try {
        const detection = await faceapi
            .detectSingleFace(videoEl.value, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor()

        if (!detection) {
            errorMessage.value = 'Wajah tidak terdeteksi. Posisikan wajah di tengah kamera dengan pencahayaan cukup.'
            faceStatus.value = 'failed'
            return
        }

        const distance = faceapi.euclideanDistance(detection.descriptor, referenceDescriptor)

        if (distance > MATCH_THRESHOLD) {
            errorMessage.value = 'Wajah tidak cocok dengan data pegawai terdaftar.'
            faceStatus.value = 'failed'
            return
        }

        const photoDataUrl = capturePhoto()

        await simpanAbsensi(photoDataUrl)
    } catch (err) {
        console.error(err)
        errorMessage.value = err?.response?.data?.message || err.message || 'Terjadi kesalahan saat memverifikasi wajah.'
        faceStatus.value = 'failed'
    }
}

const capturePhoto = () => {
    const video = videoEl.value
    const canvas = canvasEl.value

    canvas.width = video.videoWidth || 320
    canvas.height = video.videoHeight || 280

    const ctx = canvas.getContext('2d')
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height)

    return canvas.toDataURL('image/jpeg', 0.85)
}



const simpanAbsensi = async (photoDataUrl) => {
    const routeName = props.isCheckOut ? 'pegawai.absensi.pulang' : 'pegawai.absensi.simpan'
    const response = await window.axios.post(route(routeName), {
        latitude: props.deviceLocation.latitude,
        longitude: props.deviceLocation.longitude,
        method: 'face',
        photo: photoDataUrl,
    })

    stopCamera()

    emit('success', response.data.attendance)
}

/*
|--------------------------------------------------------------------------
| Coba Lagi
|--------------------------------------------------------------------------
*/

const cobaLagi = () => {
    faceStatus.value = 'ready'
    errorMessage.value = ''
}

/*
|--------------------------------------------------------------------------
| Ajukan Absensi Alternatif
|--------------------------------------------------------------------------
*/

const ajukanAlternatif = () => {
    stopCamera()
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

                <video
                    ref="videoEl"
                    class="video-feed"
                    :class="{ hidden: !['ready', 'scanning', 'failed'].includes(faceStatus) }"
                    muted
                    playsinline
                ></video>

                <canvas ref="canvasEl" class="hidden-canvas"></canvas>

                <div class="corner top-left"></div>
                <div class="corner top-right"></div>
                <div class="corner bottom-left"></div>
                <div class="corner bottom-right"></div>


                <!-- LOADING -->

                <template v-if="faceStatus === 'loading'">
                    <ArrowPathIcon class="loading-icon" />
                    <p>Menyiapkan kamera & model wajah...</p>
                </template>


                <!-- SCANNING OVERLAY -->

                <div v-if="faceStatus === 'scanning'" class="scanning-overlay">
                    <ArrowPathIcon class="loading-icon" />
                    <p>Memverifikasi wajah...</p>
                </div>


                <!-- FAILED / NO-REF / CAM-ERROR -->

                <template v-if="['failed', 'no-ref', 'cam-error'].includes(faceStatus)">
                    <ExclamationTriangleIcon
                        v-if="!['ready', 'scanning'].includes(faceStatus)"
                        class="failed-icon"
                    />
                </template>

            </div>

        </div>


        <!-- INSTRUKSI (siap) -->

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


        <!-- LOADING -->

        <div
            v-if="faceStatus === 'loading'"
            class="instruction"
        >

            <ArrowPathIcon class="loading-small" />

            <div>

                <strong>
                    Menyiapkan Face Recognition
                </strong>

                <span>
                    Memuat model pengenalan wajah & mengaktifkan kamera.
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
                    {{ errorMessage || 'Anda dapat mencoba kembali atau mengajukan absensi alternatif kepada Admin.' }}
                </span>

            </div>

        </div>


        <!-- TIDAK ADA FOTO REFERENSI -->

        <div
            v-if="faceStatus === 'no-ref'"
            class="failed-box"
        >

            <ExclamationTriangleIcon />

            <div>

                <strong>
                    Foto referensi wajah belum tersedia
                </strong>

                <span>
                    {{ errorMessage || 'Unggah foto profil yang jelas di halaman Pengaturan sebelum menggunakan Face Recognition.' }}
                </span>

            </div>

        </div>


        <!-- KAMERA ERROR -->

        <div
            v-if="faceStatus === 'cam-error'"
            class="failed-box"
        >

            <ExclamationTriangleIcon />

            <div>

                <strong>
                    Kamera tidak dapat diakses
                </strong>

                <span>
                    {{ errorMessage }}
                </span>

            </div>

        </div>


        <!-- BUTTON MULAI -->

        <button
            v-if="['ready', 'scanning'].includes(faceStatus)"
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


        <!-- BUTTON DARI NO-REF / CAM-ERROR -->

        <div
            v-if="['no-ref', 'cam-error'].includes(faceStatus)"
            class="actions"
        >

            <button
                type="button"
                class="secondary-button"
                @click="setup"
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

    background: #14201f;
    border-radius: 15px;

    overflow: hidden;
}

.video-feed {
    position: absolute;
    inset: 0;

    width: 100%;
    height: 100%;

    object-fit: cover;
    transform: scaleX(-1); /* efek cermin, lebih natural untuk selfie */
}

.video-feed.hidden {
    display: none;
}

.hidden-canvas {
    display: none;
}

.scanning-overlay {
    position: absolute;
    inset: 0;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;

    background: rgba(20, 32, 31, .55);
}

.scanning-overlay p {
    color: white;
    margin: 0;
    font-size: 10px;
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
    color: #dfe9e7;
    font-size: 10px;
}

.corner {
    position: absolute;
    width: 32px;
    height: 32px;
    border-color: #20ad98;
    z-index: 2;
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
    flex-shrink: 0;
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
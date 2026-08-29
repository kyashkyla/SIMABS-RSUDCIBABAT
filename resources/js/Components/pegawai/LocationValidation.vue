<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick } from 'vue'

import {
    MapPinIcon,
    ArrowPathIcon,
    ShieldCheckIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'

import { loadScript, loadStyle } from '@/lib/loadExternalAsset'

const props = defineProps({
    approvalStatus: {
        type: String,
        default: 'none',
    },
})

const emit = defineEmits(['location-success'])

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
|
| checking = sedang minta izin/ambil koordinat GPS device
| valid    = di dalam radius kantor
| invalid  = di luar radius kantor
| error    = GPS gagal diakses (izin ditolak, browser tidak support, dll)
|
*/

const lokasiStatus = ref('checking')
const errorMessage = ref('')
const distanceMeters = ref(null)
const radiusMeters = ref(null)
const officeName = ref('')
const devicePosition = ref(null) // { lat, lng }

let mapInstance = null
let officeMarker = null
let officeCircle = null
let deviceMarker = null
const mapEl = ref(null)

/*
|--------------------------------------------------------------------------
| Muat Leaflet (peta) dari CDN
|--------------------------------------------------------------------------
*/

const LEAFLET_CSS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
const LEAFLET_JS = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'

const ensureLeaflet = async () => {
    loadStyle(LEAFLET_CSS)
    await loadScript(LEAFLET_JS)
    return window.L
}

/*
|--------------------------------------------------------------------------
| Ambil koordinat GPS device (asli, dari browser)
|--------------------------------------------------------------------------
*/

const getDevicePosition = () => {
    return new Promise((resolve, reject) => {
        if (!('geolocation' in navigator)) {
            reject(new Error('Perangkat/browser ini tidak mendukung GPS.'))
            return
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                })
            },
            (err) => {
                if (err.code === err.PERMISSION_DENIED) {
                    reject(new Error(
                        'Izin lokasi ditolak. Aktifkan izin lokasi untuk browser ini lalu coba lagi.'
                    ))
                } else if (err.code === err.POSITION_UNAVAILABLE) {
                    reject(new Error('Posisi GPS tidak dapat ditentukan. Coba pindah ke area terbuka.'))
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

/*
|--------------------------------------------------------------------------
| Gambar / update peta
|--------------------------------------------------------------------------
*/

const renderMap = async (office, device) => {
    const L = await ensureLeaflet()

    await nextTick()

    if (!mapEl.value) return

    if (!mapInstance) {
        mapInstance = L.map(mapEl.value, {
            zoomControl: true,
            attributionControl: true,
        })

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(mapInstance)
    }

    const officeLatLng = [office.latitude, office.longitude]

    if (!officeCircle) {
        officeCircle = L.circle(officeLatLng, {
            radius: office.radius_meters,
            color: '#20ad98',
            fillColor: '#20ad98',
            fillOpacity: 0.15,
            weight: 2,
        }).addTo(mapInstance)
    } else {
        officeCircle.setLatLng(officeLatLng)
        officeCircle.setRadius(office.radius_meters)
    }

    if (!officeMarker) {
        officeMarker = L.marker(officeLatLng)
            .addTo(mapInstance)
            .bindPopup(office.name)
    } else {
        officeMarker.setLatLng(officeLatLng)
    }

    const deviceIcon = L.divIcon({
        className: '',
        html: '<div class="leaflet-device-dot"></div>',
        iconSize: [16, 16],
        iconAnchor: [8, 8],
    })

    const deviceLatLng = [device.lat, device.lng]

    if (!deviceMarker) {
        deviceMarker = L.marker(deviceLatLng, { icon: deviceIcon })
            .addTo(mapInstance)
            .bindPopup('Lokasi Anda')
    } else {
        deviceMarker.setLatLng(deviceLatLng)
    }

    const bounds = L.latLngBounds([officeLatLng, deviceLatLng]).pad(0.4)
    mapInstance.fitBounds(bounds, { maxZoom: 18 })

    // Leaflet butuh "diberitahu" ukurannya kalau container-nya baru
    // muncul/berubah ukuran (mis. saat pertama kali di-render).
    setTimeout(() => mapInstance && mapInstance.invalidateSize(), 200)
}

/*
|--------------------------------------------------------------------------
| Proses utama: ambil GPS -> kirim ke server -> validasi radius
|--------------------------------------------------------------------------
*/

const cekLokasi = async () => {
    lokasiStatus.value = 'checking'
    errorMessage.value = ''

    try {
        const device = await getDevicePosition()
        devicePosition.value = device

        const response = await window.axios.post(route('pegawai.absensi.validasi-lokasi'), {
            latitude: device.lat,
            longitude: device.lng,
        })

        const result = response.data

        distanceMeters.value = result.distance_meters
        radiusMeters.value = result.radius_meters
        officeName.value = result.office.name

        lokasiStatus.value = result.valid ? 'valid' : 'invalid'

        await renderMap(result.office, device)
    } catch (err) {
        if (err?.response?.status === 404) {
            errorMessage.value = 'Lokasi kantor belum diatur oleh admin. Hubungi admin untuk mengatur titik lokasi & radius absensi.'
        } else {
            errorMessage.value = err.message || 'Terjadi kesalahan saat memvalidasi lokasi.'
        }
        lokasiStatus.value = 'error'
    }
}

onMounted(() => {
    cekLokasi()
})

onBeforeUnmount(() => {
    if (mapInstance) {
        mapInstance.remove()
        mapInstance = null
    }
})

/*
|--------------------------------------------------------------------------
| Lanjut setelah lokasi valid
|--------------------------------------------------------------------------
*/

const lanjut = () => {
    if (lokasiStatus.value !== 'valid' || !devicePosition.value) {
        return
    }

    emit('location-success', {
        latitude: devicePosition.value.lat,
        longitude: devicePosition.value.lng,
    })
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
                        Pastikan Anda berada di wilayah {{ officeName || 'kantor' }}.
                    </p>

                </div>

            </div>


            <!-- MAP (Leaflet + OpenStreetMap) -->

            <div class="map-container">

                <div ref="mapEl" class="leaflet-mount"></div>

                <div v-if="lokasiStatus === 'checking'" class="map-overlay">
                    <ArrowPathIcon class="spin" />
                    <span>Memuat peta...</span>
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
                        Mohon izinkan akses lokasi pada browser Anda.
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
                        Anda berada {{ distanceMeters }} m dari titik kantor
                        (radius diizinkan {{ radiusMeters }} m).
                    </span>

                </div>

            </div>


            <!-- INVALID (di luar radius) -->

            <div
                v-if="lokasiStatus === 'invalid'"
                class="location-status invalid"
            >

                <ExclamationTriangleIcon />

                <div>

                    <strong>
                        Anda berada di luar wilayah kantor
                    </strong>

                    <span>
                        Jarak Anda {{ distanceMeters }} m dari kantor, sedangkan
                        radius yang diizinkan hanya {{ radiusMeters }} m.
                        Mendekatlah ke lokasi kantor lalu cek ulang.
                    </span>

                </div>

            </div>


            <!-- ERROR -->

            <div
                v-if="lokasiStatus === 'error'"
                class="location-status invalid"
            >

                <ExclamationTriangleIcon />

                <div>

                    <strong>
                        Tidak dapat memvalidasi lokasi
                    </strong>

                    <span>
                        {{ errorMessage }}
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
                v-if="lokasiStatus !== 'checking'"
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

.leaflet-mount {
    width: 100%;
    height: 100%;
}

.map-overlay {
    position: absolute;
    inset: 0;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;

    background: #edf4f2;

    color: #6c7b7e;
    font-size: 11px;
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

.invalid {
    background: #fff5f0;
}

.location-status svg {
    width: 21px;
    flex-shrink: 0;
}

.checking svg {
    color: #20aa97;
}

.valid svg {
    color: #23aa69;
}

.invalid svg {
    color: #d16a3f;
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

</style>

<style>
/* Global (bukan scoped) karena marker device dibuat lewat L.divIcon Leaflet,
   yang me-render HTML di luar jangkauan <style scoped> komponen ini. */
.leaflet-device-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #1eaf68;
    border: 3px solid white;
    box-shadow: 0 0 0 4px rgba(30, 175, 104, .25);
}
</style>
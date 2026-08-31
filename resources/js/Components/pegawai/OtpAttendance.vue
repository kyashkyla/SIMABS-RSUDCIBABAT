<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { router } from '@inertiajs/vue3'

import {
    KeyIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    approvalType: {
        type: String,
        default: 'temporary',
    },
    deviceLocation: {
        type: Object,
        required: true,
    },
    isCheckOut: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['success'])

const otp = ref('')
const isLoadingOtp = ref(false)
const timer = ref(0)
let interval = null
let notifInterval = null

const formattedTimer = computed(() => {
    const minutes = Math.floor(timer.value / 60)
    const seconds = timer.value % 60
    return `${minutes}:${seconds.toString().padStart(2, '0')}`
})

const requestOtp = async () => {
    if (isLoadingOtp.value) return
    isLoadingOtp.value = true
    
    try {
        await window.axios.post(route('pegawai.absensi.otp.request'))
        alert('Kode OTP telah dikirimkan ke notifikasi Anda.')
        startTimer()
    } catch (err) {
        console.error(err)
        alert(err.response?.data?.message || 'Gagal meminta OTP.')
    } finally {
        isLoadingOtp.value = false
    }
}

const startTimer = () => {
    timer.value = 180 // 3 minutes
    if (interval) clearInterval(interval)
    if (notifInterval) clearInterval(notifInterval)
    
    interval = setInterval(() => {
        if (timer.value > 0) {
            timer.value--
        } else {
            clearInterval(interval)
            if (notifInterval) clearInterval(notifInterval)
        }
    }, 1000)

    // Reload notifications every 3 seconds
    notifInterval = setInterval(() => {
        if (timer.value > 0) {
            router.reload({
                only: ['notifications'],
                preserveState: true,
                preserveScroll: true
            })
        }
    }, 3000)
}

onMounted(() => {
    // Optionally trigger automatically on mount:
    // requestOtp()
})

onUnmounted(() => {
    if (interval) clearInterval(interval)
    if (notifInterval) clearInterval(notifInterval)
})

const verifikasiOtp = async () => {
    if (otp.value.length !== 6 || timer.value === 0) {
        return
    }

    try {
        const routeName = props.isCheckOut ? 'pegawai.absensi.pulang' : 'pegawai.absensi.simpan'
        const response = await window.axios.post(route(routeName), {
            latitude: props.deviceLocation.latitude,
            longitude: props.deviceLocation.longitude,
            method: 'otp',
            otp_code: otp.value,
        })
        emit('success', response.data.attendance)
    } catch (err) {
        console.error(err)
        alert(err.response?.data?.message || 'Verifikasi OTP gagal.')
    }
}

const handleOtpInput = (event) => {
    otp.value = event.target.value
        .replace(/[^a-zA-Z0-9]/g, '')
        .toUpperCase()
        .slice(0, 6)
}
</script>

<template>

    <div class="main-card">

        <div class="otp-icon">
            <KeyIcon />
        </div>


        <h2>
            Absensi dengan OTP
        </h2>


        <p>
            Anda telah mendapatkan persetujuan Admin.
            Masukkan kode OTP yang diberikan oleh sistem
            untuk melakukan absensi.
        </p>


        <!-- APPROVAL -->

        <div class="approval-box">

            <CheckCircleIcon />

            <div>

                <strong>

                    {{
                        props.approvalType === 'permanent'
                            ? 'Approval Permanen'
                            : 'Approval Sementara'
                    }}

                </strong>

                <span>

                    {{
                        props.approvalType === 'permanent'
                            ? 'Akses OTP Anda bersifat permanen.'
                            : 'Akses OTP Anda bersifat sementara. Face Recognition tetap dapat digunakan pada absensi berikutnya.'
                    }}

                </span>

            </div>

        </div>


        <!-- SYSTEM INFO -->

        <div class="system-box">

            <KeyIcon />

            <div>

                <strong>
                    OTP dari Sistem
                </strong>

                <span>
                    Kode OTP diberikan oleh sistem setelah
                    Admin menyetujui pengajuan Anda.
                </span>

            </div>

        </div>


        <!-- OTP -->

        <div v-if="timer > 0">
            <div class="otp-input">
                <input
                    :value="otp"
                    maxlength="6"
                    autocomplete="one-time-code"
                    placeholder="A7K29P"
                    @input="handleOtpInput"
                    :disabled="timer === 0"
                />
            </div>

            <p class="otp-hint">
                Masukkan 6 karakter berupa huruf dan angka. Sisa waktu: <strong>{{ formattedTimer }}</strong>
            </p>

            <button
                class="primary-button"
                @click="verifikasiOtp"
                :disabled="otp.length !== 6 || timer === 0"
            >
                <KeyIcon />
                Verifikasi OTP
            </button>
        </div>
        
        <div v-else>
            <button
                class="primary-button"
                style="background: #1e3a8a; margin-top: 20px;"
                @click="requestOtp"
                :disabled="isLoadingOtp"
            >
                <PaperAirplaneIcon />
                {{ otp.length > 0 ? 'Kirim Ulang OTP' : 'Minta Kode OTP' }}
            </button>
        </div>

    </div>

</template>

<style scoped>

.main-card {
    max-width: 760px;
    margin: auto;

    padding: 35px;

    background: white;

    border: 1px solid #e2eeee;
    border-radius: 16px;

    text-align: center;

    box-shadow: 0 4px 16px rgba(30,100,90,.04);
}

.otp-icon {
    width: 60px;
    height: 60px;

    margin: 0 auto 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #e9f8f5;

    border-radius: 50%;
}

.otp-icon svg {
    width: 28px;

    color: #20a995;
}

.main-card h2 {
    margin: 0;

    color: #354347;

    font-size: 19px;
}

.main-card > p {
    max-width: 480px;

    margin: 8px auto 18px;

    color: #899598;

    font-size: 10px;

    line-height: 1.6;
}

.approval-box {
    max-width: 450px;

    margin: auto;

    padding: 12px;

    display: flex;

    gap: 9px;

    text-align: left;

    background: #effaf5;

    border-radius: 9px;
}

.approval-box > svg {
    width: 20px;

    color: #27aa68;

    flex-shrink: 0;
}

.approval-box strong {
    display: block;

    color: #405054;

    font-size: 9px;
}

.approval-box span {
    display: block;

    margin-top: 2px;

    color: #899598;

    font-size: 8px;

    line-height: 1.5;
}

.system-box {
    max-width: 450px;

    margin: 10px auto 0;

    padding: 11px;

    display: flex;

    gap: 8px;

    text-align: left;

    background: #f7faf9;

    border-radius: 9px;
}

.system-box > svg {
    width: 18px;

    color: #20a995;

    flex-shrink: 0;
}

.system-box strong {
    display: block;

    color: #4b595c;

    font-size: 9px;
}

.system-box span {
    display: block;

    margin-top: 2px;

    color: #899598;

    font-size: 8px;

    line-height: 1.5;
}

.otp-input {
    margin-top: 18px;
}

.otp-input input {
    width: 220px;
    height: 52px;

    box-sizing: border-box;

    text-align: center;

    letter-spacing: 7px;

    border: 1px solid #dce9e7;

    border-radius: 9px;

    outline: none;

    color: #20a995;

    font-size: 20px;

    font-weight: 700;

    text-transform: uppercase;
}

.otp-input input:focus {
    border-color: #20a995;

    box-shadow: 0 0 0 3px rgba(32,169,149,.08);
}

.otp-hint {
    margin-top: 7px !important;

    margin-bottom: 0 !important;

    font-size: 8px !important;

    color: #9aa4a5 !important;
}

.primary-button {
    width: 100%;
    height: 43px;

    margin-top: 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

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

.primary-button svg {
    width: 17px;
}

.primary-button:disabled {
    opacity: .5;

    cursor: not-allowed;
}

</style>
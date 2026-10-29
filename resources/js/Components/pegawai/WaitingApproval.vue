<script setup>
import { onMounted, onUnmounted } from 'vue'
import { toast } from '@/lib/toast'
import {
    ClockIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

const emit = defineEmits(['approved', 'rejected'])

const POLL_INTERVAL_MS = 5000

let timer = null
let checking = false

/*
| Cek keputusan admin ke server. Begitu disetujui -> emit 'approved'
| (temporary / permanent), kalau ditolak -> emit 'rejected'.
*/
const checkStatus = async () => {
    if (checking) return
    checking = true

    try {
        const { data } = await window.axios.get(route('pegawai.absensi.status-approval'))

        if (data.status === 'approved') {
            stopPolling()
            emit('approved', data.type)
        } else if (data.status === 'rejected') {
            stopPolling()
            toast.error(
                data.message || 'Pengajuan absensi alternatif Anda ditolak admin.',
                { title: 'Pengajuan Ditolak' }
            )
            emit('rejected')
        }
    } catch (err) {
        // Gagal sesaat (mis. koneksi putus) -> abaikan, coba lagi di polling berikutnya.
        console.error(err)
    } finally {
        checking = false
    }
}

const stopPolling = () => {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

onMounted(() => {
    checkStatus()
    timer = setInterval(checkStatus, POLL_INTERVAL_MS)
})

onUnmounted(stopPolling)
</script>

<template>

    <div class="main-card">

        <div class="waiting-icon">
            <ClockIcon />
        </div>

        <h2>
            Menunggu Persetujuan Admin
        </h2>

        <p>
            Pengajuan absensi alternatif Anda telah dikirim
            dan sedang menunggu verifikasi dari Admin.
        </p>


        <!-- STATUS -->

        <div class="status">

            <div class="status-icon">
                <ClockIcon />
            </div>

            <div>

                <span>
                    Status Pengajuan
                </span>

                <strong>
                    Menunggu Approval
                </strong>

                <small>
                    Admin akan memeriksa pengajuan Anda.
                </small>

            </div>

        </div>


        <!-- INFO -->

        <div class="info-box">

            <ShieldCheckIcon />

            <div>

                <strong>
                    Setelah disetujui Admin
                </strong>

                <p>
                    Anda akan melakukan deteksi lokasi kembali.
                    Setelah lokasi berhasil diverifikasi, sistem
                    akan mengarahkan Anda ke absensi menggunakan OTP.
                </p>

            </div>

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

.waiting-icon {
    width: 65px;
    height: 65px;

    margin: 0 auto 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #fff5d8;

    border-radius: 50%;
}

.waiting-icon svg {
    width: 30px;
    color: #d49a00;
}

.main-card h2 {
    margin: 0;

    color: #354347;

    font-size: 19px;
}

.main-card > p {
    max-width: 500px;

    margin: 8px auto 20px;

    color: #899598;

    font-size: 10px;

    line-height: 1.6;
}

.status {
    max-width: 500px;

    margin: auto;

    padding: 14px;

    display: flex;
    align-items: center;
    gap: 10px;

    background: #f7faf9;

    border-radius: 10px;

    text-align: left;
}

.status-icon {
    width: 36px;
    height: 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #fff3d1;

    border-radius: 9px;
}

.status-icon svg {
    width: 18px;
    color: #d49a00;
}

.status span {
    display: block;

    color: #899598;

    font-size: 9px;
}

.status strong {
    display: block;

    margin-top: 3px;

    color: #d49a00;

    font-size: 10px;
}

.status small {
    display: block;

    margin-top: 3px;

    color: #9aa4a5;

    font-size: 8px;
}

.info-box {
    max-width: 500px;

    margin: 15px auto 0;

    padding: 13px;

    display: flex;

    gap: 9px;

    text-align: left;

    background: #effaf5;

    border-radius: 9px;
}

.info-box > svg {
    width: 20px;

    flex-shrink: 0;

    color: #27aa68;
}

.info-box strong {
    display: block;

    color: #405054;

    font-size: 9px;
}

.info-box p {
    margin-top: 3px;

    color: #899598;

    font-size: 8px;

    line-height: 1.5;
}

</style>
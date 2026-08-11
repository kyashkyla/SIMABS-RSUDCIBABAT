<script setup>
import {
    ClockIcon,
    ShieldCheckIcon,
} from '@heroicons/vue/24/outline'

const emit = defineEmits(['approved'])

const approveSementara = () => {
    emit('approved', 'temporary')
}

const approvePermanen = () => {
    emit('approved', 'permanent')
}
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


        <!-- SIMULASI -->
        <!--
            SEMENTARA UNTUK TESTING FRONTEND.
            Nanti bagian ini DIHAPUS ketika sudah terhubung
            dengan sistem Admin/database.
        -->

        <div class="simulation">

            <small>
                SIMULASI FRONTEND
            </small>

            <p>
                Bagian ini hanya untuk mengetes response
                approval dari Admin.
            </p>

            <div class="simulation-buttons">

                <button
                    type="button"
                    @click="approveSementara"
                >
                    Test Approval Sementara
                </button>

                <button
                    type="button"
                    @click="approvePermanen"
                >
                    Test Approval Permanen
                </button>

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

.simulation {
    max-width: 500px;

    margin: 20px auto 0;

    padding: 12px;

    border: 1px dashed #ccdeda;

    border-radius: 9px;

    background: #f9fcfb;
}

.simulation small {
    color: #20a995;

    font-size: 8px;
    font-weight: 700;
}

.simulation p {
    margin: 5px 0 10px;

    color: #899598;

    font-size: 8px;
}

.simulation-buttons {
    display: flex;

    justify-content: center;

    gap: 7px;

    flex-wrap: wrap;
}

.simulation button {
    padding: 7px 10px;

    border: none;

    border-radius: 6px;

    background: #20ad98;

    color: white;

    font-size: 8px;

    cursor: pointer;
}

</style>
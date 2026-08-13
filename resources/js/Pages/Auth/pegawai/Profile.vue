<script setup>
import PegawaiLayout from './PegawaiLayout.vue'
import { Link } from '@inertiajs/vue3'

import {
    UserCircleIcon,
    IdentificationIcon,
    BriefcaseIcon,
    Cog6ToothIcon,
} from '@heroicons/vue/24/outline'


const props = defineProps({

    pegawai: {
        type: Object,
        default: () => ({}),
    },

})

</script>


<template>

    <PegawaiLayout>

        <div class="page-container">


            <!-- ================================================= -->
            <!-- HEADER -->
            <!-- ================================================= -->

            <div class="page-header">

                <div>

                    <h1>
                        Profil Saya
                    </h1>

                    <p>
                        Informasi profil dan data kepegawaian Anda
                    </p>

                </div>


                <!-- PENGATURAN -->

                <Link
                    :href="
                        route('pegawai.pengaturan')
                    "
                    class="settings-button"
                >

                    <Cog6ToothIcon />

                    Pengaturan

                </Link>

            </div>


            <!-- ================================================= -->
            <!-- PROFILE CARD -->
            <!-- ================================================= -->

            <div class="profile-card">


                <!-- ================================================= -->
                <!-- PROFILE HEADER -->
                <!-- ================================================= -->

                <div class="profile-top">


                    <!-- FOTO -->

                    <div class="profile-photo">

                        <img
                            v-if="pegawai.foto"
                            :src="pegawai.foto"
                            alt="Foto profil"
                        />

                        <UserCircleIcon
                            v-else
                        />

                    </div>


                    <!-- IDENTITAS -->

                    <div class="profile-identity">

                        <h2>
                            {{
                                pegawai.nama
                                    || 'Nama Pegawai'
                            }}
                        </h2>

                        <p>
                            {{
                                pegawai.jabatan
                                    || 'Pegawai'
                            }}
                        </p>

                        <span
                            class="status-badge"
                        >
                            {{
                                pegawai.status
                                    || 'Aktif'
                            }}
                        </span>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- INFORMASI PERSONAL -->
                <!-- ================================================= -->

                <div class="section">


                    <div class="section-title">

                        <div class="section-icon">

                            <UserCircleIcon />

                        </div>

                        <div>

                            <h3>
                                Informasi Personal
                            </h3>

                            <p>
                                Informasi dasar pegawai
                            </p>

                        </div>

                    </div>


                    <div class="info-grid">


                        <!-- NAMA -->

                        <div class="info-item">

                            <span class="label">
                                Nama Lengkap
                            </span>

                            <strong>
                                {{
                                    pegawai.nama || '-'
                                }}
                            </strong>

                        </div>


                        <!-- NIP -->

                        <div class="info-item">

                            <span class="label">
                                NIP
                            </span>

                            <strong>
                                {{
                                    pegawai.nip || '-'
                                }}
                            </strong>

                        </div>


                        <!-- EMAIL -->

                        <div class="info-item">

                            <span class="label">
                                Email
                            </span>

                            <strong>
                                {{
                                    pegawai.email || '-'
                                }}
                            </strong>

                        </div>


                        <!-- NO HP -->

                        <div class="info-item">

                            <span class="label">
                                Nomor Telepon
                            </span>

                            <strong>
                                {{
                                    pegawai.no_hp || '-'
                                }}
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- DATA KEPEGAWAIAN -->
                <!-- ================================================= -->

                <div class="section">


                    <div class="section-title">

                        <div class="section-icon">

                            <BriefcaseIcon />

                        </div>

                        <div>

                            <h3>
                                Data Kepegawaian
                            </h3>

                            <p>
                                Informasi pekerjaan dan unit kerja
                            </p>

                        </div>

                    </div>


                    <div class="info-grid">


                        <!-- JABATAN -->

                        <div class="info-item">

                            <span class="label">
                                Jabatan
                            </span>

                            <strong>
                                {{
                                    pegawai.jabatan || '-'
                                }}
                            </strong>

                        </div>


                        <!-- UNIT -->

                        <div class="info-item">

                            <span class="label">
                                Unit / Bagian
                            </span>

                            <strong>
                                {{
                                    pegawai.unit || '-'
                                }}
                            </strong>

                        </div>


                        <!-- STATUS -->

                        <div class="info-item">

                            <span class="label">
                                Status Kepegawaian
                            </span>

                            <strong>
                                {{
                                    pegawai.status || 'Aktif'
                                }}
                            </strong>

                        </div>


                        <!-- ID -->

                        <div class="info-item">

                            <span class="label">
                                ID Pegawai
                            </span>

                            <strong>
                                {{
                                    pegawai.id || '-'
                                }}
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- AKUN -->
                <!-- ================================================= -->

                <div class="section last-section">


                    <div class="section-title">

                        <div class="section-icon">

                            <IdentificationIcon />

                        </div>

                        <div>

                            <h3>
                                Informasi Akun
                            </h3>

                            <p>
                                Informasi akun yang digunakan untuk masuk
                            </p>

                        </div>

                    </div>


                    <div class="account-box">


                        <div>

                            <span>
                                Username
                            </span>

                            <strong>
                                {{
                                    pegawai.username || '-'
                                }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Email Akun
                            </span>

                            <strong>
                                {{
                                    pegawai.email || '-'
                                }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </PegawaiLayout>

</template>


<style scoped>

.page-container {
    max-width: 1100px;
    margin: auto;
}


/* =========================================
   HEADER
========================================= */

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 20px;
}

.page-header h1 {
    margin: 0;

    color: #263238;

    font-size: 24px;
    font-weight: 700;
}

.page-header p {
    margin: 5px 0 0;

    color: #7b898c;

    font-size: 13px;
}


/* =========================================
   SETTINGS BUTTON
========================================= */

.settings-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    height: 38px;

    padding: 0 14px;

    border-radius: 8px;

    background: #20ad98;

    color: white;

    font-size: 10px;
    font-weight: 600;

    text-decoration: none;

    transition: .2s;
}

.settings-button:hover {
    background: #199d89;
}

.settings-button svg {
    width: 16px;
}


/* =========================================
   PROFILE CARD
========================================= */

.profile-card {
    overflow: hidden;

    background: white;

    border: 1px solid #e2eeee;

    border-radius: 16px;

    box-shadow:
        0 3px 12px rgba(30, 100, 90, .03);
}


/* =========================================
   PROFILE TOP
========================================= */

.profile-top {
    display: flex;
    align-items: center;

    gap: 18px;

    padding: 25px;

    border-bottom: 1px solid #edf3f2;
}

.profile-photo {
    width: 90px;
    height: 90px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    overflow: hidden;

    background: #e9f8f5;

    border-radius: 50%;
}

.profile-photo img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.profile-photo svg {
    width: 52px;

    color: #20ad98;
}

.profile-identity h2 {
    margin: 0;

    color: #263238;

    font-size: 20px;
    font-weight: 700;
}

.profile-identity p {
    margin: 5px 0 8px;

    color: #7b898c;

    font-size: 11px;
}

.status-badge {
    display: inline-flex;

    padding: 5px 10px;

    border-radius: 20px;

    background: #daf6e4;

    color: #27a667;

    font-size: 9px;
    font-weight: 600;
}


/* =========================================
   SECTION
========================================= */

.section {
    padding: 23px 25px;

    border-bottom: 1px solid #edf3f2;
}

.last-section {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;

    gap: 10px;

    margin-bottom: 18px;
}

.section-icon {
    width: 38px;
    height: 38px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    background: #eaf8f5;

    border-radius: 10px;
}

.section-icon svg {
    width: 19px;

    color: #20aa97;
}

.section-title h3 {
    margin: 0;

    color: #374447;

    font-size: 14px;
}

.section-title p {
    margin: 3px 0 0;

    color: #899598;

    font-size: 9px;
}


/* =========================================
   INFORMATION
========================================= */

.info-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 12px;
}

.info-item {
    padding: 13px;

    background: #f8fbfa;

    border: 1px solid #edf3f1;

    border-radius: 9px;
}

.info-item .label {
    display: block;

    margin-bottom: 5px;

    color: #899598;

    font-size: 9px;
}

.info-item strong {
    display: block;

    color: #455356;

    font-size: 11px;

    font-weight: 600;
}


/* =========================================
   ACCOUNT
========================================= */

.account-box {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 12px;
}

.account-box > div {
    padding: 13px;

    background: #f8fbfa;

    border: 1px solid #edf3f1;

    border-radius: 9px;
}

.account-box span {
    display: block;

    margin-bottom: 5px;

    color: #899598;

    font-size: 9px;
}

.account-box strong {
    color: #455356;

    font-size: 11px;
}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 700px) {

    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .profile-top {
        align-items: flex-start;
    }

    .info-grid,
    .account-box {
        grid-template-columns: 1fr;
    }

}

</style>
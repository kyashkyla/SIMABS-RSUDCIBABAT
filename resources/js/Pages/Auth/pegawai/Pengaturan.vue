<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { useForm } from '@inertiajs/vue3'

import PegawaiLayout from './PegawaiLayout.vue'

import {
    UserCircleIcon,
    LockClosedIcon,
    BellIcon,
    ShieldCheckIcon,
    PencilIcon,
    XMarkIcon,
    CheckIcon,
    KeyIcon,
} from '@heroicons/vue/24/outline'


/* =========================================================
   PROPS (dari Pegawai\ProfileController@edit)
========================================================= */

const props = defineProps({

    pegawai: {
        type: Object,
        default: () => ({}),
    },

})


/* =========================================================
   PROFILE
========================================================= */

const isEditingProfile = ref(false)

// Foto yang sedang tersimpan di server (dari database)
const defaultPhoto = ref(props.pegawai.foto || '')

// Foto yang sedang ditampilkan (bisa berubah kalau user pilih foto baru)
const photoPreview = ref(defaultPhoto.value)

const profileForm = useForm({
    email: props.pegawai.email || '',
    photo: null,
})


/* =========================================================
   PASSWORD
========================================================= */

const isChangingPassword = ref(false)

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})


/* =========================================================
   NOTIFICATION (belum ada tabel/kolom di database,
   jadi masih preferensi lokal di layar saja)
========================================================= */

const pengingatAbsensi = ref(true)
const statusPengajuan = ref(true)


/* =========================================================
   PROFILE PHOTO
========================================================= */

const pilihFotoProfil = (event) => {

    const file = event.target.files[0]

    if (!file) {
        return
    }

    // Batasi hanya gambar
    if (!file.type.startsWith('image/')) {
        return
    }

    // Batasi ukuran 2 MB
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran foto maksimal 2 MB.')
        return
    }

    profileForm.photo = file

    photoPreview.value = URL.createObjectURL(file)
}


const hapusFotoBaru = () => {

    if (photoPreview.value && photoPreview.value !== defaultPhoto.value) {
        URL.revokeObjectURL(photoPreview.value)
    }

    profileForm.photo = null

    photoPreview.value = defaultPhoto.value
}


/* =========================================================
   PROFILE ACTION
========================================================= */

const bukaEditProfil = () => {

    profileForm.email = props.pegawai.email || ''

    isEditingProfile.value = true
}


const batalEditProfil = () => {

    isEditingProfile.value = false

    profileForm.clearErrors()
    profileForm.email = props.pegawai.email || ''

    hapusFotoBaru()
}


const simpanProfil = () => {

    profileForm.post(route('pegawai.pengaturan.update'), {
        forceFormData: true,
        preserveScroll: true,

        onSuccess: () => {

            isEditingProfile.value = false

            // Foto baru (kalau ada) sudah tersimpan, jadikan itu foto default
            defaultPhoto.value = photoPreview.value

            profileForm.photo = null
        },
    })
}


/* =========================================================
   PASSWORD
========================================================= */

const bukaUbahPassword = () => {

    isChangingPassword.value = true

    passwordForm.reset()
    passwordForm.clearErrors()
}


const batalUbahPassword = () => {

    isChangingPassword.value = false

    passwordForm.reset()
    passwordForm.clearErrors()
}


const simpanPassword = () => {

    passwordForm.put(route('password.update'), {
        preserveScroll: true,

        onSuccess: () => {
            batalUbahPassword()
            alert('Password berhasil diubah.')
        },

        onError: () => {
            // Demi keamanan, Laravel mengosongkan field password kalau gagal
            passwordForm.reset('password', 'password_confirmation')
        },
    })
}


/* =========================================================
   CLEANUP OBJECT URL
========================================================= */

onBeforeUnmount(() => {

    if (
        photoPreview.value &&
        photoPreview.value !== defaultPhoto.value
    ) {
        URL.revokeObjectURL(photoPreview.value)
    }

})
</script>


<template>

    <PegawaiLayout>

        <div class="page-container">


            <!-- =====================================================
                 HEADER
            ====================================================== -->

            <div class="page-header">

                <div>

                    <h1>
                        Pengaturan
                    </h1>

                    <p>
                        Kelola informasi akun dan preferensi Anda
                    </p>

                </div>

            </div>


            <!-- =====================================================
                 PROFILE
            ====================================================== -->

            <div class="settings-card profile-card">

                <div class="section-header">

                    <div class="section-icon">
                        <UserCircleIcon />
                    </div>

                    <div>

                        <h2>
                            Informasi Profil
                        </h2>

                        <p>
                            Informasi dasar akun pegawai
                        </p>

                    </div>

                </div>


                <!-- ================================
                     MODE VIEW
                ================================= -->

                <div
                    v-if="!isEditingProfile"
                    class="profile-view"
                >

                    <!-- FOTO -->

                    <div class="profile-photo-area">

                        <div class="profile-photo">

                            <img
                                v-if="pegawai.foto"
                                :src="pegawai.foto"
                                alt="Foto Profil"
                            />

                            <UserCircleIcon
                                v-else
                            />

                        </div>

                    </div>


                    <!-- DATA -->

                    <div class="profile-information">

                        <div class="information-item">

                            <span>
                                Nama Lengkap
                            </span>

                            <strong>
                                {{ pegawai.nama || '-' }}
                            </strong>

                        </div>


                        <div class="information-item">

                            <span>
                                Username
                            </span>

                            <strong>
                                {{ pegawai.username || '-' }}
                            </strong>

                        </div>


                        <div class="information-item">

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ pegawai.email || '-' }}
                            </strong>

                        </div>


                        <div class="information-item">

                            <span>
                                NIP
                            </span>

                            <strong>
                                {{ pegawai.nip || '-' }}
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- ================================
                     EDIT MODE
                ================================= -->

                <div
                    v-else
                    class="profile-edit"
                >

                    <!-- FOTO -->

                    <div class="edit-photo-area">

                        <label class="profile-photo large photo-upload">

                            <img
                                v-if="photoPreview"
                                :src="photoPreview"
                                alt="Foto Profil"
                            />

                            <UserCircleIcon
                                v-else
                            />

                            <div class="photo-hover-overlay">

                                <PencilIcon />

                            </div>

                            <input
                                type="file"
                                accept="image/*"
                                class="photo-input-hidden"
                                @change="pilihFotoProfil"
                            />

                        </label>


                        <div class="photo-description">

                            <strong>
                                Foto Profil
                            </strong>

                            <span>
                                JPG, PNG atau WEBP
                            </span>

                            <span>
                                Maksimal 2 MB
                            </span>

                            <span
                                v-if="profileForm.errors.photo"
                                class="field-error"
                            >
                                {{ profileForm.errors.photo }}
                            </span>


                            <button
                                v-if="profileForm.photo"
                                type="button"
                                class="remove-photo"
                                @click="hapusFotoBaru"
                            >
                                Hapus foto baru
                            </button>

                        </div>

                    </div>


                    <!-- FORM -->

                    <div class="form-grid">

                        <div class="form-group">

                            <label>
                                Nama Lengkap
                            </label>

                            <input
                                :value="pegawai.nama"
                                type="text"
                                disabled
                            />

                            <small>
                                Nama dikelola oleh administrator.
                            </small>

                        </div>


                        <div class="form-group">

                            <label>
                                Username
                            </label>

                            <input
                                :value="pegawai.username"
                                type="text"
                                disabled
                            />

                            <small>
                                Username tidak dapat diubah.
                            </small>

                        </div>


                        <div class="form-group">

                            <label>
                                Email
                            </label>

                            <input
                                v-model="profileForm.email"
                                type="email"
                                placeholder="Masukkan email"
                            />

                            <small
                                v-if="profileForm.errors.email"
                                class="field-error"
                            >
                                {{ profileForm.errors.email }}
                            </small>

                        </div>


                        <div class="form-group">

                            <label>
                                NIP
                            </label>

                            <input
                                :value="pegawai.nip"
                                type="text"
                                disabled
                            />

                            <small>
                                NIP dikelola oleh administrator.
                            </small>

                        </div>

                    </div>


                    <!-- ACTION -->

                    <div class="form-actions">

                        <button
                            type="button"
                            class="cancel-button"
                            @click="batalEditProfil"
                        >

                            <XMarkIcon />

                            Batal

                        </button>


                        <button
                            type="button"
                            class="save-button"
                            :disabled="profileForm.processing"
                            @click="simpanProfil"
                        >

                            <CheckIcon />

                            {{
                                profileForm.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Perubahan'
                            }}

                        </button>

                    </div>

                </div>


                <!-- EDIT BUTTON -->

                <button
                    v-if="!isEditingProfile"
                    type="button"
                    class="outline-button"
                    @click="bukaEditProfil"
                >

                    <PencilIcon />

                    Edit Profil

                </button>

            </div>



            <!-- =====================================================
                 TWO COLUMN
            ====================================================== -->

            <div class="bottom-grid">


                <!-- =================================================
                     PASSWORD
                ================================================== -->

                <div class="settings-card">

                    <div class="section-header">

                        <div class="section-icon">

                            <LockClosedIcon />

                        </div>

                        <div>

                            <h2>
                                Keamanan Akun
                            </h2>

                            <p>
                                Kelola keamanan akun pegawai
                            </p>

                        </div>

                    </div>


                    <!-- VIEW -->

                    <div
                        v-if="!isChangingPassword"
                        class="security-view"
                    >

                        <div class="security-icon">

                            <KeyIcon />

                        </div>

                        <div class="security-information">

                            <strong>
                                Password Akun
                            </strong>

                            <span>
                                Password terakhir diperbarui secara aman.
                            </span>

                        </div>


                        <button
                            type="button"
                            class="outline-button small"
                            @click="bukaUbahPassword"
                        >

                            Ubah Password

                        </button>

                    </div>


                    <!-- CHANGE PASSWORD -->

                    <div
                        v-else
                        class="password-form"
                    >

                        <div class="form-group">

                            <label>
                                Password Saat Ini
                            </label>

                            <input
                                v-model="passwordForm.current_password"
                                type="password"
                                placeholder="Masukkan password saat ini"
                            />

                            <small
                                v-if="passwordForm.errors.current_password"
                                class="field-error"
                            >
                                {{ passwordForm.errors.current_password }}
                            </small>

                        </div>


                        <div class="form-group">

                            <label>
                                Password Baru
                            </label>

                            <input
                                v-model="passwordForm.password"
                                type="password"
                                placeholder="Minimal 8 karakter"
                            />

                            <small
                                v-if="passwordForm.errors.password"
                                class="field-error"
                            >
                                {{ passwordForm.errors.password }}
                            </small>

                        </div>


                        <div class="form-group">

                            <label>
                                Konfirmasi Password Baru
                            </label>

                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                placeholder="Ulangi password baru"
                            />

                        </div>


                        <div class="password-note">

                            <LockClosedIcon />

                            <span>
                                Jangan bagikan password kepada siapapun.
                            </span>

                        </div>


                        <div class="form-actions">

                            <button
                                type="button"
                                class="cancel-button"
                                @click="batalUbahPassword"
                            >

                                <XMarkIcon />

                                Batal

                            </button>


                            <button
                                type="button"
                                class="save-button"
                                :disabled="passwordForm.processing"
                                @click="simpanPassword"
                            >

                                <CheckIcon />

                                {{
                                    passwordForm.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Password'
                                }}

                            </button>

                        </div>

                    </div>

                </div>



                <!-- =================================================
                     NOTIFICATION
                ================================================== -->

                <div class="settings-card">

                    <div class="section-header">

                        <div class="section-icon">

                            <BellIcon />

                        </div>

                        <div>

                            <h2>
                                Notifikasi
                            </h2>

                            <p>
                                Atur notifikasi sistem
                            </p>

                        </div>

                    </div>


                    <div class="toggle-item">

                        <div>

                            <strong>
                                Pengingat Absensi
                            </strong>

                            <span>
                                Ingatkan saya untuk melakukan absensi
                            </span>

                        </div>


                        <button
                            type="button"
                            class="toggle"
                            :class="{ active: pengingatAbsensi }"
                            @click="pengingatAbsensi = !pengingatAbsensi"
                        >

                            <div></div>

                        </button>

                    </div>


                    <div class="toggle-item">

                        <div>

                            <strong>
                                Status Pengajuan
                            </strong>

                            <span>
                                Terima pemberitahuan hasil pengajuan
                            </span>

                        </div>


                        <button
                            type="button"
                            class="toggle"
                            :class="{ active: statusPengajuan }"
                            @click="statusPengajuan = !statusPengajuan"
                        >

                            <div></div>

                        </button>

                    </div>

                </div>



                <!-- =================================================
                     METODE ABSENSI
                ================================================== -->

                <div class="settings-card">

                    <div class="section-header">

                        <div class="section-icon">

                            <ShieldCheckIcon />

                        </div>

                        <div>

                            <h2>
                                Metode Absensi
                            </h2>

                            <p>
                                Metode autentikasi yang tersedia
                            </p>

                        </div>

                    </div>


                    <div class="method-status">

                        <div>

                            <strong>
                                Face Recognition
                            </strong>

                            <span>
                                Metode utama absensi
                            </span>

                        </div>

                        <div class="enabled">
                            Aktif
                        </div>

                    </div>


                    <div class="method-status">

                        <div>

                            <strong>
                                OTP
                            </strong>

                            <span>
                                Digunakan sebagai metode alternatif
                            </span>

                        </div>

                        <div class="enabled">
                            Aktif
                        </div>

                    </div>


                    <div class="method-info">

                        <ShieldCheckIcon />

                        <span>
                            Metode absensi dikelola oleh sistem
                            dan administrator.
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </PegawaiLayout>

</template>


<style scoped>

/* =========================================================
   PAGE
========================================================= */

.page-container {
    max-width: 1100px;
    margin: auto;
}

.page-header {
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


/* =========================================================
   CARD
========================================================= */

.settings-card {
    padding: 22px;

    background: white;

    border: 1px solid #e2eeee;

    border-radius: 15px;

    box-shadow: 0 3px 12px rgba(30, 100, 90, .025);
}

.profile-card {
    margin-bottom: 18px;
}


/* =========================================================
   HEADER
========================================================= */

.section-header {
    display: flex;

    align-items: center;

    gap: 10px;

    margin-bottom: 20px;
}

.section-icon {
    display: flex;

    align-items: center;
    justify-content: center;

    width: 40px;
    height: 40px;

    border-radius: 11px;

    background: #eaf8f5;
}

.section-icon svg {
    width: 20px;

    color: #20aa97;
}

.section-header h2 {
    margin: 0;

    color: #374447;

    font-size: 14px;
}

.section-header p {
    margin: 3px 0 0;

    color: #899598;

    font-size: 9px;
}


/* =========================================================
   PROFILE VIEW
========================================================= */

.profile-view {
    display: flex;

    align-items: center;

    gap: 25px;

    padding: 5px 0 20px;
}

.profile-photo-area {
    flex-shrink: 0;
}

.profile-photo {
    position: relative;

    width: 105px;
    height: 105px;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow: hidden;

    border-radius: 50%;

    background: #eaf8f5;

    border: 4px solid #f2faf8;
}

.profile-photo svg {
    width: 55px;

    color: #20aa97;
}

.profile-photo img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.profile-information {
    flex: 1;

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 18px 30px;
}

.information-item span {
    display: block;

    margin-bottom: 5px;

    color: #899598;

    font-size: 9px;
}

.information-item strong {
    color: #39484b;

    font-size: 11px;

    font-weight: 600;
}


/* =========================================================
   PROFILE EDIT
========================================================= */

.profile-edit {
    padding-top: 3px;
}

.edit-photo-area {
    display: flex;

    align-items: center;

    gap: 15px;

    margin-bottom: 22px;

    padding-bottom: 18px;

    border-bottom: 1px solid #edf2f2;
}

.profile-photo.large {
    width: 90px;
    height: 90px;

    border-width: 3px;
}

.profile-photo.large svg {
    width: 48px;
}

/* Foto profil di mode edit: bisa diklik, dan saat di-hover
   muncul overlay gelap + ikon pensil di tengah */

.photo-upload {
    display: flex;

    cursor: pointer;
}

.photo-hover-overlay {
    position: absolute;

    inset: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: rgba(20, 40, 38, .55);

    opacity: 0;

    transition: opacity .2s;
}

.photo-upload:hover .photo-hover-overlay,
.photo-upload:focus-within .photo-hover-overlay {
    opacity: 1;
}

.photo-hover-overlay svg {
    width: 26px;

    color: white;
}

.photo-input-hidden {
    display: none;
}

.photo-description strong {
    display: block;

    color: #435154;

    font-size: 11px;
}

.photo-description span {
    display: block;

    margin-top: 3px;

    color: #899598;

    font-size: 8px;
}

.remove-photo {
    margin-top: 7px;

    padding: 0;

    border: none;

    background: none;

    color: #d46a6a;

    font-size: 8px;

    cursor: pointer;
}


/* =========================================================
   FORM
========================================================= */

.form-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 14px 18px;
}

.form-group {
    margin-bottom: 13px;
}

.form-group label {
    display: block;

    margin-bottom: 6px;

    color: #586568;

    font-size: 10px;

    font-weight: 600;
}

.form-group input {
    width: 100%;

    height: 38px;

    box-sizing: border-box;

    padding: 0 10px;

    border: 1px solid #dfeaea;

    border-radius: 8px;

    outline: none;

    color: #3f4d50;

    font-size: 10px;

    transition: .2s;
}

.form-group input:focus {
    border-color: #22ad98;

    box-shadow: 0 0 0 3px rgba(34, 173, 152, .08);
}

.form-group input:disabled {
    background: #f5f8f7;

    color: #8a9698;

    cursor: not-allowed;
}

.form-group small {
    display: block;

    margin-top: 4px;

    color: #9aa5a7;

    font-size: 8px;
}

.field-error {
    color: #d46a6a !important;
}


/* =========================================================
   ACTION BUTTON
========================================================= */

.form-actions {
    display: flex;

    justify-content: flex-end;

    gap: 8px;

    margin-top: 8px;
}

.save-button,
.cancel-button,
.outline-button {
    height: 36px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 6px;

    padding: 0 13px;

    border-radius: 8px;

    font-size: 9px;

    font-weight: 600;

    cursor: pointer;

    transition: .2s;
}

.save-button {
    border: none;

    background: #20ad98;

    color: white;
}

.save-button:hover {
    background: #1c9d89;
}

.save-button:disabled {
    background: #8fd2c6;

    cursor: not-allowed;
}

.cancel-button {
    border: 1px solid #dfe9e7;

    background: white;

    color: #687679;
}

.cancel-button:hover {
    background: #f7faf9;
}

.save-button svg,
.cancel-button svg,
.outline-button svg {
    width: 14px;
}

.outline-button {
    border: 1px solid #d7e7e4;

    background: white;

    color: #20a995;
}

.outline-button:hover {
    background: #f2faf8;
}

.outline-button.small {
    height: 32px;

    padding: 0 11px;

    flex-shrink: 0;
}


/* =========================================================
   BOTTOM GRID
========================================================= */

.bottom-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 18px;
}


/* =========================================================
   SECURITY
========================================================= */

.security-view {
    display: flex;

    align-items: center;

    gap: 12px;

    padding: 5px 0;
}

.security-icon {
    width: 38px;
    height: 38px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    border-radius: 9px;

    background: #f4f8f7;
}

.security-icon svg {
    width: 18px;

    color: #20aa97;
}

.security-information {
    flex: 1;
}

.security-information strong {
    display: block;

    color: #485558;

    font-size: 10px;
}

.security-information span {
    display: block;

    margin-top: 3px;

    color: #899598;

    font-size: 8px;
}


/* =========================================================
   PASSWORD
========================================================= */

.password-form {
    padding-top: 3px;
}

.password-note {
    display: flex;

    align-items: center;

    gap: 6px;

    margin: 3px 0 13px;

    padding: 9px;

    border-radius: 7px;

    background: #f7faf9;

    color: #899598;

    font-size: 8px;
}

.password-note svg {
    width: 14px;

    color: #20aa97;
}


/* =========================================================
   TOGGLE
========================================================= */

.toggle-item {
    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 13px 0;

    border-bottom: 1px solid #edf2f2;
}

.toggle-item:last-child {
    border-bottom: none;
}

.toggle-item strong {
    display: block;

    color: #485558;

    font-size: 11px;
}

.toggle-item span {
    display: block;

    margin-top: 3px;

    color: #899598;

    font-size: 9px;
}

.toggle {
    width: 35px;
    height: 19px;

    padding: 2px;

    border: none;

    border-radius: 20px;

    background: #dfe8e6;

    cursor: pointer;

    transition: .2s;
}

.toggle.active {
    background: #22ae99;
}

.toggle div {
    width: 15px;
    height: 15px;

    border-radius: 50%;

    background: white;

    transition: .2s;
}

.toggle.active div {
    transform: translateX(16px);
}


/* =========================================================
   METHOD
========================================================= */

.method-status {
    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 13px 0;

    border-bottom: 1px solid #edf2f2;
}

.method-status:last-of-type {
    border-bottom: none;
}

.method-status strong {
    display: block;

    color: #485558;

    font-size: 11px;
}

.method-status span {
    display: block;

    margin-top: 3px;

    color: #899598;

    font-size: 9px;
}

.enabled {
    padding: 5px 9px;

    border-radius: 20px;

    background: #daf6e4;

    color: #27a667;

    font-size: 9px;

    font-weight: 600;
}

.method-info {
    display: flex;

    align-items: center;

    gap: 7px;

    margin-top: 12px;

    padding: 9px;

    border-radius: 7px;

    background: #f7faf9;

    color: #899598;

    font-size: 8px;
}

.method-info svg {
    width: 15px;

    flex-shrink: 0;

    color: #20aa97;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 800px) {

    .bottom-grid {
        grid-template-columns: 1fr;
    }

    .profile-view {
        align-items: flex-start;

        flex-direction: column;
    }

}


@media (max-width: 600px) {

    .profile-information {
        grid-template-columns: 1fr;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .edit-photo-area {
        align-items: flex-start;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .save-button,
    .cancel-button {
        width: 100%;
    }

    .security-view {
        align-items: flex-start;

        flex-wrap: wrap;
    }

    .security-view .outline-button {
        width: 100%;
    }

}

</style>
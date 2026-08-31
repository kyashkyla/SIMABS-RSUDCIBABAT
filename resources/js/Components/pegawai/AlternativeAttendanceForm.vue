<script setup>
import { ref } from 'vue'

import {
    IdentificationIcon,
    CameraIcon,
    PaperAirplaneIcon,
} from '@heroicons/vue/24/outline'

import { router } from '@inertiajs/vue3'

const emit = defineEmits(['submitted'])

const alasan = ref('')
const keterangan = ref('')

const selfieFile = ref(null)
const idCardFile = ref(null)

const pilihSelfie = (event) => {
    selfieFile.value = event.target.files[0] || null
}

const pilihIdCard = (event) => {
    idCardFile.value = event.target.files[0] || null
}

const kirimPengajuan = () => {
    if (!alasan.value || !selfieFile.value || !idCardFile.value) {
        return
    }

    const formData = new FormData()
    formData.append('reason', alasan.value + (keterangan.value ? ' - ' + keterangan.value : ''))
    formData.append('selfie_photo', selfieFile.value)
    formData.append('id_card_photo', idCardFile.value)

    router.post(route('pegawai.absensi.alternatif.store'), formData, {
        onSuccess: () => {
            emit('submitted')
        }
    })
}

</script>

<template>

    <div class="main-card">

        <div class="header">

            <div class="icon">
                <IdentificationIcon />
            </div>

            <div>

                <h2>
                    Pengajuan Absen Alternatif
                </h2>

                <p>
                    Pengajuan akan diverifikasi oleh admin.
                </p>

            </div>

        </div>


        <!-- ALASAN -->

        <div class="form-group">

            <label>
                Alasan Pengajuan
            </label>

            <select v-model="alasan">

                <option value="">
                    Pilih alasan
                </option>

                <option value="Wajah berubah setelah operasi">
                    Wajah berubah setelah operasi
                </option>

                <option value="Gangguan kamera perangkat">
                    Gangguan kamera perangkat
                </option>

                <option value="Face Recognition tidak mengenali wajah">
                    Face Recognition tidak mengenali wajah
                </option>

                <option value="Kondisi lain">
                    Kondisi lain
                </option>

            </select>

        </div>


        <!-- KETERANGAN -->

        <div class="form-group">

            <label>
                Keterangan Tambahan
            </label>

            <textarea
                v-model="keterangan"
                placeholder="Jelaskan alasan pengajuan..."
            ></textarea>

        </div>


        <!-- FILE -->

        <div class="upload-grid">


            <!-- SELFIE -->

            <label class="upload-box">

                <CameraIcon />

                <strong>
                    Selfie Terbaru
                </strong>

                <span>
                    {{
                        selfieFile
                            ? selfieFile.name
                            : 'Upload foto wajah terbaru'
                    }}
                </span>

                <input
                    type="file"
                    accept="image/*"
                    @change="pilihSelfie"
                />

                <button type="button">
                    Pilih Foto
                </button>

            </label>


            <!-- ID CARD -->

            <label class="upload-box">

                <IdentificationIcon />

                <strong>
                    ID Card Pegawai
                </strong>

                <span>
                    {{
                        idCardFile
                            ? idCardFile.name
                            : 'Upload foto ID Card'
                    }}
                </span>

                <input
                    type="file"
                    accept="image/*"
                    @change="pilihIdCard"
                />

                <button type="button">
                    Pilih Foto
                </button>

            </label>

        </div>


        <!-- SUBMIT -->

        <button
            class="submit-button"
            @click="kirimPengajuan"
            :disabled="
                !alasan ||
                !selfieFile ||
                !idCardFile
            "
        >

            <PaperAirplaneIcon />

            Kirim Permintaan

        </button>

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

    box-shadow: 0 4px 16px rgba(30,100,90,.04);
}

.header {
    display: flex;
    align-items: center;
    gap: 10px;

    margin-bottom: 20px;
}

.icon {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #e8f8f5;
    border-radius: 11px;
}

.icon svg {
    width: 22px;
    color: #20a995;
}

.header h2 {
    margin: 0;
    color: #354347;
    font-size: 17px;
}

.header p {
    margin: 4px 0 0;
    color: #899598;
    font-size: 10px;
}

.form-group {
    margin-bottom: 14px;
}

.form-group label {
    display: block;

    margin-bottom: 6px;

    color: #566467;

    font-size: 10px;
    font-weight: 600;
}

.form-group select,
.form-group textarea {
    width: 100%;
    box-sizing: border-box;

    border: 1px solid #dfe9e7;
    border-radius: 8px;

    background: white;

    color: #4b595c;

    font-size: 10px;
    outline: none;
}

.form-group select {
    height: 38px;
    padding: 0 9px;
}

.form-group textarea {
    min-height: 80px;
    padding: 9px;
    resize: vertical;
}

.upload-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.upload-box {
    position: relative;

    padding: 15px;

    text-align: center;

    border: 1px dashed #cbdeda;
    border-radius: 9px;

    background: white;

    cursor: pointer;
}

.upload-box > svg {
    width: 22px;
    color: #20aa97;
}

.upload-box strong {
    display: block;

    margin-top: 6px;

    color: #4b585b;

    font-size: 10px;
}

.upload-box span {
    display: block;

    margin: 3px 0 8px;

    color: #909b9d;

    font-size: 8px;
}

.upload-box input {
    position: absolute;

    width: 1px;
    height: 1px;

    opacity: 0;
}

.upload-box button {
    padding: 5px 9px;

    border: 1px solid #dce8e6;
    border-radius: 6px;

    background: white;

    color: #20a995;

    font-size: 8px;
}

.submit-button {
    width: 100%;
    height: 42px;

    margin-top: 14px;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    border: none;
    border-radius: 8px;

    background: #20ad98;

    color: white;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;
}

.submit-button svg {
    width: 15px;
}

.submit-button:disabled {
    opacity: .5;
    cursor: not-allowed;
}

@media (max-width: 600px) {

    .upload-grid {
        grid-template-columns: 1fr;
    }

}

</style>
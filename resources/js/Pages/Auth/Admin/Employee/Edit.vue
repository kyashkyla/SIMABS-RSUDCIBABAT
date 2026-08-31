<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import { ref } from 'vue'

const props = defineProps({
    employee: {
        type: Object,
        required: true,
    },
    shiftOptions: {
        type: Object,
        default: () => ({}),
    },
})

const form = useForm({
    nip: props.employee.nip ?? '',
    name: props.employee.name ?? '',
    nik: props.employee.nik ?? '',
    email: props.employee.email ?? '',
    phone: props.employee.phone ?? '',
    gender: props.employee.gender ?? '',
    position: props.employee.position ?? '',
    department: props.employee.department ?? '',
    status: props.employee.status ?? 'Aktif',
    start_date: props.employee.start_date
        ? props.employee.start_date.substring(0, 10)
        : '',
    shift: props.employee.shift ?? '',
    photo: null,
})

// Pratinjau foto profil. Foto ini juga jadi acuan pencocokan Face ID
// pegawai saat absen, jadi begitu admin menggantinya di sini, foto yang
// dipakai pegawai (di halaman profil & saat verifikasi wajah) ikut berubah.
const photoPreview = ref(props.employee.photo_url || '')

const pilihFoto = (event) => {
    const file = event.target.files[0]

    if (!file) {
        return
    }

    form.photo = file
    photoPreview.value = URL.createObjectURL(file)
}

const submit = () => {
    // PENTING: jangan pakai form.put() langsung di sini.
    //
    // Inertia hanya otomatis meng-convert request jadi POST + _method=PUT
    // kalau ada FILE BARU yang ikut dikirim. Kalau admin tidak mengganti
    // foto (photo tetap null), form.put() + forceFormData tetap mengirim
    // body sebagai multipart/form-data TAPI dengan method HTTP asli PUT.
    // PHP tidak bisa mem-parsing body multipart pada request PUT (cuma
    // bisa untuk POST), jadi semua field yang dikirim jadi kosong di sisi
    // server -> muncul error "field is required" padahal sudah diisi.
    //
    // Solusinya: paksa spoofing method lewat field `_method`, lalu kirim
    // selalu sebagai POST, apa pun kondisinya (ganti foto atau tidak).
    form.transform((data) => ({
        ...data,
        _method: 'put',
    })).post(route('admin.employees.update', props.employee.id), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Edit Pegawai" />

    <AdminLayout>

        <!-- HEADER -->
        <div class="flex items-center gap-4 mb-8">

            <Link
                :href="`/admin/pegawai/${employee.id}`"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200
                       flex items-center justify-center
                       text-slate-600 hover:bg-slate-50 transition">

                ←

            </Link>

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    Edit Pegawai
                </h1>

                <p class="text-slate-500 mt-1">
                    Perbarui informasi data pegawai.
                </p>

            </div>

        </div>


        <!-- FORM -->
        <form
            @submit.prevent="submit"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">

            <!-- FOTO PROFIL / ACUAN FACE ID -->

            <div class="mb-10 pb-8 border-b border-slate-100">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-amber-100 text-amber-600
                               flex items-center justify-center">

                        📷

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Foto Profil
                        </h2>

                        <p class="text-sm text-slate-500">
                            Foto ini juga dipakai sebagai acuan pencocokan Face ID saat pegawai absen
                        </p>

                    </div>

                </div>

                <div class="flex items-center gap-6">

                    <div
                        class="w-24 h-24 rounded-2xl overflow-hidden
                               bg-slate-100 border border-slate-200
                               flex items-center justify-center shrink-0">

                        <img
                            v-if="photoPreview"
                            :src="photoPreview"
                            alt="Foto Profil"
                            class="w-full h-full object-cover"
                        />

                        <span v-else class="text-slate-400 text-3xl">
                            👤
                        </span>

                    </div>

                    <div>

                        <label
                            class="inline-block px-4 py-2 rounded-xl
                                   bg-slate-100 hover:bg-slate-200
                                   text-slate-700 text-sm font-semibold
                                   cursor-pointer transition">

                            Ganti Foto

                            <input
                                type="file"
                                accept="image/png, image/jpeg, image/jpg"
                                class="hidden"
                                @change="pilihFoto"
                            />

                        </label>

                        <p class="text-xs text-slate-400 mt-2">
                            Format JPG/PNG, maksimal 2MB.
                        </p>

                        <p
                            v-if="form.errors.photo"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.photo }}

                        </p>

                    </div>

                </div>

            </div>


            <!-- DATA IDENTITAS -->

            <div class="mb-10">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-emerald-100 text-emerald-600
                               flex items-center justify-center">

                        👤

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Data Identitas
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi pribadi pegawai
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- NIP -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            NIP
                        </label>

                        <input
                            v-model="form.nip"
                            type="text"
                            placeholder="Masukkan NIP"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.nip"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.nip }}

                        </p>

                    </div>


                    <!-- NIK -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            NIK
                        </label>

                        <input
                            v-model="form.nik"
                            type="text"
                            placeholder="Masukkan NIK"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.nik"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.nik }}

                        </p>

                    </div>


                    <!-- Nama -->

                    <div class="md:col-span-2">

                        <label class="block font-medium text-slate-700 mb-2">
                            Nama Lengkap
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Masukkan nama lengkap"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.name"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.name }}

                        </p>

                    </div>


                    <!-- Gender -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Jenis Kelamin
                        </label>

                        <select
                            v-model="form.gender"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">

                            <option value="">
                                Pilih jenis kelamin
                            </option>

                            <option value="Laki-laki">
                                Laki-laki
                            </option>

                            <option value="Perempuan">
                                Perempuan
                            </option>

                        </select>

                        <p
                            v-if="form.errors.gender"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.gender }}

                        </p>

                    </div>


                    <!-- Email -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Email
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="contoh@email.com"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.email"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.email }}

                        </p>

                    </div>


                    <!-- Phone -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Nomor HP
                        </label>

                        <input
                            v-model="form.phone"
                            type="text"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.phone"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.phone }}

                        </p>

                    </div>

                </div>

            </div>


            <!-- DATA KEPEGAWAIAN -->

            <div class="border-t border-slate-100 pt-8">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-blue-100 text-blue-600
                               flex items-center justify-center">

                        💼

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Data Kepegawaian
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi pekerjaan pegawai
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Jabatan -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Jabatan
                        </label>

                        <input
                            v-model="form.position"
                            type="text"
                            placeholder="Masukkan jabatan"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.position"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.position }}

                        </p>

                    </div>


                    <!-- Departemen -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Departemen
                        </label>

                        <select
                            v-model="form.department"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">

                            <option value="">
                                Pilih departemen
                            </option>

                            <option value="IGD">
                                IGD
                            </option>

                            <option value="Radiologi">
                                Radiologi
                            </option>

                            <option value="Farmasi">
                                Farmasi
                            </option>

                            <option value="Rawat Inap">
                                Rawat Inap
                            </option>

                            <option value="Administrasi">
                                Administrasi
                            </option>

                            <!-- Jaga-jaga: kalau departemen pegawai ini tersimpan
                                 dengan nilai lama yang sudah tidak ada di daftar
                                 di atas, tetap tampilkan sebagai pilihan supaya
                                 datanya tidak hilang / tidak dianggap kosong. -->
                            <option
                                v-if="form.department && !['IGD','Radiologi','Farmasi','Rawat Inap','Administrasi'].includes(form.department)"
                                :value="form.department">

                                {{ form.department }}

                            </option>

                        </select>

                        <p
                            v-if="form.errors.department"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.department }}

                        </p>

                    </div>


                    <!-- Status -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Status Pegawai
                        </label>

                        <select
                            v-model="form.status"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">

                            <option value="Aktif">
                                Aktif
                            </option>

                            <option value="Nonaktif">
                                Nonaktif
                            </option>

                        </select>

                    </div>


                    <!-- Shift -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Shift
                        </label>

                        <select
                            v-model="form.shift"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3 bg-white
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">

                            <option value="">
                                Pilih shift
                            </option>

                            <option
                                v-for="(detail, nama) in shiftOptions"
                                :key="nama"
                                :value="nama">

                                {{ nama }} ({{ detail.start }} - {{ detail.end }})

                            </option>

                        </select>

                        <p
                            v-if="form.shift && shiftOptions[form.shift]"
                            class="text-sm text-slate-500 mt-2">

                            Absen masuk hanya bisa jam
                            {{ shiftOptions[form.shift].check_in.start }}-{{ shiftOptions[form.shift].check_in.end }},
                            absen pulang hanya bisa jam
                            {{ shiftOptions[form.shift].check_out.start }}-{{ shiftOptions[form.shift].check_out.end }}.

                        </p>

                    </div>


                    <!-- Tanggal -->

                    <div>

                        <label class="block font-medium text-slate-700 mb-2">
                            Tanggal Mulai Bekerja
                        </label>

                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                    </div>

                </div>

            </div>


            <!-- BUTTON -->

            <div
                class="flex flex-col-reverse sm:flex-row
                       justify-end gap-3
                       border-t border-slate-100
                       mt-10 pt-6">

                <Link
                    :href="`/admin/pegawai/${employee.id}`"
                    class="px-6 py-3 rounded-xl
                           bg-slate-100 hover:bg-slate-200
                           text-slate-700 font-semibold
                           text-center transition">

                    Batal

                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-3 rounded-xl
                           bg-emerald-500 hover:bg-emerald-600
                           disabled:opacity-50
                           disabled:cursor-not-allowed
                           text-white font-semibold
                           transition">

                    {{ form.processing
                        ? 'Menyimpan...'
                        : 'Simpan Perubahan'
                    }}

                </button>

            </div>

        </form>

    </AdminLayout>
</template>
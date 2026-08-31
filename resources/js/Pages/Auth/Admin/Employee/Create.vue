<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import { ref } from 'vue'

const props = defineProps({
    shiftOptions: {
        type: Object,
        default: () => ({}),
    },
})

const form = useForm({
    nip: '',
    name: '',
    nik: '',
    email: '',
    phone: '',
    gender: '',
    position: '',
    department: '',
    status: 'Aktif',
    start_date: '',
    shift: '',
    photo: null,
})

const photoPreview = ref('')

const pilihFoto = (event) => {
    const file = event.target.files[0]

    if (!file) {
        return
    }

    form.photo = file
    photoPreview.value = URL.createObjectURL(file)
}

const hapusFoto = () => {
    if (photoPreview.value) {
        URL.revokeObjectURL(photoPreview.value)
    }

    form.photo = null
    photoPreview.value = ''
}

const submitForm = () => {
    form.post(route('admin.employees.store'), {
        forceFormData: true,
    })
}
</script>

<template>
    <Head title="Tambah Pegawai" />

    <AdminLayout>

        <!-- ================= HEADER ================= -->

        <div class="flex items-center gap-4 mb-8">

            <Link
                href="/admin/pegawai"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200
                       flex items-center justify-center
                       text-slate-600 hover:bg-slate-50 transition">

                ←

            </Link>

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    Tambah Pegawai
                </h1>

                <p class="text-slate-500 mt-1">
                    Tambahkan data pegawai baru ke dalam sistem SIMABS.
                </p>

            </div>

        </div>


        <!-- ================= FORM ================= -->

        <form
            @submit.prevent="submitForm"
            class="space-y-6">


            <!-- ================= FOTO PROFIL ================= -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl bg-amber-100
                               text-amber-600 flex items-center justify-center">

                        📷

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Foto Profil
                        </h2>

                        <p class="text-sm text-slate-500">
                            Foto ini juga dipakai sebagai acuan Face ID saat pegawai absen
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

                            Pilih Foto

                            <input
                                type="file"
                                accept="image/png, image/jpeg, image/jpg"
                                class="hidden"
                                @change="pilihFoto"
                            />

                        </label>

                        <button
                            v-if="photoPreview"
                            type="button"
                            @click="hapusFoto"
                            class="ml-2 px-4 py-2 rounded-xl
                                   text-slate-500 text-sm font-semibold
                                   hover:bg-slate-50 transition">

                            Hapus

                        </button>

                        <p class="text-xs text-slate-400 mt-2">
                            Opsional saat ini. Format JPG/PNG, maksimal 2MB.
                        </p>

                        <p
                            v-if="form.errors.photo"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.photo }}

                        </p>

                    </div>

                </div>

            </div>


            <!-- ================= DATA IDENTITAS ================= -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl bg-emerald-100
                               text-emerald-600 flex items-center justify-center">

                        👤

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Data Identitas
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi dasar pegawai
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                    <!-- NIP -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            NIP
                        </label>

                        <input
                            v-model="form.nip"
                            type="text"
                            placeholder="Masukkan NIP"
                            class="w-full rounded-xl border border-slate-200
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


                    <!-- Nama -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Lengkap
                        </label>

                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Masukkan nama lengkap"
                            class="w-full rounded-xl border border-slate-200
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


                    <!-- NIK -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            NIK
                        </label>

                        <input
                            v-model="form.nik"
                            type="text"
                            placeholder="Masukkan NIK"
                            class="w-full rounded-xl border border-slate-200
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


                    <!-- Email -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>

                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="contoh@email.com"
                            class="w-full rounded-xl border border-slate-200
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


                    <!-- No HP -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nomor HP
                        </label>

                        <input
                            v-model="form.phone"
                            type="text"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-xl border border-slate-200
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


                    <!-- Jenis Kelamin -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Jenis Kelamin
                        </label>

                        <select
                            v-model="form.gender"
                            class="w-full rounded-xl border border-slate-200
                                   px-4 py-3
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

                </div>

            </div>


            <!-- ================= DATA KEPEGAWAIAN ================= -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl bg-blue-100
                               text-blue-600 flex items-center justify-center">

                        💼

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Data Kepegawaian
                        </h2>

                        <p class="text-sm text-slate-500">
                            Informasi jabatan dan unit kerja
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                    <!-- Jabatan -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Jabatan
                        </label>

                        <input
                            v-model="form.position"
                            type="text"
                            placeholder="Contoh: Perawat"
                            class="w-full rounded-xl border border-slate-200
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

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Departemen
                        </label>

                        <select
                            v-model="form.department"
                            class="w-full rounded-xl border border-slate-200
                                   px-4 py-3
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

                        </select>

                        <p
                            v-if="form.errors.department"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.department }}

                        </p>

                    </div>


                    <!-- Status -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Status Pegawai
                        </label>

                        <select
                            v-model="form.status"
                            class="w-full rounded-xl border border-slate-200
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">

                            <option value="Aktif">
                                Aktif
                            </option>

                            <option value="Nonaktif">
                                Nonaktif
                            </option>

                        </select>

                        <p
                            v-if="form.errors.status"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.status }}

                        </p>

                    </div>


                    <!-- Tanggal Masuk -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Mulai Bekerja
                        </label>

                        <input
                            v-model="form.start_date"
                            type="date"
                            class="w-full rounded-xl border border-slate-200
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

                        <p
                            v-if="form.errors.start_date"
                            class="text-sm text-red-500 mt-1">

                            {{ form.errors.start_date }}

                        </p>

                    </div>

                </div>

            </div>


            <!-- ================= SHIFT ================= -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl bg-orange-100
                               text-orange-600 flex items-center justify-center">

                        🕐

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Pengaturan Shift
                        </h2>

                        <p class="text-sm text-slate-500">
                            Tentukan shift kerja pegawai
                        </p>

                    </div>

                </div>


                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Shift Kerja
                    </label>

                    <select
                        v-model="form.shift"
                        class="w-full md:w-1/2 rounded-xl border border-slate-200
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2 focus:ring-emerald-500">

                        <option value="">
                            Pilih shift
                        </option>

                        <option
                            v-for="(detail, nama) in shiftOptions"
                            :key="nama"
                            :value="nama">

                            Shift {{ nama }} ({{ detail.start }} - {{ detail.end }})

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

                    <p
                        v-if="form.errors.shift"
                        class="text-sm text-red-500 mt-1">

                        {{ form.errors.shift }}

                    </p>

                </div>

            </div>


            <!-- ================= FACE ID ================= -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-10 h-10 rounded-xl bg-purple-100
                               text-purple-600 flex items-center justify-center">

                        😊

                    </div>

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Registrasi Face ID
                        </h2>

                        <p class="text-sm text-slate-500">
                            Data wajah dapat didaftarkan setelah data pegawai dibuat.
                        </p>

                    </div>

                </div>


                <div
                    class="rounded-xl bg-purple-50 border border-purple-100 p-5">

                    <div class="flex gap-4">

                        <div class="text-2xl">
                            ℹ️
                        </div>

                        <div>

                            <h3 class="font-semibold text-purple-800">
                                Registrasi Face ID
                            </h3>

                            <p class="text-sm text-purple-700 mt-1 leading-6">
                                Kalau foto profil di atas sudah diunggah, foto
                                tersebut otomatis menjadi acuan Face ID pegawai.
                                Kalau belum diunggah sekarang, admin bisa
                                menambahkannya nanti lewat menu edit pegawai.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================= BUTTON ================= -->

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pb-8">

                <Link
                    href="/admin/pegawai"
                    class="px-6 py-3 rounded-xl
                           border border-slate-200
                           bg-white text-slate-600
                           font-semibold text-center
                           hover:bg-slate-50 transition">

                    Batal

                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-3 rounded-xl
                           bg-emerald-500 hover:bg-emerald-600
                           text-white font-semibold
                           transition disabled:opacity-50">

                    {{ form.processing ? 'Menyimpan...' : 'Simpan Pegawai' }}

                </button>

            </div>

        </form>

    </AdminLayout>
</template>
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    employee: {
        type: Object,
        required: true,
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
})

const submit = () => {
    form.put(route('admin.employees.update', props.employee.id))
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

                        <input
                            v-model="form.department"
                            type="text"
                            placeholder="Masukkan departemen"
                            class="w-full rounded-xl border border-slate-300
                                   px-4 py-3
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500"
                        />

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

                            <option value="Pagi">
                                Pagi
                            </option>

                            <option value="Siang">
                                Siang
                            </option>

                            <option value="Malam">
                                Malam
                            </option>

                        </select>

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
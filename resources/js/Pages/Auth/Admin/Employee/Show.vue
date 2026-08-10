<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    employee: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Head :title="`Detail - ${employee.name}`" />

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
                    Detail Pegawai
                </h1>

                <p class="text-slate-500 mt-1">
                    Informasi lengkap data pegawai.
                </p>

            </div>

        </div>


        <!-- ================= PROFILE ================= -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">

            <div class="flex flex-col md:flex-row md:items-center gap-6">

                <!-- Avatar -->

                <div
                    class="w-24 h-24 rounded-2xl
                           bg-emerald-100 text-emerald-700
                           flex items-center justify-center
                           text-4xl font-bold">

                    {{ employee.name.charAt(0) }}

                </div>


                <!-- Identity -->

                <div class="flex-1">

                    <h2 class="text-2xl font-bold text-slate-800">
                        {{ employee.name }}
                    </h2>

                    <p class="text-slate-500 mt-1">
                        NIP {{ employee.nip }}
                    </p>

                    <div class="flex flex-wrap gap-2 mt-4">

                        <span
                            class="px-3 py-1 rounded-full
                                   bg-blue-100 text-blue-700
                                   text-sm font-medium">

                            {{ employee.position }}

                        </span>

                        <span
                            class="px-3 py-1 rounded-full
                                   bg-slate-100 text-slate-600
                                   text-sm font-medium">

                            {{ employee.department }}

                        </span>

                        <span
                            v-if="employee.status === 'Aktif'"
                            class="px-3 py-1 rounded-full
                                   bg-green-100 text-green-700
                                   text-sm font-medium">

                            Aktif

                        </span>

                        <span
                            v-else
                            class="px-3 py-1 rounded-full
                                   bg-red-100 text-red-700
                                   text-sm font-medium">

                            Nonaktif

                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ================= DATA ================= -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Data Identitas -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

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


                <div class="space-y-5">

                    <div>

                        <p class="text-sm text-slate-400">
                            Nama Lengkap
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.name }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            NIP
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.nip }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            NIK
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.nik }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Jenis Kelamin
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.gender }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Email
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.email || '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Nomor HP
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.phone || '-' }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- Data Kepegawaian -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

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


                <div class="space-y-5">

                    <div>

                        <p class="text-sm text-slate-400">
                            Jabatan
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.position }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Departemen
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.department }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Status Pegawai
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.status }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Tanggal Mulai Bekerja
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            {{ employee.start_date || '-' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-sm text-slate-400">
                            Shift Kerja
                        </p>

                        <p class="font-semibold text-slate-700 mt-1">
                            Shift {{ employee.shift }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- Face ID -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 lg:col-span-2">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

                    <div class="flex items-center gap-4">

                        <div
                            class="w-12 h-12 rounded-xl
                                   bg-purple-100 text-purple-600
                                   flex items-center justify-center text-xl">

                            😊

                        </div>

                        <div>

                            <h2 class="text-xl font-bold text-slate-800">
                                Face ID
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Status registrasi wajah pegawai
                            </p>

                        </div>

                    </div>


                    <div>

                        <span
                            v-if="employee.face_id_registered"
                            class="inline-flex items-center gap-2
                                   px-4 py-2 rounded-xl
                                   bg-green-100 text-green-700
                                   font-semibold">

                            ✓ Face ID Terdaftar

                        </span>

                        <span
                            v-else
                            class="inline-flex items-center gap-2
                                   px-4 py-2 rounded-xl
                                   bg-orange-100 text-orange-700
                                   font-semibold">

                            ! Belum Terdaftar

                        </span>

                    </div>

                </div>


                <div
                    v-if="!employee.face_id_registered"
                    class="mt-5 bg-orange-50 border border-orange-100
                           rounded-xl p-4 text-sm text-orange-700">

                    Pegawai ini belum memiliki data Face ID.
                    Registrasi Face ID diperlukan agar pegawai dapat
                    melakukan absensi menggunakan metode Face ID.

                </div>

            </div>

        </div>

        <!-- AKUN LOGIN -->

        <div
            class="bg-white rounded-2xl shadow-sm
                border border-slate-100 p-6 mt-6">

            <div class="flex items-center gap-3 mb-6">

                <div
                    class="w-10 h-10 rounded-xl
                        bg-emerald-100
                        flex items-center justify-center">

                    🔐

                </div>

                <div>

                    <h2 class="text-xl font-bold text-slate-800">
                        Akun Login
                    </h2>

                    <p class="text-sm text-slate-500">
                        Informasi akun akses pegawai
                    </p>

                </div>

            </div>


            <div
                v-if="employee.user"
                class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- USERNAME -->

                <div>

                    <p class="text-sm text-slate-500 mb-1">
                        Username
                    </p>

                    <p class="font-semibold text-slate-800">
                        {{ employee.user.username }}
                    </p>

                </div>


                <!-- PASSWORD -->

                <div>

                    <p class="text-sm text-slate-500 mb-1">
                        Password
                    </p>

                    <p class="font-semibold text-slate-500 tracking-widest">
                        ••••••••
                    </p>

                </div>


                <!-- ROLE -->

                <div>

                    <p class="text-sm text-slate-500 mb-1">
                        Role
                    </p>

                    <span
                        class="inline-flex px-3 py-1
                            rounded-full
                            bg-blue-100 text-blue-700
                            text-sm font-medium">

                        {{ employee.user.role }}

                    </span>

                </div>


                <!-- STATUS -->

                <div>

                    <p class="text-sm text-slate-500 mb-1">
                        Status Akun
                    </p>

                    <span
                        class="inline-flex px-3 py-1
                            rounded-full
                            bg-green-100 text-green-700
                            text-sm font-medium">

                        ● Aktif

                    </span>

                </div>

            </div>


            <div
                v-else
                class="bg-yellow-50 border border-yellow-200
                    rounded-xl p-4">

                <p class="text-sm text-yellow-700">

                    ⚠️ Pegawai ini belum memiliki akun login.

                </p>

            </div>

        </div>


        <!-- ================= FOOTER ================= -->

        <div class="flex justify-end gap-3 mt-6 pb-8">

            <Link
                href="/admin/pegawai"
                class="px-6 py-3 rounded-xl
                    bg-white border border-slate-200
                    text-slate-600 font-semibold
                    hover:bg-slate-50 transition">

                Kembali

            </Link>

            <Link
                :href="`/admin/pegawai/${employee.id}/reset-password`"
                class="px-5 py-3 rounded-xl bg-emerald-500 text-white font-semibold hover:bg-emerald-600 transition"
            >
                🔐 Reset Password
            </Link>

            <Link
                :href="`/admin/pegawai/${employee.id}/edit`"
                class="px-6 py-3 rounded-xl
                    bg-blue-500 hover:bg-blue-600
                    text-white font-semibold
                    transition">

                ✏ Edit Pegawai

            </Link>

        </div>

    </AdminLayout>
</template>
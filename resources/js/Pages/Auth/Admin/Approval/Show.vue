<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const approvalType = ref('temporary')
const startDate = ref('2026-08-13')
const endDate = ref('2026-08-20')
const adminNote = ref('')

const approve = () => {
    alert(
        approvalType.value === 'temporary'
            ? 'Pengajuan disetujui sebagai Approval Sementara.'
            : 'Pengajuan disetujui sebagai Approval Permanen.'
    )
}

const reject = () => {
    alert('Pengajuan ditolak.')
}
</script>

<template>

    <Head title="Detail Persetujuan Absensi" />

    <AdminLayout>

        <div class="max-w-6xl mx-auto space-y-6">

            <!-- HEADER -->

            <div class="flex items-center gap-4">

                <Link
                    href="/admin/persetujuan-absensi"
                    class="w-10 h-10 rounded-xl bg-white border border-slate-200
                           flex items-center justify-center text-slate-500
                           hover:bg-slate-50 transition">

                    ←

                </Link>

                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Detail Persetujuan Absensi
                    </h1>

                    <p class="text-slate-500 mt-1">
                        Periksa pengajuan absensi alternatif pegawai.
                    </p>

                </div>

            </div>


            <!-- STATUS -->

            <div
                class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5
                       flex items-center justify-between">

                <div class="flex items-center gap-4">

                    <div
                        class="w-11 h-11 rounded-xl bg-yellow-100
                               flex items-center justify-center text-yellow-600 text-xl">

                        ⏳

                    </div>

                    <div>

                        <p class="font-semibold text-yellow-800">
                            Menunggu Persetujuan Admin
                        </p>

                        <p class="text-sm text-yellow-700 mt-1">
                            Pengajuan belum diberikan keputusan.
                        </p>

                    </div>

                </div>

                <span
                    class="px-4 py-2 rounded-full bg-yellow-100
                           text-yellow-700 text-sm font-semibold">

                    Menunggu

                </span>

            </div>


            <!-- DATA PEGAWAI -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Data Pegawai
                    </h2>

                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <p class="text-sm text-slate-500">
                            Nama Pegawai
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            Budi Santoso
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            NIP
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            0283973982
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Departemen
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            Radiologi
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Jabatan
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            Dokter
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Shift
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            Pagi
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Metode Absensi Utama
                        </p>

                        <p class="font-semibold text-emerald-600 mt-1">
                            Face ID
                        </p>

                    </div>

                </div>

            </div>


            <!-- ALASAN PENGAJUAN -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Detail Pengajuan
                    </h2>

                </div>

                <div class="p-6 space-y-5">

                    <div>

                        <p class="text-sm text-slate-500 mb-2">
                            Alasan Pengajuan
                        </p>

                        <div
                            class="bg-slate-50 rounded-xl p-4 text-slate-700">

                            Face ID tidak dapat digunakan karena kamera
                            perangkat mengalami kerusakan.

                        </div>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500 mb-2">
                            Metode Absensi Alternatif
                        </p>

                        <span
                            class="inline-flex items-center px-4 py-2
                                   rounded-xl bg-blue-50 text-blue-700
                                   font-semibold">

                            🔐 OTP

                        </span>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500 mb-2">
                            Tanggal Pengajuan
                        </p>

                        <p class="text-slate-700">
                            13 Agustus 2026
                        </p>

                    </div>

                </div>

            </div>


            <!-- BUKTI -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Bukti Pengajuan
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Foto yang dikirim oleh pegawai sebagai bukti pengajuan.
                    </p>

                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- SELFIE -->

                    <div>

                        <p class="font-semibold text-slate-700 mb-3">
                            Foto Selfie
                        </p>

                        <div
                            class="aspect-[4/3] rounded-2xl bg-slate-100
                                   border border-slate-200 overflow-hidden
                                   flex items-center justify-center">

                            <div class="text-center text-slate-400">

                                <div class="text-5xl mb-3">
                                    📸
                                </div>

                                <p class="text-sm">
                                    Foto selfie pegawai
                                </p>

                            </div>

                        </div>

                        <button
                            class="mt-3 w-full py-2.5 rounded-xl
                                   border border-slate-200
                                   text-slate-600 text-sm font-medium
                                   hover:bg-slate-50 transition">

                            Lihat Foto Selfie

                        </button>

                    </div>


                    <!-- ID CARD -->

                    <div>

                        <p class="font-semibold text-slate-700 mb-3">
                            Foto ID Card
                        </p>

                        <div
                            class="aspect-[4/3] rounded-2xl bg-slate-100
                                   border border-slate-200 overflow-hidden
                                   flex items-center justify-center">

                            <div class="text-center text-slate-400">

                                <div class="text-5xl mb-3">
                                    🪪
                                </div>

                                <p class="text-sm">
                                    Foto ID Card pegawai
                                </p>

                            </div>

                        </div>

                        <button
                            class="mt-3 w-full py-2.5 rounded-xl
                                   border border-slate-200
                                   text-slate-600 text-sm font-medium
                                   hover:bg-slate-50 transition">

                            Lihat Foto ID Card

                        </button>

                    </div>

                </div>

            </div>


            <!-- KEPUTUSAN ADMIN -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Keputusan Admin
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Tentukan jenis approval untuk pegawai ini.
                    </p>

                </div>

                <div class="p-6 space-y-6">

                    <!-- PILIHAN APPROVAL -->

                    <div>

                        <label class="text-sm font-semibold text-slate-700">
                            Jenis Approval
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">

                            <!-- SEMENTARA -->

                            <label
                                class="relative cursor-pointer">

                                <input
                                    v-model="approvalType"
                                    type="radio"
                                    value="temporary"
                                    class="peer sr-only">

                                <div
                                    class="border-2 border-slate-200
                                           rounded-2xl p-5
                                           peer-checked:border-emerald-500
                                           peer-checked:bg-emerald-50
                                           transition">

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="w-11 h-11 rounded-xl
                                                   bg-blue-100
                                                   flex items-center justify-center
                                                   text-blue-600 text-xl">

                                            🕒

                                        </div>

                                        <div>

                                            <h3 class="font-semibold text-slate-800">
                                                Approval Sementara
                                            </h3>

                                            <p class="text-sm text-slate-500 mt-1">
                                                Pegawai dapat menggunakan OTP
                                                selama periode yang ditentukan.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </label>


                            <!-- PERMANEN -->

                            <label
                                class="relative cursor-pointer">

                                <input
                                    v-model="approvalType"
                                    type="radio"
                                    value="permanent"
                                    class="peer sr-only">

                                <div
                                    class="border-2 border-slate-200
                                           rounded-2xl p-5
                                           peer-checked:border-emerald-500
                                           peer-checked:bg-emerald-50
                                           transition">

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="w-11 h-11 rounded-xl
                                                   bg-emerald-100
                                                   flex items-center justify-center
                                                   text-emerald-600 text-xl">

                                            ✓

                                        </div>

                                        <div>

                                            <h3 class="font-semibold text-slate-800">
                                                Approval Permanen
                                            </h3>

                                            <p class="text-sm text-slate-500 mt-1">
                                                Pegawai dapat menggunakan OTP
                                                tanpa batas waktu.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </label>

                        </div>

                    </div>


                    <!-- TANGGAL -->

                    <div
                        v-if="approvalType === 'temporary'"
                        class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="text-sm font-semibold text-slate-700">
                                Tanggal Mulai
                            </label>

                            <input
                                v-model="startDate"
                                type="date"
                                class="mt-2 w-full rounded-xl
                                       border border-slate-300 px-4 py-3
                                       focus:outline-none
                                       focus:ring-2 focus:ring-emerald-500">

                        </div>

                        <div>

                            <label class="text-sm font-semibold text-slate-700">
                                Tanggal Berakhir
                            </label>

                            <input
                                v-model="endDate"
                                type="date"
                                class="mt-2 w-full rounded-xl
                                       border border-slate-300 px-4 py-3
                                       focus:outline-none
                                       focus:ring-2 focus:ring-emerald-500">

                        </div>

                    </div>


                    <!-- INFO PERMANEN -->

                    <div
                        v-if="approvalType === 'permanent'"
                        class="bg-emerald-50 border border-emerald-200
                               rounded-xl p-4">

                        <div class="flex gap-3">

                            <span class="text-emerald-600 text-xl">
                                ✓
                            </span>

                            <div>

                                <p class="font-semibold text-emerald-800">
                                    Approval Permanen
                                </p>

                                <p class="text-sm text-emerald-700 mt-1">
                                    Pegawai akan dapat menggunakan OTP sebagai
                                    metode absensi alternatif tanpa batas waktu.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- CATATAN -->

                    <div>

                        <label class="text-sm font-semibold text-slate-700">
                            Catatan Admin
                        </label>

                        <textarea
                            v-model="adminNote"
                            rows="4"
                            placeholder="Tambahkan catatan atau alasan keputusan..."
                            class="mt-2 w-full rounded-xl border border-slate-300
                                   px-4 py-3 resize-none
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">
                        </textarea>

                    </div>

                </div>

            </div>


            <!-- ACTION -->

            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-100
                       p-6 flex flex-col md:flex-row
                       md:items-center md:justify-between gap-4">

                <Link
                    href="/admin/persetujuan-absensi"
                    class="text-slate-500 hover:text-slate-700 font-medium">

                    ← Kembali ke Daftar

                </Link>

                <div class="flex gap-3">

                    <button
                        @click="reject"
                        class="px-6 py-3 rounded-xl
                               border border-red-200
                               text-red-600 font-semibold
                               hover:bg-red-50 transition">

                        Tolak Pengajuan

                    </button>

                    <button
                        @click="approve"
                        class="px-6 py-3 rounded-xl
                               bg-emerald-600 text-white
                               font-semibold
                               hover:bg-emerald-700 transition">

                        Setujui Pengajuan

                    </button>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>
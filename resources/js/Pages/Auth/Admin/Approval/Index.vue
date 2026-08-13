<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const approvals = [
    {
        id: 1,
        name: 'Budi Santoso',
        nip: '0283973982',
        department: 'Radiologi',
        reason: 'Face ID tidak dapat digunakan',
        date: '13 Agustus 2026',
        status: 'Menunggu',
    },
    {
        id: 2,
        name: 'Maya Kusuma',
        nip: '1987654321',
        department: 'IGD',
        reason: 'Kamera perangkat mengalami kerusakan',
        date: '13 Agustus 2026',
        status: 'Menunggu',
    },
    {
        id: 3,
        name: 'Rizky Saputra',
        nip: '1987654322',
        department: 'Farmasi',
        reason: 'Kendala Face ID',
        date: '12 Agustus 2026',
        status: 'Disetujui',
        type: 'Sementara',
    },
]
</script>

<template>

    <Head title="Persetujuan Absensi" />

    <AdminLayout>

        <div class="space-y-6">

            <!-- HEADER -->
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Persetujuan Absensi
                </h1>

                <p class="text-slate-500 mt-1">
                    Kelola pengajuan penggunaan absensi alternatif pegawai.
                </p>
            </div>

            <!-- STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <!-- Menunggu -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-slate-500">
                                Menunggu Persetujuan
                            </p>

                            <h2 class="text-3xl font-bold text-slate-800 mt-2">
                                2
                            </h2>
                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-yellow-600 text-xl">
                            ⏳
                        </div>

                    </div>

                </div>

                <!-- Sementara -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-slate-500">
                                Approval Sementara Aktif
                            </p>

                            <h2 class="text-3xl font-bold text-slate-800 mt-2">
                                1
                            </h2>
                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 text-xl">
                            🕒
                        </div>

                    </div>

                </div>

                <!-- Permanen -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm text-slate-500">
                                Approval Permanen
                            </p>

                            <h2 class="text-3xl font-bold text-slate-800 mt-2">
                                0
                            </h2>
                        </div>

                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 text-xl">
                            ✓
                        </div>

                    </div>

                </div>

            </div>

            <!-- TABLE -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <!-- TABLE HEADER -->
                <div class="p-6 border-b border-slate-100">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        <div>
                            <h2 class="text-lg font-semibold text-slate-800">
                                Pengajuan Absensi Alternatif
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Periksa pengajuan sebelum memberikan persetujuan.
                            </p>
                        </div>

                        <!-- FILTER -->
                        <select
                            class="border border-slate-300 rounded-xl px-4 py-2.5 text-sm
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">

                            <option>Semua Status</option>
                            <option>Menunggu</option>
                            <option>Disetujui</option>
                            <option>Ditolak</option>

                        </select>

                    </div>

                </div>

                <!-- TABLE CONTENT -->
                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50">

                            <tr class="text-left text-sm text-slate-500">

                                <th class="px-6 py-4 font-medium">
                                    Pegawai
                                </th>

                                <th class="px-6 py-4 font-medium">
                                    Departemen
                                </th>

                                <th class="px-6 py-4 font-medium">
                                    Alasan
                                </th>

                                <th class="px-6 py-4 font-medium">
                                    Tanggal
                                </th>

                                <th class="px-6 py-4 font-medium">
                                    Status
                                </th>

                                <th class="px-6 py-4 font-medium text-right">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr
                                v-for="approval in approvals"
                                :key="approval.id"
                                class="border-t border-slate-100 hover:bg-slate-50 transition">

                                <!-- PEGAWAI -->
                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-10 h-10 rounded-full bg-emerald-100
                                                   flex items-center justify-center
                                                   text-emerald-700 font-semibold">

                                            {{ approval.name.charAt(0) }}

                                        </div>

                                        <div>

                                            <p class="font-semibold text-slate-800">
                                                {{ approval.name }}
                                            </p>

                                            <p class="text-sm text-slate-500">
                                                NIP {{ approval.nip }}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <!-- DEPARTEMEN -->
                                <td class="px-6 py-5 text-sm text-slate-600">
                                    {{ approval.department }}
                                </td>

                                <!-- ALASAN -->
                                <td class="px-6 py-5">

                                    <p class="text-sm text-slate-700">
                                        {{ approval.reason }}
                                    </p>

                                    <span
                                        class="text-xs text-slate-400">
                                        Metode alternatif: OTP
                                    </span>

                                </td>

                                <!-- TANGGAL -->
                                <td class="px-6 py-5 text-sm text-slate-600">
                                    {{ approval.date }}
                                </td>

                                <!-- STATUS -->
                                <td class="px-6 py-5">

                                    <span
                                        v-if="approval.status === 'Menunggu'"
                                        class="inline-flex items-center px-3 py-1 rounded-full
                                               text-xs font-semibold
                                               bg-yellow-100 text-yellow-700">

                                        Menunggu

                                    </span>

                                    <span
                                        v-else-if="approval.status === 'Disetujui'"
                                        class="inline-flex items-center px-3 py-1 rounded-full
                                               text-xs font-semibold
                                               bg-emerald-100 text-emerald-700">

                                        Disetujui
                                        {{ approval.type ? '• ' + approval.type : '' }}

                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex items-center px-3 py-1 rounded-full
                                               text-xs font-semibold
                                               bg-red-100 text-red-700">

                                        Ditolak

                                    </span>

                                </td>

                                <!-- AKSI -->
                                <td class="px-6 py-5 text-right">

                                    <Link
                                        :href="`/admin/persetujuan-absensi/${approval.id}`"
                                        class="inline-flex items-center px-4 py-2
                                               rounded-xl bg-emerald-50
                                               text-emerald-700 text-sm font-semibold
                                               hover:bg-emerald-100 transition">

                                        Lihat Detail

                                    </Link>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>
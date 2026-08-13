<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const attendance = [
    {
        id: 1,
        name: 'Ahmad Fauzi',
        nip: '1987654321',
        department: 'Radiologi',
        date: '10 Agustus 2026',
        checkIn: '07:03',
        checkOut: '15:02',
        method: 'Face ID',
        status: 'Hadir',
    },
    {
        id: 2,
        name: 'Maya Kusuma',
        nip: '1987654322',
        department: 'IGD',
        date: '10 Agustus 2026',
        checkIn: '07:08',
        checkOut: '15:05',
        method: 'OTP',
        status: 'Hadir',
    },
    {
        id: 3,
        name: 'Rizky Saputra',
        nip: '1987654323',
        department: 'Farmasi',
        date: '10 Agustus 2026',
        checkIn: '-',
        checkOut: '-',
        method: '-',
        status: 'Tidak Hadir',
    },
    {
        id: 4,
        name: 'Siti Rahma',
        nip: '1987654324',
        department: 'Administrasi',
        date: '10 Agustus 2026',
        checkIn: '08:17',
        checkOut: '16:01',
        method: 'Alternatif',
        status: 'Terlambat',
    },
    {
        id: 5,
        name: 'Budi Santoso',
        nip: '1987654325',
        department: 'Laboratorium',
        date: '09 Agustus 2026',
        checkIn: '06:58',
        checkOut: '14:59',
        method: 'Face ID',
        status: 'Hadir',
    },
]

const statusClass = (status) => {
    switch (status) {
        case 'Hadir':
            return 'bg-green-100 text-green-700'

        case 'Terlambat':
            return 'bg-yellow-100 text-yellow-700'

        case 'Tidak Hadir':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'
    }
}

const methodClass = (method) => {
    switch (method) {
        case 'Face ID':
            return 'bg-emerald-100 text-emerald-700'

        case 'OTP':
            return 'bg-blue-100 text-blue-700'

        case 'Alternatif':
            return 'bg-orange-100 text-orange-700'

        default:
            return 'bg-gray-100 text-gray-500'
    }
}
</script>

<template>

    <Head title="Riwayat Absensi" />

    <AdminLayout>

        <!-- Header -->

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    Riwayat Absensi
                </h1>

                <p class="text-slate-500 mt-1">
                    Melihat dan memantau seluruh riwayat absensi pegawai.
                </p>

            </div>

            <div class="flex gap-3">

                <button
                    class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition">

                    Export Excel

                </button>

                <button
                    class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition">

                    Export PDF

                </button>

            </div>

        </div>


        <!-- Statistik -->

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Total Absensi
                </p>

                <h2 class="text-3xl font-bold text-slate-800 mt-2">
                    132
                </h2>

                <p class="text-sm text-slate-400 mt-1">
                    Hari ini
                </p>

            </div>


            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Hadir
                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">
                    120
                </h2>

                <p class="text-sm text-green-500 mt-1">
                    91% kehadiran
                </p>

            </div>


            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Terlambat
                </p>

                <h2 class="text-3xl font-bold text-yellow-500 mt-2">
                    8
                </h2>

                <p class="text-sm text-yellow-500 mt-1">
                    Hari ini
                </p>

            </div>


            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Tidak Hadir
                </p>

                <h2 class="text-3xl font-bold text-red-500 mt-2">
                    4
                </h2>

                <p class="text-sm text-red-500 mt-1">
                    Hari ini
                </p>

            </div>

        </div>


        <!-- Filter -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">

            <div class="flex items-center gap-2 mb-5">

                <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center">
                    🔎
                </div>

                <div>

                    <h2 class="font-semibold text-slate-800">
                        Filter Absensi
                    </h2>

                    <p class="text-sm text-slate-400">
                        Gunakan filter untuk mencari data absensi.
                    </p>

                </div>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Tanggal -->

                <div>

                    <label class="block text-sm font-medium text-slate-600 mb-2">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />

                </div>


                <!-- Departemen -->

                <div>

                    <label class="block text-sm font-medium text-slate-600 mb-2">
                        Departemen
                    </label>

                    <select
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none">

                        <option value="">
                            Semua Departemen
                        </option>

                        <option>Radiologi</option>
                        <option>IGD</option>
                        <option>Farmasi</option>
                        <option>Laboratorium</option>
                        <option>Administrasi</option>

                    </select>

                </div>


                <!-- Status -->

                <div>

                    <label class="block text-sm font-medium text-slate-600 mb-2">
                        Status
                    </label>

                    <select
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none">

                        <option value="">
                            Semua Status
                        </option>

                        <option>Hadir</option>
                        <option>Terlambat</option>
                        <option>Tidak Hadir</option>

                    </select>

                </div>


                <!-- Metode -->

                <div>

                    <label class="block text-sm font-medium text-slate-600 mb-2">
                        Metode
                    </label>

                    <select
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none">

                        <option value="">
                            Semua Metode
                        </option>

                        <option>Face ID</option>
                        <option>OTP</option>
                        <option>Alternatif</option>

                    </select>

                </div>

            </div>


            <div class="flex justify-end mt-5">

                <button
                    class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition">

                    Terapkan Filter

                </button>

            </div>

        </div>


        <!-- Table -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            <div class="p-6 border-b border-slate-100">

                <div class="flex justify-between items-center">

                    <div>

                        <h2 class="text-xl font-semibold text-slate-800">
                            Data Riwayat Absensi
                        </h2>

                        <p class="text-sm text-slate-400 mt-1">
                            Daftar aktivitas absensi pegawai.
                        </p>

                    </div>

                    <span class="text-sm text-slate-500">
                        5 data
                    </span>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Pegawai
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Departemen
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Tanggal
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Jam Masuk
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Jam Pulang
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Metode
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Status
                            </th>

                            <th class="text-center px-6 py-4 text-sm font-semibold text-slate-600">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        <tr
                            v-for="item in attendance"
                            :key="item.id"
                            class="hover:bg-slate-50 transition">

                            <!-- Pegawai -->

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-semibold">

                                        {{ item.name.charAt(0) }}

                                    </div>

                                    <div>

                                        <p class="font-medium text-slate-800">
                                            {{ item.name }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            NIP: {{ item.nip }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <td class="px-6 py-4 text-slate-600">
                                {{ item.department }}
                            </td>


                            <td class="px-6 py-4 text-slate-600">
                                {{ item.date }}
                            </td>


                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ item.checkIn }}
                            </td>


                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ item.checkOut }}
                            </td>


                            <td class="px-6 py-4">

                                <span
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-xs font-medium',
                                        methodClass(item.method)
                                    ]">

                                    {{ item.method }}

                                </span>

                            </td>


                            <td class="px-6 py-4">

                                <span
                                    :class="[
                                        'px-3 py-1.5 rounded-full text-xs font-medium',
                                        statusClass(item.status)
                                    ]">

                                    {{ item.status }}

                                </span>

                            </td>


                            <td class="px-6 py-4 text-center">

                                <Link
                                    :href="`/admin/absensi/${item.id}`"
                                    class="text-emerald-600 hover:text-emerald-800 font-medium text-sm">

                                    Detail

                                </Link>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </AdminLayout>

</template>
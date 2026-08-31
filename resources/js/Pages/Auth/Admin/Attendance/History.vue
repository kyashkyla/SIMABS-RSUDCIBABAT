<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { reactive, computed } from 'vue'

const props = defineProps({
    attendances: { type: Object, required: true }, // Laravel paginator
    departments: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
})

const form = reactive({
    date: props.filters.date || '',
    department: props.filters.department || '',
    search: props.filters.search || '',
})

const rows = computed(() => props.attendances.data)

const summary = computed(() => ({
    total: rows.value.length,
    hadir: rows.value.filter((r) => r.status === 'Hadir' || r.status === 'Alternatif').length,
    terlambat: rows.value.filter((r) => r.status === 'Terlambat').length,
    tidakHadir: rows.value.filter((r) => r.status === 'Tidak Hadir').length,
}))

const applyFilter = () => {
    router.get(route('admin.attendance.history'), form, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    })
}

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

        </div>


        <!-- Statistik -->

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Total Absensi
                </p>

                <h2 class="text-3xl font-bold text-slate-800 mt-2">
                    {{ summary.total }}
                </h2>

                <p class="text-sm text-slate-400 mt-1">
                    Halaman ini
                </p>

            </div>


            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Hadir
                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">
                    {{ summary.hadir }}
                </h2>

                <p class="text-sm text-green-500 mt-1">
                    Halaman ini
                </p>

            </div>


            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Terlambat
                </p>

                <h2 class="text-3xl font-bold text-yellow-500 mt-2">
                    {{ summary.terlambat }}
                </h2>

                <p class="text-sm text-yellow-500 mt-1">
                    Halaman ini
                </p>

            </div>


            <div class="bg-white rounded-2xl shadow-sm p-5 border border-slate-100">

                <p class="text-sm text-slate-500">
                    Tidak Hadir
                </p>

                <h2 class="text-3xl font-bold text-red-500 mt-2">
                    {{ summary.tidakHadir }}
                </h2>

                <p class="text-sm text-red-500 mt-1">
                    Halaman ini
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
                        v-model="form.date"
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
                        v-model="form.department"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none">

                        <option value="">
                            Semua Departemen
                        </option>

                        <option v-for="dept in departments" :key="dept" :value="dept">
                            {{ dept }}
                        </option>

                    </select>

                </div>


                <!-- Status -->

                <div>

                    <label class="block text-sm font-medium text-slate-600 mb-2">
                        Cari Nama Pegawai
                    </label>

                    <input
                        v-model="form.search"
                        type="text"
                        placeholder="Ketik nama pegawai..."
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    />

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
                    @click="applyFilter"
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
                        {{ attendances.total }} data
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

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        <tr v-if="rows.length === 0">
                            <td colspan="7" class="px-6 py-10 text-center text-slate-400">
                                Tidak ada data absensi untuk filter ini.
                            </td>
                        </tr>

                        <tr
                            v-for="item in rows"
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


                        </tr>

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            <div
                v-if="attendances.links && attendances.links.length > 3"
                class="flex flex-wrap gap-2 justify-end p-6 border-t border-slate-100">

                <Link
                    v-for="(link, idx) in attendances.links"
                    :key="idx"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 rounded-lg text-sm"
                    :class="[
                        link.active ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                        !link.url && 'opacity-40 pointer-events-none'
                    ]"
                />

            </div>

        </div>

    </AdminLayout>

</template>
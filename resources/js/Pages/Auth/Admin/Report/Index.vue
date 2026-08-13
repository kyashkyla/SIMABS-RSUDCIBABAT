<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const startDate = ref('')
const endDate = ref('')
const department = ref('')
const shift = ref('')
const status = ref('')

const reports = ref([
    {
        id: 1,
        date: '12 Agustus 2026',
        nip: '1987654321',
        name: 'Ahmad Fauzi',
        department: 'Radiologi',
        shift: 'Pagi',
        checkIn: '07:03',
        checkOut: '15:05',
        method: 'Face ID',
        status: 'Hadir',
    },
    {
        id: 2,
        date: '12 Agustus 2026',
        nip: '1987654322',
        name: 'Maya Kusuma',
        department: 'IGD',
        shift: 'Pagi',
        checkIn: '07:08',
        checkOut: '15:10',
        method: 'OTP',
        status: 'Terlambat',
    },
    {
        id: 3,
        date: '12 Agustus 2026',
        nip: '1987654323',
        name: 'Rizky Saputra',
        department: 'Farmasi',
        shift: 'Siang',
        checkIn: '-',
        checkOut: '-',
        method: '-',
        status: 'Tidak Hadir',
    },
    {
        id: 4,
        date: '12 Agustus 2026',
        nip: '1987654324',
        name: 'Siti Rahma',
        department: 'Laboratorium',
        shift: 'Pagi',
        checkIn: '06:58',
        checkOut: '15:01',
        method: 'Face ID',
        status: 'Hadir',
    },
    {
        id: 5,
        date: '12 Agustus 2026',
        nip: '1987654325',
        name: 'Deni Kurniawan',
        department: 'Administrasi',
        shift: 'Siang',
        checkIn: '13:15',
        checkOut: '21:02',
        method: 'Face ID',
        status: 'Hadir',
    },
])

const filteredReports = computed(() => {
    return reports.value.filter((item) => {
        return (
            (!department.value || item.department === department.value) &&
            (!shift.value || item.shift === shift.value) &&
            (!status.value || item.status === status.value)
        )
    })
})

const totalPegawai = computed(() => filteredReports.value.length)

const totalHadir = computed(() =>
    filteredReports.value.filter(item => item.status === 'Hadir').length
)

const totalTerlambat = computed(() =>
    filteredReports.value.filter(item => item.status === 'Terlambat').length
)

const totalTidakHadir = computed(() =>
    filteredReports.value.filter(item => item.status === 'Tidak Hadir').length
)

const resetFilter = () => {
    startDate.value = ''
    endDate.value = ''
    department.value = ''
    shift.value = ''
    status.value = ''
}

const exportExcel = () => {
    alert('Fitur Export Excel akan dihubungkan ke Laravel.')
}

const exportPDF = () => {
    alert('Fitur Export PDF akan dihubungkan ke Laravel.')
}
</script>

<template>

    <Head title="Laporan Absensi" />

    <AdminLayout>

        <div class="space-y-6">

            <!-- HEADER -->

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <h1 class="text-3xl font-bold text-slate-800">
                        Laporan Absensi
                    </h1>

                    <p class="text-slate-500 mt-1">
                        Rekapitulasi dan laporan kehadiran pegawai RSUD Cibabat
                    </p>

                </div>

                <div class="flex gap-3">

                    <button
                        @click="exportExcel"
                        class="px-4 py-2.5 rounded-xl bg-green-600 text-white font-semibold hover:bg-green-700 transition">

                        📊 Export Excel

                    </button>

                    <button
                        @click="exportPDF"
                        class="px-4 py-2.5 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">

                        📄 Export PDF

                    </button>

                </div>

            </div>


            <!-- FILTER -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

                <div class="flex items-center justify-between mb-5">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-800">
                            Filter Laporan
                        </h2>

                        <p class="text-sm text-slate-500">
                            Tentukan periode dan kategori absensi
                        </p>

                    </div>

                    <button
                        @click="resetFilter"
                        class="text-sm text-slate-500 hover:text-emerald-600">

                        Reset Filter

                    </button>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">

                    <!-- Tanggal Mulai -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Mulai
                        </label>

                        <input
                            v-model="startDate"
                            type="date"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />

                    </div>


                    <!-- Tanggal Akhir -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Tanggal Akhir
                        </label>

                        <input
                            v-model="endDate"
                            type="date"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />

                    </div>


                    <!-- Departemen -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Departemen
                        </label>

                        <select
                            v-model="department"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                            <option value="">
                                Semua Departemen
                            </option>

                            <option value="Radiologi">
                                Radiologi
                            </option>

                            <option value="IGD">
                                IGD
                            </option>

                            <option value="Farmasi">
                                Farmasi
                            </option>

                            <option value="Laboratorium">
                                Laboratorium
                            </option>

                            <option value="Administrasi">
                                Administrasi
                            </option>

                        </select>

                    </div>


                    <!-- Shift -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Shift
                        </label>

                        <select
                            v-model="shift"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                            <option value="">
                                Semua Shift
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


                    <!-- Status -->

                    <div>

                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Status
                        </label>

                        <select
                            v-model="status"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500">

                            <option value="">
                                Semua Status
                            </option>

                            <option value="Hadir">
                                Hadir
                            </option>

                            <option value="Terlambat">
                                Terlambat
                            </option>

                            <option value="Tidak Hadir">
                                Tidak Hadir
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <!-- STATISTIK -->

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

                <!-- Total -->

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-500">
                                Total Data
                            </p>

                            <h3 class="text-3xl font-bold text-slate-800 mt-2">
                                {{ totalPegawai }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Data absensi
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-2xl">
                            👥
                        </div>

                    </div>

                </div>


                <!-- Hadir -->

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-500">
                                Hadir
                            </p>

                            <h3 class="text-3xl font-bold text-green-600 mt-2">
                                {{ totalHadir }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Kehadiran normal
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl">
                            ✓
                        </div>

                    </div>

                </div>


                <!-- Terlambat -->

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-500">
                                Terlambat
                            </p>

                            <h3 class="text-3xl font-bold text-yellow-500 mt-2">
                                {{ totalTerlambat }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Melebihi jam masuk
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-2xl">
                            ⏰
                        </div>

                    </div>

                </div>


                <!-- Tidak Hadir -->

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-slate-500">
                                Tidak Hadir
                            </p>

                            <h3 class="text-3xl font-bold text-red-500 mt-2">
                                {{ totalTidakHadir }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Tidak melakukan absensi
                            </p>

                        </div>

                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center text-2xl">
                            !
                        </div>

                    </div>

                </div>

            </div>


            <!-- TABLE -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                <div class="p-6 border-b border-slate-200">

                    <div class="flex items-center justify-between">

                        <div>

                            <h2 class="text-xl font-semibold text-slate-800">
                                Rekap Absensi
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Daftar absensi berdasarkan filter yang dipilih
                            </p>

                        </div>

                        <span class="text-sm text-slate-500">
                            {{ filteredReports.length }} data
                        </span>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Tanggal
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    NIP
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Pegawai
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Departemen
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Shift
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Masuk
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Pulang
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Metode
                                </th>

                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            <tr
                                v-for="item in filteredReports"
                                :key="item.id"
                                class="hover:bg-slate-50 transition">

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ item.date }}
                                </td>

                                <td class="px-6 py-4 text-sm font-medium text-slate-700">
                                    {{ item.nip }}
                                </td>

                                <td class="px-6 py-4">

                                    <p class="font-medium text-slate-800">
                                        {{ item.name }}
                                    </p>

                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ item.department }}
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ item.shift }}
                                </td>

                                <td class="px-6 py-4 text-sm font-medium">
                                    {{ item.checkIn }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ item.checkOut }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ item.method }}
                                </td>

                                <td class="px-6 py-4">

                                    <span
                                        v-if="item.status === 'Hadir'"
                                        class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">

                                        Hadir

                                    </span>

                                    <span
                                        v-else-if="item.status === 'Terlambat'"
                                        class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">

                                        Terlambat

                                    </span>

                                    <span
                                        v-else
                                        class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">

                                        Tidak Hadir

                                    </span>

                                </td>

                            </tr>


                            <tr v-if="filteredReports.length === 0">

                                <td
                                    colspan="9"
                                    class="px-6 py-12 text-center">

                                    <div class="text-4xl mb-3">
                                        📋
                                    </div>

                                    <p class="font-medium text-slate-700">
                                        Tidak ada data absensi
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">
                                        Coba ubah filter laporan.
                                    </p>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>
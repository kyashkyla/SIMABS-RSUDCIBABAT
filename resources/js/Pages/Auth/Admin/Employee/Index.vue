<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    employees: {
        type: Array,
        default: () => [],
    },
})

const search = ref('')
const selectedDepartment = ref('Semua Departemen')
const selectedStatus = ref('Semua Status')

const filteredEmployees = computed(() => {
    return props.employees.filter((employee) => {

        const keyword = search.value.toLowerCase()

        const matchSearch =
            employee.name.toLowerCase().includes(keyword) ||
            employee.nip.toLowerCase().includes(keyword)

        const matchDepartment =
            selectedDepartment.value === 'Semua Departemen' ||
            employee.department === selectedDepartment.value

        const matchStatus =
            selectedStatus.value === 'Semua Status' ||
            employee.status === selectedStatus.value

        return matchSearch && matchDepartment && matchStatus
    })
})

const totalEmployees = computed(() => props.employees.length)

const activeEmployees = computed(() =>
    props.employees.filter(
        employee => employee.status === 'Aktif'
    ).length
)

const faceIdRegistered = computed(() =>
    props.employees.filter(
        employee => employee.face_id_registered
    ).length
)

const faceIdNotRegistered = computed(() =>
    props.employees.filter(
        employee => !employee.face_id_registered
    ).length
)
</script>

<template>
    <Head title="Data Pegawai" />

    <AdminLayout>

        <!-- ================= HEADER ================= -->

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

            <div>
                <h1 class="text-3xl font-bold text-slate-800">
                    Data Pegawai
                </h1>

                <p class="text-slate-500 mt-1">
                    Kelola data pegawai dan registrasi Face ID.
                </p>
            </div>

            <Link
                href="/admin/pegawai/create"
                class="inline-flex items-center justify-center gap-2 px-5 py-3
                    bg-emerald-500 hover:bg-emerald-600
                    text-white font-semibold rounded-xl
                    shadow-sm transition">

                <span class="text-xl">+</span>

                Tambah Pegawai

            </Link>

        </div>


        <!-- ================= STATISTIK ================= -->

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

            <!-- Total -->

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Total Pegawai
                        </p>

                        <h2 class="text-3xl font-bold text-slate-800 mt-2">
                            {{ totalEmployees }}
                        </h2>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-emerald-100
                                text-emerald-600 flex items-center justify-center text-xl">
                        👥
                    </div>

                </div>

                <p class="text-sm text-slate-400 mt-4">
                    Seluruh data pegawai
                </p>

            </div>


            <!-- Aktif -->

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Pegawai Aktif
                        </p>

                        <h2 class="text-3xl font-bold text-green-600 mt-2">
                            {{ activeEmployees }}
                        </h2>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-green-100
                                text-green-600 flex items-center justify-center text-xl">
                        ✓
                    </div>

                </div>

                <p class="text-sm text-slate-400 mt-4">
                    Pegawai yang aktif bekerja
                </p>

            </div>


            <!-- Face ID -->

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Face ID Terdaftar
                        </p>

                        <h2 class="text-3xl font-bold text-blue-600 mt-2">
                            {{ faceIdRegistered }}
                        </h2>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-blue-100
                                text-blue-600 flex items-center justify-center text-xl">
                        😊
                    </div>

                </div>

                <p class="text-sm text-slate-400 mt-4">
                    Pegawai telah terdaftar
                </p>

            </div>


            <!-- Belum Face ID -->

            <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Belum Face ID
                        </p>

                        <h2 class="text-3xl font-bold text-orange-500 mt-2">
                            {{ faceIdNotRegistered }}
                        </h2>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-orange-100
                                text-orange-500 flex items-center justify-center text-xl">
                        !
                    </div>

                </div>

                <p class="text-sm text-slate-400 mt-4">
                    Perlu registrasi wajah
                </p>

            </div>

        </div>


        <!-- ================= FILTER ================= -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 mb-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Search -->

                <div class="relative">

                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        🔍
                    </span>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama atau NIP..."
                        class="w-full rounded-xl border border-slate-200
                               pl-11 pr-4 py-3
                               focus:outline-none focus:ring-2
                               focus:ring-emerald-500 focus:border-transparent"
                    />

                </div>


                <!-- Department -->

                <select
                    v-model="selectedDepartment"
                    class="w-full rounded-xl border border-slate-200
                           px-4 py-3
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-500">

                    <option>Semua Departemen</option>
                    <option>Radiologi</option>
                    <option>IGD</option>
                    <option>Farmasi</option>
                    <option>Rawat Inap</option>
                    <option>Administrasi</option>

                </select>


                <!-- Status -->

                <select
                    v-model="selectedStatus"
                    class="w-full rounded-xl border border-slate-200
                           px-4 py-3
                           focus:outline-none focus:ring-2
                           focus:ring-emerald-500">

                    <option>Semua Status</option>
                    <option>Aktif</option>
                    <option>Nonaktif</option>

                </select>

            </div>

        </div>


        <!-- ================= TABLE ================= -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

            <!-- Table Header -->

            <div class="px-6 py-5 border-b border-slate-100">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    <div>

                        <h2 class="text-xl font-bold text-slate-800">
                            Daftar Pegawai
                        </h2>

                        <p class="text-sm text-slate-500 mt-1">
                            Menampilkan {{ filteredEmployees.length }} data pegawai
                        </p>

                    </div>

                </div>

            </div>


            <!-- Table -->

            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Pegawai
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                NIP
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Jabatan
                            </th>

                            <th class="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                                Departemen
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">
                                Shift
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">
                                Face ID
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">
                                Status
                            </th>

                            <th class="px-6 py-4 text-center text-sm font-semibold text-slate-600">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="employee in filteredEmployees"
                            :key="employee.id"
                            class="border-t border-slate-100 hover:bg-slate-50 transition">

                            <!-- Pegawai -->

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="w-11 h-11 rounded-full overflow-hidden
                                               bg-emerald-100 text-emerald-700
                                               flex items-center justify-center
                                               font-bold shrink-0">

                                        <img
                                            v-if="employee.photo_url"
                                            :src="employee.photo_url"
                                            :alt="employee.name"
                                            class="w-full h-full object-cover"
                                        />

                                        <span v-else>
                                            {{ employee.name.charAt(0) }}
                                        </span>

                                    </div>

                                    <div>

                                        <p class="font-semibold text-slate-700">
                                            {{ employee.name }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            ID-{{ employee.id }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <!-- NIP -->

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ employee.nip }}
                            </td>


                            <!-- Jabatan -->

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ employee.position }}
                            </td>


                            <!-- Departemen -->

                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ employee.department }}
                            </td>


                            <!-- Shift -->

                            <td class="px-6 py-4 text-center">

                                <span
                                    class="px-3 py-1 rounded-lg
                                           bg-slate-100 text-slate-600
                                           text-sm">

                                    {{ employee.shift }}

                                </span>

                            </td>


                            <!-- Face ID -->

                            <td class="px-6 py-4 text-center">

                                <span
                                    v-if="employee.face_id_registered"
                                    class="inline-flex items-center gap-1
                                           px-3 py-1 rounded-full
                                           bg-green-100 text-green-700
                                           text-sm font-medium">

                                    ✓ Terdaftar

                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1
                                           px-3 py-1 rounded-full
                                           bg-orange-100 text-orange-700
                                           text-sm font-medium">

                                    ! Belum

                                </span>

                            </td>


                            <!-- Status -->

                            <td class="px-6 py-4 text-center">

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

                            </td>


                            <!-- Aksi -->

                            <td class="px-6 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    <Link
                                        :href="`/admin/pegawai/${employee.id}`"
                                        title="Detail"
                                        class="w-9 h-9 rounded-lg
                                            bg-slate-100 hover:bg-slate-200
                                            text-slate-600 transition
                                            flex items-center justify-center">

                                        👁

                                    </Link>

                                    <Link
                                        :href="`/admin/pegawai/${employee.id}/edit`"
                                        title="Edit"
                                        class="w-9 h-9 rounded-lg
                                            bg-blue-100 hover:bg-blue-200
                                            text-blue-600 transition
                                            flex items-center justify-center">

                                        ✏

                                    </Link>

                                    <Link
                                        :href="`/admin/pegawai/${employee.id}/delete`"
                                        title="Hapus"
                                        class="w-9 h-9 rounded-lg
                                            bg-red-100 hover:bg-red-200
                                            text-red-600 transition
                                            flex items-center justify-center">

                                        🗑

                                    </Link>

                                    <Link
                                        v-if="!employee.face_id_registered"
                                        :href="`/admin/pegawai/${employee.id}/edit`"
                                        title="Registrasi Face ID (unggah foto profil)"
                                        class="w-9 h-9 rounded-lg
                                               bg-emerald-100 hover:bg-emerald-200
                                               text-emerald-600 transition
                                               flex items-center justify-center">

                                        😊

                                    </Link>

                                </div>

                            </td>

                        </tr>


                        <!-- Empty -->

                        <tr v-if="filteredEmployees.length === 0">

                            <td
                                colspan="8"
                                class="px-6 py-12 text-center">

                                <div class="text-4xl mb-3">
                                    🔍
                                </div>

                                <p class="font-semibold text-slate-700">
                                    Data pegawai tidak ditemukan
                                </p>

                                <p class="text-sm text-slate-400 mt-1">
                                    Coba ubah kata kunci atau filter pencarian.
                                </p>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </AdminLayout>
</template>
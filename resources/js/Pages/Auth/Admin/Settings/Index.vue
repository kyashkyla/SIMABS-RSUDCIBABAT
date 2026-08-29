<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import LocationRadiusCard from '@/Components/Admin/LocationRadiusCard.vue'
import { MapPinIcon } from '@heroicons/vue/24/outline'

defineProps({
    locations: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()
const successMessage = computed(() => page.props.flash?.success)
</script>

<template>
    <Head title="Pengaturan" />

    <AdminLayout>

        <!-- ================= HEADER ================= -->

        <div class="mb-8">

            <h1 class="text-3xl font-bold text-slate-800">
                Pengaturan
            </h1>

            <p class="text-slate-500 mt-1">
                Atur lokasi & radius presensi pegawai di sini.
            </p>

        </div>


        <!-- ================= FLASH SUCCESS ================= -->

        <div
            v-if="successMessage"
            class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50
                   text-emerald-700 px-5 py-3 font-medium">

            {{ successMessage }}

        </div>


        <!-- ================= INFO CARD ================= -->

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">

            <div class="flex items-center gap-3">

                <div
                    class="w-10 h-10 rounded-xl bg-emerald-100
                           text-emerald-600 flex items-center justify-center">

                    <MapPinIcon class="w-5 h-5" />

                </div>

                <div>

                    <h2 class="text-xl font-bold text-slate-800">
                        Lokasi & Radius Presensi
                    </h2>

                    <p class="text-sm text-slate-500 mt-1 leading-6">
                        Pegawai hanya bisa absen kalau posisinya berada di dalam
                        radius salah satu lokasi di bawah. Radius diatur langsung
                        oleh admin di sini, pegawai tidak perlu (dan tidak bisa)
                        mengatur radius sendiri.
                    </p>

                </div>

            </div>

        </div>


        <!-- ================= DAFTAR LOKASI ================= -->

        <div class="space-y-4">

            <LocationRadiusCard
                v-for="location in locations"
                :key="location.id"
                :location="location"
            />

            <!-- Kartu tambah lokasi baru -->
            <LocationRadiusCard :location="null" />

            <p
                v-if="locations.length === 0"
                class="text-center text-slate-400 text-sm py-6">
                Belum ada lokasi presensi. Tambahkan lokasi baru di atas.
            </p>

        </div>

    </AdminLayout>
</template>
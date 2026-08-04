<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { User, Shield, Building2 } from 'lucide-vue-next'
</script>

<template>
    <Head title="SIMABS" />

    <div class="min-h-screen bg-slate-50 flex flex-col items-center justify-center px-6">

        <!-- Logo -->
        <div
            class="w-20 h-20 rounded-2xl bg-emerald-500 flex items-center justify-center shadow-lg mb-6">

            <Building2 class="w-10 h-10 text-white" />
        </div>

        <!-- Title -->

        <h1 class="text-6xl font-extrabold text-slate-800">
            SIMABS
        </h1>

        <p class="mt-2 text-emerald-500 text-2xl font-semibold">
            Sistem Informasi Manajemen Absensi
        </p>

        <p class="text-slate-500 text-lg mt-1">
            RSUD Cibabat Kota Cimahi
        </p>

        <!-- Card -->

        <div class="grid md:grid-cols-2 gap-8 mt-14">

            <!-- Pegawai -->

            <div
                class="bg-white rounded-3xl shadow-xl p-10 w-[360px] hover:scale-105 duration-300">

                <div
                    class="w-16 h-16 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto">

                    <User class="text-white w-8 h-8" />

                </div>

                <h2 class="text-center text-3xl font-bold mt-6">

                    Pegawai

                </h2>

                <p class="text-center text-slate-500 mt-4">

                    Masuk sebagai pegawai untuk melakukan absensi,
                    melihat riwayat kehadiran,
                    dan mengelola profil.

                </p>

                <Link href="/login"
                    class="mt-8 block text-center bg-emerald-500 text-white py-3 rounded-xl font-semibold hover:bg-emerald-600">

                    Masuk Sebagai Pegawai

                </Link>

            </div>

            <!-- Admin -->

            <div
                class="bg-gradient-to-br from-emerald-500 to-teal-400 text-white rounded-3xl shadow-xl p-10 w-[360px] hover:scale-105 duration-300">

                <div
                    class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto">

                    <Shield class="w-8 h-8" />

                </div>

                <h2 class="text-center text-3xl font-bold mt-6">

                    Administrator

                </h2>

                <p class="text-center mt-4 opacity-90">

                    Panel administrator untuk mengelola data pegawai,
                    memonitor absensi,
                    melakukan persetujuan absensi alternatif,
                    serta melihat laporan.

                </p>

                <Link href="/admin/login"
                    class="mt-8 block text-center bg-white text-emerald-600 py-3 rounded-xl font-semibold hover:bg-slate-100">

                    Masuk Sebagai Admin

                </Link>

            </div>

        </div>

        <p class="text-slate-500 mt-12 text-sm">

            © 2026 RSUD Cibabat — Kota Cimahi, Jawa Barat

        </p>

    </div>
</template>
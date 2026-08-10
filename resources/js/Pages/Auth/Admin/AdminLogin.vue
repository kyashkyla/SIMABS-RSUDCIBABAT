<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const username = ref('')
const password = ref('')

const goDashboard = () => {
    router.visit(route('admin.dashboard'))
}
</script>

<template>
    <Head title="Login Administrator" />

    <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4">

        <div class="w-full max-w-6xl bg-white rounded-3xl overflow-hidden shadow-2xl grid md:grid-cols-2">

            <!-- ================= LEFT ================= -->

            <div class="relative bg-gradient-to-br from-emerald-500 via-emerald-500 to-teal-400 text-white p-12">

                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center mb-6">
                    🏥
                </div>

                <p class="font-semibold text-lg">
                    Panel Admin
                </p>

                <p class="opacity-80 mb-10">
                    SIMABS RSUD Cibabat
                </p>

                <h1 class="text-5xl font-bold leading-tight mb-6">
                    Portal Administrator
                </h1>

                <p class="text-lg opacity-90 leading-8">
                    Akses penuh untuk manajemen data pegawai, monitoring absensi,
                    persetujuan absensi alternatif, serta pelaporan sistem.
                </p>

                <div class="mt-10 space-y-4">

                    <div class="bg-white/10 rounded-xl p-4">
                        ✓ Manajemen Data Pegawai (CRUD)
                    </div>

                    <div class="bg-white/10 rounded-xl p-4">
                        ✓ Monitoring Absensi Real-Time
                    </div>

                    <div class="bg-white/10 rounded-xl p-4">
                        ✓ Persetujuan Absensi Alternatif
                    </div>

                    <div class="bg-white/10 rounded-xl p-4">
                        ✓ Export PDF & Excel
                    </div>

                </div>

                <div class="grid grid-cols-3 gap-4 mt-12">

                    <div class="bg-white/10 rounded-xl text-center py-5">
                        <h2 class="text-3xl font-bold">132</h2>
                        <p>Pegawai</p>
                    </div>

                    <div class="bg-white/10 rounded-xl text-center py-5">
                        <h2 class="text-3xl font-bold">98%</h2>
                        <p>Akurasi</p>
                    </div>

                    <div class="bg-white/10 rounded-xl text-center py-5">
                        <h2 class="text-3xl font-bold">24/7</h2>
                        <p>Online</p>
                    </div>

                </div>

            </div>

            <!-- ================= RIGHT ================= -->

            <div class="p-12 flex items-center">

                <div class="w-full">

                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mb-6 text-3xl">
                        🛡️
                    </div>

                    <h2 class="text-4xl font-bold text-slate-800">
                        Masuk Admin
                    </h2>

                    <p class="text-slate-500 mt-2">
                        Khusus untuk administrator sistem
                    </p>

                    <!-- FORM -->

                    <form @submit.prevent="goDashboard" class="mt-10 space-y-6">

                        <div>

                            <label class="font-medium">
                                Username Administrator
                            </label>

                            <input
                                v-model="username"
                                type="text"
                                placeholder="Masukkan username"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            />

                        </div>

                        <div>

                            <label class="font-medium">
                                Password
                            </label>

                            <input
                                v-model="password"
                                type="password"
                                placeholder="Masukkan password"
                                class="mt-2 w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            />

                        </div>

                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-emerald-500 to-green-500 text-white rounded-xl py-3 font-semibold hover:from-emerald-600 hover:to-green-600 transition">

                            Masuk sebagai Admin

                        </button>

                    </form>

                    <div class="mt-6 bg-yellow-100 text-yellow-700 rounded-xl p-4 text-sm">
                        ⚠️ Akses hanya diperuntukkan bagi Administrator RSUD Cibabat.
                    </div>

                    <div class="text-center mt-8 text-gray-500">

                        Login sebagai Pegawai?

                        <Link
                            href="/login"
                            class="text-emerald-600 font-semibold hover:underline">

                            Klik di sini

                        </Link>

                    </div>

                    <div class="text-center mt-4">

                        <Link
                            href="/"
                            class="text-gray-500 hover:text-emerald-600">

                            ← Kembali ke Beranda

                        </Link>

                    </div>

                </div>

            </div>

        </div>

    </div>

</template>
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    BuildingOffice2Icon,
    UserIcon,
    LockClosedIcon,
    EyeIcon,
    EyeSlashIcon
} from '@heroicons/vue/24/outline'

import { ref } from 'vue'

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const showPassword = ref(false)

// Pegawai tidak bisa reset password sendiri (self-service email reset
// dimatikan). Kalau lupa password, harus menghubungi admin supaya admin
// yang mereset lewat menu "Reset Password" di Data Pegawai.
const tampilkanInfoLupaPassword = ref(false)

const form = useForm({
    nip: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>

<Head title="Login Pegawai" />

<div class="min-h-screen bg-slate-100 flex items-center justify-center px-6 py-10">

    <div class="w-full max-w-6xl bg-white rounded-3xl shadow-2xl overflow-hidden grid lg:grid-cols-2">

        <!-- ====================== LEFT ====================== -->

        <div class="p-12">

            <!-- Logo -->

            <div class="flex items-center gap-4">

                <div class="bg-emerald-500 p-3 rounded-2xl shadow-lg">

                    <BuildingOffice2Icon class="w-8 h-8 text-white"/>

                </div>

                <div>

                    <h1 class="font-bold text-2xl text-slate-800">

                        SIMABS

                    </h1>

                    <p class="text-sm text-slate-500">

                        RSUD Cibabat Kota Cimahi

                    </p>

                </div>

            </div>

            <h2 class="text-4xl font-bold mt-10 text-slate-800">

                Selamat Datang 

            </h2>

            <p class="text-slate-500 mt-2">

                Silakan login untuk melanjutkan absensi.

            </p>

            <form @submit.prevent="submit" class="mt-10 space-y-6">

                <!-- Username -->

                <div>

                    <label class="font-medium text-gray-700">

                        NIP

                    </label>

                    <div class="relative mt-2">

                        <UserIcon
                            class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

                        <input

                            v-model="form.nip"

                            type="text"

                            inputmode="numeric"

                            placeholder="Masukkan NIP"

                            class="w-full rounded-xl border border-gray-300 py-3 pl-12 pr-4 focus:ring-2 focus:ring-emerald-500 focus:outline-none"/>

                    </div>

                    <p
                        v-if="form.errors.nip"
                        class="text-red-500 text-sm mt-2">

                        {{ form.errors.nip }}

                    </p>

                </div>

                <!-- Password -->

                <div>

                    <label class="font-medium text-gray-700">

                        Password

                    </label>

                    <div class="relative mt-2">

                        <LockClosedIcon
                            class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

                        <input

                            v-model="form.password"

                            :type="showPassword ? 'text' : 'password'"

                            placeholder="Masukkan Password"

                            class="w-full rounded-xl border border-gray-300 py-3 pl-12 pr-12 focus:ring-2 focus:ring-emerald-500 focus:outline-none"/>

                        <button

                            type="button"

                            @click="showPassword=!showPassword"

                            class="absolute right-4 top-1/2 -translate-y-1/2">

                            <EyeIcon
                                v-if="!showPassword"
                                class="w-5 h-5 text-gray-500"/>

                            <EyeSlashIcon
                                v-else
                                class="w-5 h-5 text-gray-500"/>

                        </button>

                    </div>

                    <p
                        v-if="form.errors.password"
                        class="text-red-500 text-sm mt-2">

                        {{ form.errors.password }}

                    </p>

                </div>

                <!-- Remember -->

                <div class="flex items-center justify-between">

                    <label class="flex items-center gap-2">

                        <input
                            type="checkbox"
                            v-model="form.remember"
                            class="rounded border-gray-300 text-emerald-500">

                        <span class="text-sm text-gray-600">

                            Ingat Saya

                        </span>

                    </label>

                    <button

                        type="button"

                        @click="tampilkanInfoLupaPassword = !tampilkanInfoLupaPassword"

                        class="text-sm text-emerald-600 hover:underline">

                        Lupa Password?

                    </button>

                </div>

                <!-- Info Lupa Password -->

                <div

                    v-if="tampilkanInfoLupaPassword"

                    class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl p-4">

                    Untuk alasan keamanan, password tidak dapat direset secara mandiri.
                    Silakan hubungi <strong>Administrator SIMABS</strong> untuk mengajukan reset password akun Anda.

                </div>

                <!-- Button -->

                <button

                    :disabled="form.processing"

                    class="w-full bg-gradient-to-r from-emerald-500 to-green-500 text-white py-3 rounded-xl font-semibold hover:shadow-xl transition">

                    Masuk

                </button>

            </form>

            <!-- Register -->

            <div class="text-center mt-6 text-gray-500">

    Belum punya akun?

    <span class="text-emerald-600 font-semibold">
        Hubungi Administrator
    </span>

</div>

        </div>

        <!-- ====================== RIGHT ====================== -->

        <div
            class="bg-gradient-to-br from-emerald-500 to-green-500 text-white flex flex-col justify-center items-center p-12 relative overflow-hidden">

            <!-- Dekorasi -->

            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/10"></div>

            <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full bg-white/10"></div>

            <!-- Logo -->

            <div
                class="w-32 h-32 rounded-full bg-white flex items-center justify-center shadow-2xl">

                <BuildingOffice2Icon
                    class="w-16 h-16 text-emerald-500"/>

            </div>

            <h2 class="text-4xl font-bold mt-8">

                RSUD Cibabat

            </h2>

            <p class="text-center mt-4 max-w-md leading-8">

                Sistem Informasi Manajemen Absensi
                berbasis Geolocation,
                Verifikasi Wajah,
                dan OTP Alternatif.

            </p>

            <!-- Statistik -->

            <div class="grid grid-cols-3 gap-4 mt-10 w-full">

                <div class="bg-white/10 rounded-xl py-5 text-center">

                    <h3 class="text-3xl font-bold">

                        132

                    </h3>

                    <p class="text-sm">

                        Pegawai

                    </p>

                </div>

                <div class="bg-white/10 rounded-xl py-5 text-center">

                    <h3 class="text-3xl font-bold">

                        98%

                    </h3>

                    <p class="text-sm">

                        Akurasi

                    </p>

                </div>

                <div class="bg-white/10 rounded-xl py-5 text-center">

                    <h3 class="text-3xl font-bold">

                        24/7

                    </h3>

                    <p class="text-sm">

                        Online

                    </p>

                </div>

            </div>

            <!-- Fitur -->

            <div class="w-full mt-10 space-y-3">

                <div class="bg-white/10 rounded-xl p-3">

                    ✓ Face Recognition

                </div>

                <div class="bg-white/10 rounded-xl p-3">

                    ✓ Validasi Geolocation

                </div>

                <div class="bg-white/10 rounded-xl p-3">

                    ✓ OTP Alternatif (Persetujuan Admin)

                </div>

            </div>

        </div>

    </div>

</div>

</template>
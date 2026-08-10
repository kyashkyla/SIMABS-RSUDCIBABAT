<script setup>
import {
    BellIcon,
    MagnifyingGlassIcon,
    ChevronDownIcon,
} from '@heroicons/vue/24/outline'

const today = new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
})
</script>

<template>

<header class="bg-white shadow-sm border-b border-slate-200">

    <div class="flex items-center justify-between px-8 py-5">

        <!-- Judul -->

        <div>

            <h1 class="text-3xl font-bold text-slate-800">

                Dashboard Administrator

            </h1>

            <p class="text-slate-500 mt-1">

                Selamat datang di Sistem Informasi Manajemen Absensi RSUD Cibabat

            </p>

        </div>

        <!-- Right -->

        <div class="flex items-center gap-5">

            <!-- Search -->

            <div class="relative hidden lg:block">

                <MagnifyingGlassIcon
                    class="w-5 h-5 absolute left-4 top-3.5 text-gray-400"/>

                <input
                    type="text"
                    placeholder="Cari..."
                    class="w-72 pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:outline-none"/>

            </div>

            <!-- Tanggal -->

            <div class="hidden md:block text-right">

                <p class="text-xs text-gray-400">

                    Hari Ini

                </p>

                <p class="font-semibold text-slate-700">

                    {{ today }}

                </p>

            </div>

            <!-- Notifikasi -->

            <button
                class="relative w-12 h-12 rounded-xl bg-slate-100 hover:bg-slate-200 transition">

                <BellIcon
                    class="w-6 h-6 mx-auto text-slate-700"/>

                <span
                    class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full">
                </span>

            </button>

            <!-- Profile -->

            <button
                class="flex items-center gap-3 bg-slate-100 rounded-xl px-4 py-2 hover:bg-slate-200 transition">

                <div
                    class="w-11 h-11 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-lg">

                    A

                </div>

                <div class="text-left hidden md:block">

                    <h3 class="font-semibold text-slate-800">

                        Administrator

                    </h3>

                    <p class="text-sm text-gray-500">

                        Super Admin

                    </p>

                </div>

                <ChevronDownIcon
                    class="w-5 h-5 text-gray-500"/>

            </button>

        </div>

    </div>

</header>

</template>
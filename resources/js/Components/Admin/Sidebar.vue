<script setup>
import { Link, usePage } from '@inertiajs/vue3'

import {
    HomeIcon,
    UsersIcon,
    ClipboardDocumentCheckIcon,
    ClockIcon,
    DocumentChartBarIcon,
    Cog6ToothIcon,
    ArrowLeftStartOnRectangleIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const menus = [
    {
        name: 'Dashboard',
        route: '/admin/dashboard',
        icon: HomeIcon,
    },
    {
        name: 'Data Pegawai',
        route: '/admin/pegawai',
        icon: UsersIcon,
    },
    {
        name: 'Persetujuan Absensi',
        route: '/admin/persetujuan',
        icon: ClipboardDocumentCheckIcon,
    },
    {
        name: 'Riwayat Absensi',
        route: '/admin/riwayat',
        icon: ClockIcon,
    },
    {
        name: 'Laporan',
        route: '/admin/laporan',
        icon: DocumentChartBarIcon,
    },
    {
        name: 'Pengaturan',
        route: '/admin/pengaturan',
        icon: Cog6ToothIcon,
    },
]
</script>

<template>
    <aside
        class="w-72 min-h-screen bg-gradient-to-b from-emerald-600 to-emerald-700 text-white flex flex-col shadow-2xl">

        <!-- Logo -->

        <div class="px-8 py-8 border-b border-white/20">

            <div class="flex items-center gap-4">

                <div
                    class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-3xl">

                    🏥

                </div>

                <div>

                    <h1 class="font-bold text-2xl">

                        SIMABS

                    </h1>

                    <p class="text-sm text-emerald-100">

                        RSUD Cibabat

                    </p>

                </div>

            </div>

        </div>

        <!-- Menu -->

        <div class="flex-1 px-5 py-6">

            <p class="uppercase text-xs tracking-widest text-emerald-200 mb-4">

                Menu

            </p>

            <nav class="space-y-2">

                <Link
                    v-for="menu in menus"
                    :key="menu.name"
                    :href="menu.route"
                    class="flex items-center gap-4 px-5 py-3 rounded-xl transition duration-200"
                    :class="page.url === menu.route
                        ? 'bg-white text-emerald-700 shadow-lg'
                        : 'hover:bg-white/10'">

                    <component
                        :is="menu.icon"
                        class="w-6 h-6" />

                    <span class="font-medium">

                        {{ menu.name }}

                    </span>

                </Link>

            </nav>

        </div>

        <!-- Footer -->

        <div class="px-5 pb-8">

            <Link
                href="/logout"
                method="post"
                as="button"
                class="w-full flex items-center gap-4 px-5 py-3 rounded-xl bg-red-500 hover:bg-red-600 transition">

                <ArrowLeftStartOnRectangleIcon
                    class="w-6 h-6" />

                Logout

            </Link>

        </div>

    </aside>
</template>
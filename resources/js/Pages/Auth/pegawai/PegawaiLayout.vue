<script setup>
import { ref, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';

import {
    Menu,
    Bell,
    UserCircle,
    ChevronDown,
    Home,
    Clock,
    FileText,
    BarChart3,
    Settings,
    LogOut,
} from 'lucide-vue-next';


// =====================================================
// SIDEBAR STATE
// =====================================================

const sidebarCollapsed = ref(false);
const profileOpen = ref(false);


// Ambil kondisi sidebar terakhir dari browser
onMounted(() => {
    const savedState = localStorage.getItem('pegawai_sidebar_collapsed');

    if (savedState !== null) {
        sidebarCollapsed.value = savedState === 'true';
    }
});


// Simpan kondisi sidebar setiap kali berubah
watch(sidebarCollapsed, (value) => {
    localStorage.setItem(
        'pegawai_sidebar_collapsed',
        value ? 'true' : 'false'
    );
});


// Toggle sidebar
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;

    // Tutup dropdown profile kalau sidebar berubah
    profileOpen.value = false;
};


// Toggle profile dropdown
const toggleProfile = () => {
    profileOpen.value = !profileOpen.value;
};


// =====================================================
// MENU
// =====================================================

const menuItems = [
    {
        name: 'Dashboard',
        route: 'pegawai.dashboard',
        icon: Home,
    },
    {
        name: 'Absensi',
        route: 'pegawai.absensi',
        icon: Clock,
    },
    {
        name: 'Riwayat Absensi',
        route: 'pegawai.riwayat',
        icon: FileText,
    },
    {
        name: 'Laporan',
        route: 'pegawai.laporan',
        icon: BarChart3,
    },
    {
        name: 'Pengaturan',
        route: 'pegawai.pengaturan',
        icon: Settings,
    },
];


// Cek menu aktif
const isActive = (routeName) => {
    return route().current(routeName);
};
</script>


<template>
    <div class="min-h-screen bg-[#f4fbf9] text-slate-800">

        <!-- ================================================= -->
        <!-- SIDEBAR -->
        <!-- ================================================= -->

        <aside
            class="fixed left-0 top-0 z-40 h-screen bg-gradient-to-b from-[#13b8a6] to-[#18c768] text-white transition-all duration-300"
            :class="sidebarCollapsed ? 'w-[110px]' : 'w-[335px]'"
        >

            <!-- ========================= -->
            <!-- LOGO + TOGGLE -->
            <!-- ========================= -->

            <div
                class="flex h-[122px] border-b border-white/15"
                :class="
                    sidebarCollapsed
                        ? 'items-center justify-center'
                        : 'items-center px-8'
                "
            >

                <!-- TOMBOL HAMBURGER -->
                <button
                    @click="toggleSidebar"
                    class="flex h-[56px] w-[56px] shrink-0 items-center justify-center rounded-2xl bg-white/15 transition hover:bg-white/25"
                    title="Menu"
                >
                    <Menu
                        class="h-7 w-7"
                        stroke-width="2"
                    />
                </button>


                <!-- NAMA SIMABS -->
                <div
                    v-if="!sidebarCollapsed"
                    class="ml-4 overflow-hidden whitespace-nowrap"
                >
                    <h1 class="text-xl font-bold tracking-wide">
                        SIMABS
                    </h1>

                    <p class="text-sm text-white/80">
                        RSUD Cibabat
                    </p>
                </div>

            </div>


            <!-- ========================= -->
            <!-- ROLE -->
            <!-- ========================= -->

            <div
                v-if="!sidebarCollapsed"
                class="px-8 pt-6 pb-3"
            >
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-medium"
                >
                    <span
                        class="h-2 w-2 rounded-full bg-white"
                    ></span>

                    Pegawai
                </div>
            </div>


            <!-- ========================= -->
            <!-- MENU -->
            <!-- ========================= -->

            <nav
                class="space-y-2"
                :class="
                    sidebarCollapsed
                        ? 'px-3 pt-6'
                        : 'px-4 pt-3'
                "
            >

                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :title="sidebarCollapsed ? item.name : ''"
                    class="group flex h-[56px] rounded-xl transition-all duration-200"
                    :class="[
                        isActive(item.route)
                            ? 'bg-white text-[#13aa9c] shadow-md'
                            : 'text-white hover:bg-white/10',

                        sidebarCollapsed
                            ? 'items-center justify-center'
                            : 'items-center px-4'
                    ]"
                >

                    <!-- ICON -->
                    <component
                        :is="item.icon"
                        class="h-6 w-6 shrink-0"
                        stroke-width="1.8"
                    />

                    <!-- TEXT -->
                    <span
                        v-if="!sidebarCollapsed"
                        class="ml-4 whitespace-nowrap text-base font-medium"
                    >
                        {{ item.name }}
                    </span>

                </Link>

            </nav>


            <!-- ========================= -->
            <!-- LOGOUT -->
            <!-- ========================= -->

            <div
                class="absolute bottom-0 left-0 right-0 border-t border-white/15 p-4"
            >

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    :title="sidebarCollapsed ? 'Keluar' : ''"
                    class="flex h-[54px] w-full rounded-xl text-white transition hover:bg-white/10"
                    :class="
                        sidebarCollapsed
                            ? 'items-center justify-center'
                            : 'items-center px-4'
                    "
                >

                    <LogOut
                        class="h-6 w-6 shrink-0"
                        stroke-width="1.8"
                    />

                    <span
                        v-if="!sidebarCollapsed"
                        class="ml-4 text-base font-medium"
                    >
                        Keluar
                    </span>

                </Link>

            </div>

        </aside>


        <!-- ================================================= -->
        <!-- MAIN CONTENT -->
        <!-- ================================================= -->

        <div
            class="min-h-screen transition-all duration-300"
            :class="
                sidebarCollapsed
                    ? 'ml-[110px]'
                    : 'ml-[335px]'
            "
        >

            <!-- ========================= -->
            <!-- TOPBAR -->
            <!-- ========================= -->

            <header
                class="sticky top-0 z-30 flex h-[90px] items-center justify-between border-b border-slate-200 bg-white px-8"
            >

                <!-- TANGGAL -->
                <div>
                    <p class="text-sm text-slate-500">
                        Kamis, 30 Juli 2026
                    </p>
                </div>


                <!-- ========================= -->
                <!-- RIGHT SIDE -->
                <!-- ========================= -->

                <div class="flex items-center gap-6">

                    <!-- NOTIFICATION -->
                    <button
                        class="relative flex h-10 w-10 items-center justify-center rounded-full hover:bg-slate-100"
                    >

                        <Bell
                            class="h-6 w-6 text-slate-500"
                            stroke-width="1.7"
                        />

                        <span
                            class="absolute right-1 top-1 h-2.5 w-2.5 rounded-full bg-red-500"
                        ></span>

                    </button>


                    <!-- PROFILE -->
                    <div class="relative">

                        <button
                            @click="toggleProfile"
                            class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-slate-50"
                        >

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-full bg-teal-50"
                            >

                                <UserCircle
                                    class="h-8 w-8 text-[#13b8a6]"
                                    stroke-width="1.5"
                                />

                            </div>


                            <div class="hidden text-left md:block">

                                <p
                                    class="text-sm font-semibold text-slate-800"
                                >
                                    Dr. Ahmad Fauzi
                                </p>

                            </div>


                            <ChevronDown
                                class="h-4 w-4 text-slate-400 transition"
                                :class="
                                    profileOpen
                                        ? 'rotate-180'
                                        : ''
                                "
                            />

                        </button>


                        <!-- ========================= -->
                        <!-- PROFILE DROPDOWN -->
                        <!-- ========================= -->

                        <div
                            v-if="profileOpen"
                            class="absolute right-0 top-14 w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-xl"
                        >

                            <Link
    :href="route('pegawai.profile')"
    class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-slate-50"
    @click="profileOpen = false"
>
    <UserCircle
        class="h-5 w-5"
    />

    Profile Saya
</Link>


                            <div
                                class="my-1 border-t border-slate-100"
                            ></div>


                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-red-500 transition hover:bg-red-50"
                            >

                                <LogOut
                                    class="h-5 w-5"
                                />

                                Logout

                            </Link>

                        </div>

                    </div>

                </div>

            </header>


            <!-- ========================= -->
            <!-- CONTENT -->
            <!-- ========================= -->

            <main class="p-8">
                <slot />
            </main>

        </div>

    </div>
</template>
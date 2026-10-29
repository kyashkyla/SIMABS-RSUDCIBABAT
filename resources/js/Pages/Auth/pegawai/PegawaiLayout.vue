<script setup>
import { ref, onMounted, onUnmounted, watch, computed } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';

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
    CheckCircle as CheckCircleIcon,
} from 'lucide-vue-next';


// =====================================================
// USER YANG SEDANG LOGIN
// =====================================================

const page = usePage();
const authUser = computed(() => page.props.auth?.user);


// =====================================================
// TANGGAL HARI INI (otomatis, tidak manual lagi)
// =====================================================

const now = ref(new Date());
let clockInterval = null;

// Format: "Kamis, 27 Agustus 2026"
const tanggalHariIni = computed(() => {
    return now.value.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
});


// =====================================================
// SIDEBAR STATE
// =====================================================

const sidebarCollapsed = ref(false);
const profileOpen = ref(false);
const notifOpen = ref(false);


// Ambil kondisi sidebar terakhir dari browser
onMounted(() => {
    const savedState = localStorage.getItem('pegawai_sidebar_collapsed');

    if (savedState !== null) {
        sidebarCollapsed.value = savedState === 'true';
    }

    // Perbarui tanggal setiap menit, jadi otomatis ganti begitu hari berganti
    // tanpa perlu reload halaman
    clockInterval = setInterval(() => {
        now.value = new Date();
    }, 60 * 1000);
});

onUnmounted(() => {
    if (clockInterval) {
        clearInterval(clockInterval);
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
// (short = label pendek untuk menu bawah di HP)
// =====================================================

const menuItems = [
    {
        name: 'Dashboard',
        short: 'Beranda',
        route: 'pegawai.dashboard',
        icon: Home,
    },
    {
        name: 'Absensi',
        short: 'Absen',
        route: 'pegawai.absensi',
        icon: Clock,
    },
    {
        name: 'Riwayat Absensi',
        short: 'Riwayat',
        route: 'pegawai.riwayat',
        icon: FileText,
    },
    {
        name: 'Laporan',
        short: 'Laporan',
        route: 'pegawai.laporan',
        icon: BarChart3,
    },
    {
        name: 'Pengaturan',
        short: 'Setelan',
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
        <!-- SIDEBAR (khusus desktop) -->
        <!-- ================================================= -->

        <aside
            class="fixed left-0 top-0 z-40 hidden h-screen bg-gradient-to-b from-[#13b8a6] to-[#18c768] text-white transition-all duration-300 lg:block"
            :class="sidebarCollapsed ? 'w-[110px]' : 'w-[335px]'"
        >

            <!-- LOGO + TOGGLE -->

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


            <!-- ROLE -->

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


            <!-- MENU -->

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


            <!-- LOGOUT -->

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
                    ? 'lg:ml-[110px]'
                    : 'lg:ml-[335px]'
            "
        >

            <!-- TOPBAR -->

            <header
                class="sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 pt-[env(safe-area-inset-top)] sm:px-6 lg:h-[90px] lg:px-8"
            >

                <!-- TANGGAL (otomatis mengikuti tanggal hari ini) -->
                <div class="min-w-0">
                    <p class="truncate text-xs text-slate-500 sm:text-sm">
                        {{ tanggalHariIni }}
                    </p>
                </div>


                <!-- RIGHT SIDE -->

                <div class="flex shrink-0 items-center gap-2 sm:gap-6">

                    <!-- NOTIFICATION -->
                    <div class="relative">
                        <button
                            @click="notifOpen = !notifOpen"
                            class="relative flex h-10 w-10 items-center justify-center rounded-full hover:bg-slate-100"
                        >
                            <Bell class="h-6 w-6 text-slate-500" stroke-width="1.7" />
                            <span v-if="page.props.notifications?.unreadCount > 0" class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] text-white">
                                {{ page.props.notifications.unreadCount }}
                            </span>
                        </button>

                        <div v-if="notifOpen" class="fixed left-3 right-3 top-[68px] z-50 overflow-hidden rounded-xl border border-slate-200 bg-white py-2 shadow-xl sm:absolute sm:left-auto sm:right-0 sm:top-14 sm:w-80">
                            <div class="px-4 py-2 border-b border-slate-100 flex justify-between items-center">
                                <span class="font-bold text-sm">Notifikasi</span>
                            </div>

                            <div class="max-h-64 overflow-y-auto">
                                <div v-if="page.props.notifications?.latest?.length === 0" class="p-4 text-center text-sm text-slate-500">
                                    Belum ada notifikasi.
                                </div>
                                <button
                                    v-for="notif in page.props.notifications?.latest"
                                    :key="notif.id"
                                    @click="router.put(route('notifications.read', notif.id)); notifOpen = false;"
                                    class="w-full text-left px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition flex gap-3"
                                    :class="!notif.read && 'bg-emerald-50/50'"
                                >
                                    <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0" :class="notif.read ? 'bg-slate-200' : 'bg-emerald-500'"></span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-slate-800">{{ notif.title }}</p>
                                        <p class="text-xs text-slate-600 mt-1">{{ notif.message }}</p>
                                        <p class="text-[10px] text-slate-400 mt-1">{{ notif.time }}</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>


                    <!-- PROFILE -->
                    <div class="relative">

                        <button
                            @click="toggleProfile"
                            class="flex items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-slate-50"
                        >

                            <div
                                class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-full bg-teal-50"
                            >

                                <img
                                    v-if="authUser?.photo"
                                    :src="authUser.photo"
                                    alt="Foto profil"
                                    class="h-full w-full object-cover"
                                />

                                <UserCircle
                                    v-else
                                    class="h-8 w-8 text-[#13b8a6]"
                                    stroke-width="1.5"
                                />

                            </div>


                            <div class="hidden text-left md:block">

                                <p
                                    class="text-sm font-semibold text-slate-800"
                                >
                                    {{ authUser?.name }}
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


                        <!-- PROFILE DROPDOWN -->

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


            <!-- CONTENT -->

            <main class="p-4 pb-28 sm:p-6 sm:pb-28 lg:p-8 lg:pb-8">
                <slot />
            </main>

        </div>


        <!-- ================================================= -->
        <!-- MENU BAWAH (khusus HP & tablet, desktop pakai sidebar) -->
        <!-- ================================================= -->

        <nav
            class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_16px_rgba(0,0,0,0.06)] lg:hidden"
            aria-label="Menu utama"
        >
            <div class="mx-auto grid max-w-xl grid-cols-5">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="route(item.route)"
                    class="flex min-h-[56px] flex-col items-center justify-center gap-1 px-1 py-2 text-[11px] font-medium transition"
                    :class="isActive(item.route) ? 'text-[#13aa9c]' : 'text-slate-400'"
                >
                    <component
                        :is="item.icon"
                        class="h-6 w-6"
                        :stroke-width="isActive(item.route) ? 2.2 : 1.7"
                    />
                    <span class="w-full truncate text-center">{{ item.short }}</span>
                </Link>
            </div>
        </nav>


        <!-- ================================================= -->
        <!-- TOAST / FLASH MESSAGES -->
        <!-- ================================================= -->
        <div v-if="page.props.flash?.success" class="fixed bottom-24 left-4 right-4 z-[60] flex items-center gap-3 rounded-xl bg-teal-500 px-5 py-4 text-white shadow-xl sm:left-auto sm:right-6 lg:bottom-6">
            <CheckCircleIcon class="h-6 w-6 text-white" />
            <div>
                <p class="font-bold text-sm">Berhasil</p>
                <p class="text-xs">{{ page.props.flash.success }}</p>
            </div>
            <button @click="page.props.flash.success = null" class="ml-4 text-white hover:text-slate-200">
                &times;
            </button>
        </div>

    </div>
</template>
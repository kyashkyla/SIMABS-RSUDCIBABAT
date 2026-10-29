<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
    HomeIcon,
    UsersIcon,
    ClipboardDocumentCheckIcon,
    ClockIcon,
    DocumentChartBarIcon,
    Cog6ToothIcon,
    ArrowLeftStartOnRectangleIcon,
    Bars3Icon,
} from '@heroicons/vue/24/outline'
import { mobileNavOpen } from '@/lib/adminNav'

const page = usePage()

// ==============================
// SIDEBAR OPEN / CLOSE
// ==============================
const isCollapsed = ref(false)

const menus = [
    { name: 'Dashboard', route: '/admin/dashboard', icon: HomeIcon },
    { name: 'Data Pegawai', route: '/admin/pegawai', icon: UsersIcon },
    { name: 'Persetujuan Absensi', route: '/admin/persetujuan-absensi', icon: ClipboardDocumentCheckIcon },
    { name: 'Riwayat Absensi', route: '/admin/absensi', icon: ClockIcon },
    { name: 'Laporan', route: '/admin/laporan', icon: DocumentChartBarIcon },
    { name: 'Pengaturan', route: '/admin/pengaturan', icon: Cog6ToothIcon },
]

// Di HP sidebar jadi panel geser (drawer), jadi mode "ciut" (ikon saja)
// hanya berlaku di desktop (>= lg).
const desktopQuery = window.matchMedia('(min-width: 1024px)')
const isDesktop = ref(desktopQuery.matches)
const onQueryChange = (e) => {
    isDesktop.value = e.matches
    if (e.matches) mobileNavOpen.value = false
}
onMounted(() => desktopQuery.addEventListener('change', onQueryChange))
onUnmounted(() => desktopQuery.removeEventListener('change', onQueryChange))

const collapsed = computed(() => isCollapsed.value && isDesktop.value)

// Pindah halaman -> tutup panel di HP
watch(() => page.url, () => {
    mobileNavOpen.value = false
})

const toggleSidebar = () => {
    if (isDesktop.value) {
        isCollapsed.value = !isCollapsed.value
    } else {
        mobileNavOpen.value = false
    }
}
</script>

<template>

    <aside
        class="fixed inset-y-0 left-0 z-50 w-[85vw] max-w-xs overflow-y-auto bg-gradient-to-b from-emerald-600 to-emerald-700 text-white flex flex-col shadow-2xl transition-all duration-300 ease-in-out flex-shrink-0 lg:static lg:z-auto lg:max-w-none lg:translate-x-0 lg:overflow-visible lg:min-h-screen"
        :class="[
            collapsed ? 'lg:w-24' : 'lg:w-80',
            mobileNavOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >

        <!-- HEADER SIDEBAR -->
        <div
            class="h-32 border-b border-white/20 flex items-center transition-all duration-300"
            :class="collapsed ? 'justify-center px-4' : 'justify-between px-7'"
        >
            <div v-if="!collapsed" class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl">
                    🏥
                </div>
                <div>
                    <h1 class="font-bold text-2xl">SIMABS</h1>
                    <p class="text-sm text-emerald-100">RSUD Cibabat</p>
                </div>
            </div>

            <div
                v-else
                class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl"
            >
                🏥
            </div>

            <button
                type="button"
                @click="toggleSidebar"
                class="w-12 h-12 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition duration-200"
                title="Buka/Tutup Sidebar"
            >
                <Bars3Icon class="w-7 h-7" />
            </button>
        </div>


        <!-- STATUS ADMIN -->
        <div
            class="transition-all duration-300"
            :class="collapsed ? 'px-3 py-6' : 'px-7 py-7'"
        >
            <div
                class="bg-white/10 border border-white/10 rounded-full py-2 flex items-center"
                :class="collapsed ? 'justify-center' : 'justify-center gap-2 px-4'"
            >
                <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                <span v-if="!collapsed" class="font-medium">Administrator</span>
            </div>
        </div>


        <!-- MENU -->
        <div
            class="flex-1 transition-all duration-300"
            :class="collapsed ? 'px-3' : 'px-4'"
        >
            <p
                v-if="!collapsed"
                class="uppercase text-xs tracking-widest text-emerald-200 px-3 mb-4"
            >
                Menu
            </p>

            <nav class="space-y-2">
                <Link
                    v-for="menu in menus"
                    :key="menu.name"
                    :href="menu.route"
                    class="flex items-center rounded-xl transition-all duration-200"
                    :class="[
                        page.url === menu.route
                            ? 'bg-white text-emerald-700 shadow-lg'
                            : 'text-white hover:bg-white/10',

                        collapsed
                            ? 'justify-center w-full h-14'
                            : 'gap-4 px-5 py-3.5'
                    ]"
                >
                    <component :is="menu.icon" class="w-6 h-6 flex-shrink-0" />

                    <span v-if="!collapsed" class="font-medium whitespace-nowrap">
                        {{ menu.name }}
                    </span>
                </Link>
            </nav>
        </div>


        <!-- LOGOUT -->
        <div
            class="pb-7 transition-all duration-300"
            :class="collapsed ? 'px-3' : 'px-4'"
        >
            <Link
                href="/logout"
                method="post"
                as="button"
                class="flex items-center rounded-xl bg-red-500 hover:bg-red-600 transition duration-200"
                :class="collapsed
                    ? 'justify-center w-full h-14'
                    : 'w-full gap-4 px-5 py-3.5'"
            >
                <ArrowLeftStartOnRectangleIcon class="w-6 h-6 flex-shrink-0" />

                <span v-if="!collapsed" class="font-medium">Logout</span>
            </Link>
        </div>

    </aside>

</template>
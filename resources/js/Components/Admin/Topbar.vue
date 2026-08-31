<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

import {
    BellIcon,
    ChevronDownIcon,
    UserCircleIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

const authUser = computed(() => page.props.auth?.user)
const notifications = computed(() => page.props.notifications ?? { unreadCount: 0, latest: [] })

const showProfileMenu = ref(false)
const showNotifMenu = ref(false)

const today = ref('')

const updateDate = () => {
    today.value = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

const toggleProfile = () => {
    showProfileMenu.value = !showProfileMenu.value
    showNotifMenu.value = false
}

const toggleNotif = () => {
    showNotifMenu.value = !showNotifMenu.value
    showProfileMenu.value = false
}

const closeMenus = (event) => {
    if (!event.target.closest('.profile-menu')) {
        showProfileMenu.value = false
    }
    if (!event.target.closest('.notif-menu')) {
        showNotifMenu.value = false
    }
}

const openNotification = (notif) => {
    router.put(route('notifications.read', notif.id))
    showNotifMenu.value = false
}

const markAllRead = () => {
    router.put(route('notifications.read-all'), {}, { preserveScroll: true })
}

onMounted(() => {
    updateDate()
    document.addEventListener('click', closeMenus)
})

onUnmounted(() => {
    document.removeEventListener('click', closeMenus)
})
</script>

<template>

    <header class="bg-white border-b border-slate-200 shadow-sm">

        <div
            class="h-24 px-8 flex items-center justify-between">

            <!-- ================= LEFT ================= -->

            <div>

                <p class="text-sm text-slate-400 mb-1">
                    {{ today }}
                </p>

                <h1 class="text-xl font-semibold text-slate-700">
                    Dashboard Administrator
                </h1>

            </div>


            <!-- ================= RIGHT ================= -->

            <div class="flex items-center gap-5">

                <!-- Notifikasi -->

                <div class="relative notif-menu">

                    <button
                        type="button"
                        @click.stop="toggleNotif"
                        class="relative w-11 h-11 rounded-full
                               hover:bg-slate-100
                               flex items-center justify-center
                               transition">

                        <BellIcon
                            class="w-6 h-6 text-slate-600"
                        />

                        <!-- Notification Dot -->

                        <span
                            v-if="notifications.unreadCount > 0"
                            class="absolute top-2 right-2
                                   w-2.5 h-2.5
                                   bg-red-500
                                   rounded-full
                                   border-2 border-white">
                        </span>

                    </button>

                    <!-- ================= DROPDOWN NOTIFIKASI ================= -->

                    <div
                        v-if="showNotifMenu"
                        class="absolute right-0 top-14
                               w-80
                               bg-white
                               rounded-xl
                               shadow-xl
                               border border-slate-100
                               z-50">

                        <div class="flex items-center justify-between px-4 py-3 border-b">

                            <p class="text-sm font-semibold text-slate-700">
                                Notifikasi
                            </p>

                            <button
                                v-if="notifications.unreadCount > 0"
                                type="button"
                                @click="markAllRead"
                                class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">
                                Tandai semua dibaca
                            </button>

                        </div>

                        <div class="max-h-80 overflow-y-auto">

                            <p
                                v-if="!notifications.latest || notifications.latest.length === 0"
                                class="px-4 py-6 text-sm text-slate-400 text-center">
                                Belum ada notifikasi.
                            </p>

                            <button
                                v-for="notif in notifications.latest"
                                :key="notif.id"
                                type="button"
                                @click="openNotification(notif)"
                                class="w-full text-left px-4 py-3 border-b last:border-b-0 hover:bg-slate-50 transition flex gap-3"
                                :class="!notif.read && 'bg-emerald-50/50'">

                                <span
                                    class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0"
                                    :class="notif.read ? 'bg-slate-200' : 'bg-emerald-500'">
                                </span>

                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate">
                                        {{ notif.title }}
                                    </p>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">
                                        {{ notif.message }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        {{ notif.time }}
                                    </p>
                                </div>

                            </button>

                        </div>

                    </div>

                </div>


                <!-- Garis Pemisah -->

                <div class="h-10 w-px bg-slate-200">
                </div>


                <!-- ================= PROFILE ================= -->

                <div class="relative profile-menu">

                    <button
                        type="button"
                        @click.stop="toggleProfile"
                        class="flex items-center gap-3
                               px-2 py-1.5
                               rounded-xl
                               hover:bg-slate-50
                               transition">

                        <!-- Avatar -->

                        <div
                            class="w-11 h-11
                                   rounded-full
                                   bg-emerald-50
                                   border border-emerald-100
                                   flex items-center justify-center">

                            <UserCircleIcon
                                class="w-7 h-7 text-emerald-500"
                            />

                        </div>


                        <!-- Nama -->

                        <div class="text-left hidden sm:block">

                            <p class="text-sm font-semibold text-slate-700">
                                {{ authUser?.name ?? 'Administrator' }}
                            </p>

                            <p class="text-xs text-slate-400">
                                {{ authUser?.role === 'admin' ? 'Admin Sistem' : authUser?.role }}
                            </p>

                        </div>


                        <!-- Arrow -->

                        <ChevronDownIcon
                            class="w-4 h-4 text-slate-400
                                   transition-transform"
                            :class="{
                                'rotate-180': showProfileMenu
                            }"
                        />

                    </button>


                    <!-- ================= DROPDOWN ================= -->

                    <div
                        v-if="showProfileMenu"
                        class="absolute right-0 top-14
                               w-56
                               bg-white
                               rounded-xl
                               shadow-xl
                               border border-slate-100
                               py-2
                               z-50">

                        <div class="px-4 py-3 border-b">

                            <p class="text-sm font-semibold text-slate-700">
                                {{ authUser?.name ?? 'Administrator' }}
                            </p>

                            <p class="text-xs text-slate-400">
                                {{ authUser?.email }}
                            </p>

                        </div>


                        <a
                            href="/admin/pengaturan"
                            class="block px-4 py-3
                                   text-sm text-slate-600
                                   hover:bg-slate-50
                                   transition">

                            Pengaturan Akun

                        </a>


                        <div class="border-t my-1">
                        </div>


                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            class="block w-full text-left px-4 py-3
                                   text-sm text-red-500
                                   hover:bg-red-50
                                   transition">

                            Keluar

                        </Link>

                    </div>

                </div>

            </div>

        </div>

    </header>

</template>
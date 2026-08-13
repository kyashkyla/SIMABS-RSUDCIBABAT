<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

import {
    BellIcon,
    ChevronDownIcon,
    UserCircleIcon,
} from '@heroicons/vue/24/outline'

const showProfileMenu = ref(false)

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
}

const closeProfile = (event) => {
    if (!event.target.closest('.profile-menu')) {
        showProfileMenu.value = false
    }
}

onMounted(() => {
    updateDate()
    document.addEventListener('click', closeProfile)
})

onUnmounted(() => {
    document.removeEventListener('click', closeProfile)
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

                <button
                    type="button"
                    class="relative w-11 h-11 rounded-full
                           hover:bg-slate-100
                           flex items-center justify-center
                           transition">

                    <BellIcon
                        class="w-6 h-6 text-slate-600"
                    />

                    <!-- Notification Dot -->

                    <span
                        class="absolute top-2 right-2
                               w-2.5 h-2.5
                               bg-red-500
                               rounded-full
                               border-2 border-white">
                    </span>

                </button>


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
                                Administrator
                            </p>

                            <p class="text-xs text-slate-400">
                                Super Admin
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
                                Administrator
                            </p>

                            <p class="text-xs text-slate-400">
                                Admin Sistem
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


                        <a
                            href="/admin/logout"
                            class="block px-4 py-3
                                   text-sm text-red-500
                                   hover:bg-red-50
                                   transition">

                            Keluar

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </header>

</template>
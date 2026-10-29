<script setup>
import Sidebar from '@/Components/Admin/Sidebar.vue'
import Topbar from '@/Components/Admin/Topbar.vue'
import { mobileNavOpen } from '@/lib/adminNav'
</script>

<template>
    <div class="flex h-screen bg-slate-100">

        <!-- Latar gelap saat menu terbuka di HP -->
        <div
            v-if="mobileNavOpen"
            class="fixed inset-0 z-40 bg-black/50 lg:hidden"
            @click="mobileNavOpen = false"
        ></div>

        <!-- Sidebar -->
        <Sidebar />

        <!-- Content -->
        <div class="flex-1 min-w-0 flex flex-col overflow-hidden">

            <!-- Topbar -->
            <Topbar />

            <!-- Isi Halaman -->
            <main class="flex-1 overflow-y-auto p-3 sm:p-6">

                <slot />

            </main>

        </div>

    </div>
</template>
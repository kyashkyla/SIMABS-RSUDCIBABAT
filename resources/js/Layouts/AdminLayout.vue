<script setup>
import Sidebar from '@/Components/Admin/Sidebar.vue'
import Topbar from '@/Components/Admin/Topbar.vue'
</script>

<template>
    <div class="flex h-screen bg-slate-100">

        <!-- Sidebar -->
        <Sidebar />

        <!-- Content -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Topbar -->
            <Topbar />

            <!-- Isi Halaman -->
            <main class="flex-1 overflow-y-auto p-6">

                <slot />

            </main>

        </div>

    </div>
</template>
<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Reset password mandiri (via email) dimatikan untuk pegawai/admin SIMABS.
// Password hanya boleh direset oleh admin lewat menu "Reset Password"
// di Data Pegawai, supaya lebih aman dan terkontrol.
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Password" />

        <div class="mb-4 text-sm text-gray-700">
            Untuk alasan keamanan, password akun SIMABS
            <strong>tidak dapat direset secara mandiri</strong> lewat email.
        </div>

        <div class="mb-6 text-sm text-gray-700">
            Silakan hubungi <strong>Administrator SIMABS RSUD Cibabat</strong>
            untuk mengajukan reset password akun Anda.
        </div>

        <div class="flex items-center justify-end">
            <Link
                href="/login"
                class="text-sm text-emerald-600 hover:underline"
            >
                Kembali ke halaman login
            </Link>
        </div>
    </GuestLayout>
</template>
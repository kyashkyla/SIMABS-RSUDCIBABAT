<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import {
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

import PegawaiLayout from './PegawaiLayout.vue';

const props = defineProps({
    histories: {
        type: Array,
        default: () => [],
    },
});

// Label status buat ditampilkan (samakan istilah dengan tabel `attendances`)
const statusLabel = {
    hadir: 'Hadir',
    terlambat: 'Terlambat',
    alternatif: 'Izin',
};

const methodLabel = {
    face: 'Face Recognition',
    otp: 'OTP Code',
    alternative: 'Absensi Alternatif',
};

// Susun ulang data dari server: format tanggal ke Bahasa Indonesia,
// terjemahkan status & metode.
const rows = computed(() =>
    props.histories.map((item) => {
        const tanggal = new Date(`${item.attendance_date}T00:00:00`);

        return {
            ...item,
            dateLabel: tanggal.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            }),
            statusLabel: statusLabel[item.status] ?? item.status,
            methodLabel: methodLabel[item.method] ?? (item.method ?? '—'),
        };
    })
);
</script>

<template>
    <Head title="Riwayat Absensi" />

    <PegawaiLayout>

        <div>
            <h1 class="text-2xl font-bold">
                Riwayat Absensi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Lihat riwayat kehadiran Anda
            </p>
        </div>

        <div
            class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm"
        >

            <div class="border-b border-slate-100 p-5">
                <h2 class="font-semibold">
                    Riwayat Kehadiran
                </h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-4">
                                Tanggal
                            </th>

                            <th class="px-5 py-4">
                                Jam Masuk
                            </th>

                            <th class="px-5 py-4">
                                Metode
                            </th>

                            <th class="px-5 py-4">
                                Status
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr
                            v-for="item in rows"
                            :key="item.attendance_date"
                            class="border-t border-slate-100"
                        >

                            <td class="px-5 py-4 font-medium">
                                {{ item.dateLabel }}
                            </td>

                            <td class="px-5 py-4">
                                {{ item.check_in_at ?? '—' }}
                            </td>

                            <td class="px-5 py-4 text-slate-500">
                                {{ item.methodLabel }}
                            </td>

                            <td class="px-5 py-4">

                                <span
                                    v-if="item.status === 'hadir'"
                                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-600"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                    Hadir
                                </span>

                                <span
                                    v-else-if="item.status === 'terlambat'"
                                    class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-600"
                                >
                                    <ExclamationTriangleIcon class="h-4 w-4" />
                                    Terlambat
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-600"
                                >
                                    <ClockIcon class="h-4 w-4" />
                                    {{ item.statusLabel }}
                                </span>

                            </td>

                        </tr>

                        <tr v-if="rows.length === 0">
                            <td
                                colspan="4"
                                class="px-5 py-10 text-center text-sm text-slate-400"
                            >
                                Belum ada riwayat absensi.
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </PegawaiLayout>
</template>
<script setup>
import { Head } from '@inertiajs/vue3';
import {
    ClockIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';

import PegawaiLayout from './PegawaiLayout.vue';

const histories = [
    {
        date: '30 Juli 2026',
        masuk: '07:52',
        keluar: '16:05',
        method: 'Face Recognition',
        status: 'Hadir',
    },
    {
        date: '29 Juli 2026',
        masuk: '07:55',
        keluar: '16:10',
        method: 'Face Recognition',
        status: 'Hadir',
    },
    {
        date: '28 Juli 2026',
        masuk: '08:15',
        keluar: '16:10',
        method: 'OTP Code',
        status: 'Terlambat',
    },
    {
        date: '27 Juli 2026',
        masuk: '07:49',
        keluar: '16:02',
        method: 'Face Recognition',
        status: 'Hadir',
    },
];
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
                                Jam Keluar
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
                            v-for="item in histories"
                            :key="item.date"
                            class="border-t border-slate-100"
                        >

                            <td class="px-5 py-4 font-medium">
                                {{ item.date }}
                            </td>

                            <td class="px-5 py-4">
                                {{ item.masuk }}
                            </td>

                            <td class="px-5 py-4">
                                {{ item.keluar }}
                            </td>

                            <td class="px-5 py-4 text-slate-500">
                                {{ item.method }}
                            </td>

                            <td class="px-5 py-4">

                                <span
                                    v-if="item.status === 'Hadir'"
                                    class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-600"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                    Hadir
                                </span>

                                <span
                                    v-else
                                    class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-600"
                                >
                                    <ExclamationTriangleIcon class="h-4 w-4" />
                                    Terlambat
                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </PegawaiLayout>
</template>
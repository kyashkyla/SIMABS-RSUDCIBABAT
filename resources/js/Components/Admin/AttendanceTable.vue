<script setup>
import { EyeIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    attendances: {
        type: Array,
        default: () => [],
    },
})

const badgeColor = (status)=>{

    switch(status){

        case 'Hadir':
            return 'bg-green-100 text-green-700'

        case 'Alternatif':
            return 'bg-yellow-100 text-yellow-700'

        case 'Tidak Hadir':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-gray-100 text-gray-700'

    }

}
</script>

<template>

<div class="bg-white rounded-2xl shadow-lg">

    <!-- Header -->

    <div class="flex justify-between items-center px-6 py-5 border-b">

        <div>

            <h2 class="text-xl font-bold text-slate-800">

                Absensi Terbaru

            </h2>

            <p class="text-gray-500 text-sm">

                Data absensi pegawai hari ini

            </p>

        </div>

        <button
            class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white">

            Lihat Semua

        </button>

    </div>

    <!-- Table -->

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-slate-50">

                <tr>

                    <th class="text-left px-6 py-4">Pegawai</th>

                    <th class="text-left">Departemen</th>

                    <th class="text-left">Jam Masuk</th>

                    <th class="text-left">Metode</th>

                    <th class="text-left">Status</th>

                    <th class="text-center">Aksi</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="item in props.attendances"
                    :key="item.id"
                    class="border-b hover:bg-slate-50 transition">

                    <!-- Pegawai -->

                    <td class="px-6 py-5">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-11 h-11 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold">

                                {{ item.nama.charAt(0) }}

                            </div>

                            <div>

                                <h3
                                    class="font-semibold text-slate-700">

                                    {{ item.nama }}

                                </h3>

                                <p
                                    class="text-sm text-gray-400">

                                    ID-{{ item.id }}

                                </p>

                            </div>

                        </div>

                    </td>

                    <td>

                        {{ item.departemen }}

                    </td>

                    <td>

                        {{ item.jam }}

                    </td>

                    <td>

                        <span
                            class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">

                            {{ item.metode }}

                        </span>

                    </td>

                    <td>

                        <span
                            class="px-3 py-1 rounded-full text-sm"
                            :class="badgeColor(item.status)">

                            {{ item.status }}

                        </span>

                    </td>

                    <td class="text-center">

                        <button
                            class="w-10 h-10 rounded-lg bg-slate-100 hover:bg-emerald-500 hover:text-white transition">

                            <EyeIcon class="w-5 h-5 mx-auto"/>

                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

</template>
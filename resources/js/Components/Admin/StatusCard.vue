<script setup>
import { computed } from 'vue'

const props = defineProps({

    hadir: {
        type: Number,
        default: 120
    },

    terlambat: {
        type: Number,
        default: 8
    },

    tidakHadir: {
        type: Number,
        default: 4
    },

    belumAbsen: {
        type: Number,
        default: 0
    },

    total: {
        type: Number,
        default: 132
    }

})

const percent = computed(() => {

    return Math.round(
        (props.hadir / props.total) * 100
    )

})

const circumference = 2 * Math.PI * 55

const offset = computed(() => {

    return circumference - (percent.value / 100) * circumference

})
</script>

<template>

<div class="bg-white rounded-2xl shadow-lg p-6">

    <h2 class="text-xl font-bold text-slate-800 mb-6">

        Status Kehadiran

    </h2>

    <!-- Circular Progress -->

    <div class="flex justify-center mb-8">

        <div class="relative w-44 h-44">

            <svg
                class="w-full h-full -rotate-90">

                <!-- Background -->

                <circle
                    cx="88"
                    cy="88"
                    r="55"
                    stroke="#E5E7EB"
                    stroke-width="12"
                    fill="none"/>

                <!-- Progress -->

                <circle
                    cx="88"
                    cy="88"
                    r="55"
                    stroke="#10B981"
                    stroke-width="12"
                    fill="none"
                    stroke-linecap="round"
                    :stroke-dasharray="circumference"
                    :stroke-dashoffset="offset"/>

            </svg>

            <div
                class="absolute inset-0 flex flex-col items-center justify-center">

                <h1
                    class="text-4xl font-bold text-emerald-600">

                    {{ percent }}%

                </h1>

                <p class="text-gray-500">

                    Kehadiran

                </p>

            </div>

        </div>

    </div>

    <!-- Detail -->

    <div class="space-y-4">

        <div
            class="flex justify-between">

            <span class="text-gray-600">

                Hadir

            </span>

            <span
                class="font-bold text-green-600">

                {{ hadir }}

            </span>

        </div>

        <div
            class="flex justify-between">

            <span class="text-gray-600">

                Terlambat

            </span>

            <span
                class="font-bold text-yellow-500">

                {{ terlambat }}

            </span>

        </div>

        <div
            class="flex justify-between">

            <span class="text-gray-600">

                Tidak Hadir

            </span>

            <span
                class="font-bold text-red-500">

                {{ tidakHadir }}

            </span>

        </div>

        <div
            class="flex justify-between">

            <span class="text-gray-600">

                Belum Absen

            </span>

            <span
                class="font-bold text-blue-500">

                {{ belumAbsen }}

            </span>

        </div>

    </div>

</div>

</template>
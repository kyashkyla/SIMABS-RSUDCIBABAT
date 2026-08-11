<script setup>
import { computed } from 'vue'

import {
    CheckIcon,
    MapPinIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    method: {
        type: String,
        default: 'Face Recognition',
    },
})

const emit = defineEmits(['finish'])

const currentTime = computed(() => {
    return new Date().toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    })
})

const currentDate = computed(() => {
    return new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
})
</script>

<template>
    <div class="mx-auto max-w-3xl">

        <div
            class="rounded-3xl border border-emerald-100 bg-white p-7 text-center shadow-sm md:p-12"
        >

            <!-- SUCCESS ICON -->
            <div
                class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-emerald-400 to-green-500 shadow-lg shadow-emerald-200"
            >
                <CheckIcon
                    class="h-12 w-12 text-white"
                    stroke-width="2.5"
                />
            </div>


            <!-- TITLE -->
            <h2 class="mt-7 text-3xl font-bold text-slate-800">
                Absensi Berhasil!
            </h2>

            <p class="mt-2 text-slate-500">
                Absensi Anda berhasil dicatat oleh sistem.
            </p>


            <!-- INFORMATION -->
            <div
                class="mt-8 grid gap-4 md:grid-cols-3"
            >

                <!-- TIME -->
                <div
                    class="rounded-2xl bg-emerald-50 p-5"
                >
                    <ClockIcon
                        class="mx-auto h-6 w-6 text-emerald-500"
                    />

                    <p class="mt-3 text-xs text-slate-500">
                        Jam Masuk
                    </p>

                    <p class="mt-1 text-lg font-bold text-slate-800">
                        {{ currentTime }}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        {{ currentDate }}
                    </p>
                </div>


                <!-- METHOD -->
                <div
                    class="rounded-2xl bg-emerald-50 p-5"
                >

                    <svg
                        class="mx-auto h-6 w-6 text-emerald-500"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3v18m9-9H3"
                        />
                    </svg>

                    <p class="mt-3 text-xs text-slate-500">
                        Metode
                    </p>

                    <p class="mt-1 text-lg font-bold text-slate-800">
                        {{ method }}
                    </p>

                </div>


                <!-- STATUS -->
                <div
                    class="rounded-2xl bg-emerald-50 p-5"
                >

                    <CheckIcon
                        class="mx-auto h-6 w-6 text-emerald-500"
                    />

                    <p class="mt-3 text-xs text-slate-500">
                        Status
                    </p>

                    <p class="mt-1 text-lg font-bold text-emerald-600">
                        Hadir
                    </p>

                </div>

            </div>


            <!-- LOCATION -->
            <div
                class="mt-6 flex items-center justify-center gap-2 text-sm text-slate-500"
            >
                <MapPinIcon
                    class="h-5 w-5 text-emerald-500"
                />

                <span>
                    RSUD Cibabat, Jl. Dustira No. 1, Cimahi Tengah
                </span>
            </div>


            <!-- INFO -->
            <div
                class="mx-auto mt-6 max-w-xl rounded-xl bg-slate-50 p-4 text-sm text-slate-500"
            >
                Absensi telah tercatat dan dapat dilihat pada menu
                <strong class="text-slate-700">
                    Riwayat Absensi
                </strong>.
            </div>


            <!-- FINISH -->
            <button
                type="button"
                @click="emit('finish')"
                class="mt-7 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 px-8 py-3 font-semibold text-white shadow-sm transition hover:shadow-lg"
            >
                Selesai
            </button>

        </div>

    </div>
</template>
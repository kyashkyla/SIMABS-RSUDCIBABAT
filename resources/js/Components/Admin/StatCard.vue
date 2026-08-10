<script setup>
import {
    UserGroupIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    title: String,
    value: [String, Number],
    subtitle: String,
    color: {
        type: String,
        default: 'emerald',
    },
    icon: {
        type: String,
        default: 'users',
    }
})

const icons = {
    users: UserGroupIcon,
    hadir: CheckCircleIcon,
    terlambat: ExclamationTriangleIcon,
    tidakhadir: XCircleIcon,
}

const colors = {
    emerald: {
        bg: 'bg-gradient-to-r from-emerald-500 to-emerald-600',
        text: 'text-white',
        icon: 'bg-white/20'
    },

    green: {
        bg: 'bg-white',
        text: 'text-green-600',
        icon: 'bg-green-100'
    },

    yellow: {
        bg: 'bg-white',
        text: 'text-yellow-500',
        icon: 'bg-yellow-100'
    },

    red: {
        bg: 'bg-white',
        text: 'text-red-500',
        icon: 'bg-red-100'
    }
}
</script>

<template>

<div
    class="rounded-2xl shadow-lg p-6 transition duration-300 hover:shadow-xl hover:-translate-y-1"
    :class="colors[color].bg">

    <div class="flex justify-between items-center">

        <div>

            <p
                class="text-sm"
                :class="color=='emerald' ? 'text-emerald-100' : 'text-gray-500'">

                {{ title }}

            </p>

            <h2
                class="text-4xl font-bold mt-3"
                :class="colors[color].text">

                {{ value }}

            </h2>

            <p
                class="mt-4 text-sm"
                :class="color=='emerald'
                    ? 'text-emerald-100'
                    : 'text-gray-400'">

                {{ subtitle }}

            </p>

        </div>

        <div
            class="w-16 h-16 rounded-2xl flex items-center justify-center"
            :class="colors[color].icon">

            <component
                :is="icons[icon]"
                class="w-8 h-8"
                :class="colors[color].text"/>

        </div>

    </div>

</div>

</template>
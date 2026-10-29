<script setup>
import { computed } from 'vue'

import {
    CheckCircleIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    // completed | checked_in | closed
    state: {
        type: String,
        default: 'closed',
    },
    message: {
        type: String,
        default: '',
    },
    // { start: 'HH:mm', end: 'HH:mm' } | null
    window: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['back'])

// completed / checked_in = pegawai sudah melakukan absensi
const alreadyDone = computed(() =>
    ['completed', 'checked_in'].includes(props.state)
)

const title = computed(() =>
    alreadyDone.value
        ? 'Anda Telah Melakukan Absensi'
        : 'Bukan Jam Absensi'
)

const description = computed(() => {
    if (props.message) return props.message

    return alreadyDone.value
        ? 'Anda telah melakukan absensi. Tidak perlu absen kembali.'
        : 'Anda tidak dapat melakukan absensi karena sudah bukan jamnya.'
})
</script>

<template>

    <div class="main-card">

        <div class="icon" :class="alreadyDone ? 'done' : 'closed'">
            <CheckCircleIcon v-if="alreadyDone" />
            <ClockIcon v-else />
        </div>

        <h2>
            {{ title }}
        </h2>

        <p>
            {{ description }}
        </p>

        <div v-if="alreadyDone" class="note done">
            Berlaku untuk absensi menggunakan Face Recognition maupun OTP.
            Anda tidak perlu melakukan absensi lagi.
        </div>

        <div v-else-if="props.window" class="note closed">
            Jam absensi shift Anda:
            <strong>{{ props.window.start }} - {{ props.window.end }}</strong>
        </div>

        <button
            type="button"
            class="primary-button"
            @click="emit('back')"
        >
            Kembali ke Dashboard
        </button>

    </div>

</template>

<style scoped>

.main-card {
    max-width: 760px;
    margin: auto;
    padding: 35px;

    background: white;

    border: 1px solid #e2eeee;
    border-radius: 16px;

    text-align: center;

    box-shadow: 0 4px 16px rgba(30,100,90,.04);
}

.icon {
    width: 65px;
    height: 65px;

    margin: 0 auto 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;
}

.icon svg {
    width: 30px;
}

.icon.done {
    background: #e9f8f5;
}

.icon.done svg {
    color: #20a995;
}

.icon.closed {
    background: #fff5d8;
}

.icon.closed svg {
    color: #d49a00;
}

.main-card h2 {
    margin: 0;

    color: #354347;

    font-size: 19px;
}

.main-card > p {
    max-width: 500px;

    margin: 8px auto 18px;

    color: #899598;

    font-size: 11px;

    line-height: 1.6;
}

.note {
    max-width: 500px;

    margin: 0 auto;
    padding: 12px;

    border-radius: 9px;

    font-size: 10px;
    line-height: 1.5;
}

.note.done {
    background: #effaf5;
    color: #3d7a5c;
}

.note.closed {
    background: #fff9e8;
    color: #8a6a10;
}

.primary-button {
    width: 100%;
    max-width: 500px;
    height: 43px;

    margin-top: 20px;

    border: none;
    border-radius: 9px;

    background: linear-gradient(
        90deg,
        #18b7a4,
        #20c66b
    );

    color: white;

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;
}

</style>
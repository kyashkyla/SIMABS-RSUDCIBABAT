<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { CheckCircle, XCircle, AlertTriangle, Info, X } from 'lucide-vue-next'
import { toasts, dismissToast } from '@/lib/toast'

const icons = {
    success: CheckCircle,
    error: XCircle,
    warning: AlertTriangle,
    info: Info,
}

const titles = {
    success: 'Berhasil',
    error: 'Gagal',
    warning: 'Perhatian',
    info: 'Informasi',
}

/*
| Posisi toast mengikuti <header> aplikasi (header pegawai / topbar admin):
| muncul tepat di bawah header, selebar area konten, dan meluncur keluar
| dari bawah header (bukan dari ujung atas browser).
| Kalau halaman tidak punya header (mis. login), jatuh ke atas layar.
*/
const box = ref(null) // { top, left, width } atau null
let observer = null
let observedHeader = null

const findHeader = () => {
    for (const el of document.querySelectorAll('header')) {
        const r = el.getBoundingClientRect()
        if (r.height > 0 && r.top <= 1) return el
    }
    return null
}

const measure = () => {
    const header = findHeader()

    if (!header) {
        box.value = null
        return
    }

    const r = header.getBoundingClientRect()
    box.value = { top: r.bottom, left: r.left, width: r.width }

    // Hitung ulang otomatis kalau header berubah lebar (sidebar dibuka/ditutup).
    if (observer && observedHeader !== header) {
        observer.disconnect()
        observer.observe(header)
        observedHeader = header
    }
}

const regionStyle = computed(() =>
    box.value
        ? {
              top: `${box.value.top}px`,
              left: `${box.value.left}px`,
              width: `${box.value.width}px`,
          }
        : {
              top: 'env(safe-area-inset-top, 0px)',
              left: '0',
              width: '100%',
          }
)

onMounted(() => {
    if (typeof ResizeObserver !== 'undefined') {
        observer = new ResizeObserver(measure)
    }
    window.addEventListener('resize', measure)
    measure()
})

onUnmounted(() => {
    window.removeEventListener('resize', measure)
    observer?.disconnect()
})

// Ukur ulang setiap ada toast baru (layout bisa berganti setelah navigasi).
watch(() => toasts.length, measure)
</script>

<template>
    <div
        class="toast-region"
        :class="{ 'toast-region--below-header': box }"
        :style="regionStyle"
        aria-live="polite"
        aria-atomic="false"
    >
        <TransitionGroup name="toast" tag="div" class="toast-stack">
            <div
                v-for="t in toasts"
                :key="t.id"
                class="toast"
                :class="`toast--${t.type}`"
                role="status"
                @click="dismissToast(t.id)"
            >
                <div class="toast__icon">
                    <component :is="icons[t.type]" :size="20" />
                </div>

                <div class="toast__body">
                    <strong>{{ t.title || titles[t.type] }}</strong>
                    <p>{{ t.message }}</p>
                </div>

                <button
                    type="button"
                    class="toast__close"
                    aria-label="Tutup notifikasi"
                    @click.stop="dismissToast(t.id)"
                >
                    <X :size="16" />
                </button>

                <span
                    v-if="t.duration > 0"
                    class="toast__progress"
                    :style="{ animationDuration: t.duration + 'ms' }"
                ></span>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
/*
| ====== WARNA (ubah di sini / override lewat CSS variable) ======
| Default memakai gradasi hijau-teal yang sama dengan sidebar aplikasi.
| Contoh override global di resources/css/app.css:
|   :root { --toast-success-from: #059669; --toast-success-to: #047857; }
*/
.toast {
    --from: var(--toast-success-from, #13b8a6);
    --to: var(--toast-success-to, #18c768);
}

.toast--error {
    --from: var(--toast-error-from, #f0545b);
    --to: var(--toast-error-to, #d93a4a);
}

.toast--warning {
    --from: var(--toast-warning-from, #f5b400);
    --to: var(--toast-warning-to, #ee9100);
}

.toast--info {
    --from: var(--toast-info-from, #13a3b8);
    --to: var(--toast-info-to, #2f86d6);
}

/*
| z-index 20: di BAWAH header (z-30) supaya toast terlihat keluar dari
| bawah header, tapi di atas konten halaman.
*/
.toast-region {
    position: fixed;
    z-index: 20;

    padding: 0 12px;

    /* Memotong toast yang masih "di balik" header saat animasi */
    overflow: hidden;

    pointer-events: none;
}

.toast-stack {
    width: 100%;
    max-width: 520px;
    margin: 0 auto;
}

.toast {
    position: relative;
    overflow: hidden;

    display: flex;
    align-items: flex-start;
    gap: 12px;

    margin: 12px 0 16px;
    padding: 13px 10px 16px 14px;

    border-radius: 16px;

    background: linear-gradient(135deg, var(--from), var(--to));
    color: #fff;

    box-shadow: 0 8px 18px rgba(15, 60, 50, .22);

    pointer-events: auto;
    cursor: pointer;
    touch-action: manipulation;
}

.toast__icon {
    flex-shrink: 0;

    width: 36px;
    height: 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 11px;

    background: rgba(255, 255, 255, .22);
}

.toast__body {
    flex: 1;
    min-width: 0;
    padding-top: 1px;
}

.toast__body strong {
    display: block;

    font-size: 14px;
    font-weight: 700;
    line-height: 1.3;
}

.toast__body p {
    margin: 2px 0 0;

    font-size: 13px;
    line-height: 1.45;

    color: rgba(255, 255, 255, .92);
    overflow-wrap: anywhere;
}

.toast__close {
    flex-shrink: 0;

    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: none;
    border-radius: 8px;

    background: transparent;
    color: rgba(255, 255, 255, .85);

    cursor: pointer;
}

.toast__close:hover {
    background: rgba(255, 255, 255, .2);
}

/* Garis hitung mundur 7 detik */
.toast__progress {
    position: absolute;
    left: 0;
    bottom: 0;

    height: 4px;
    width: 100%;

    background: rgba(255, 255, 255, .55);

    transform-origin: left;
    animation-name: toast-countdown;
    animation-timing-function: linear;
    animation-fill-mode: forwards;
}

@keyframes toast-countdown {
    from { transform: scaleX(1); }
    to   { transform: scaleX(0); }
}

/* Meluncur turun dari bawah header, lalu naik masuk lagi ke bawah header */
.toast-enter-active {
    transition: transform .5s cubic-bezier(.2, .9, .3, 1.1), opacity .25s ease;
}

.toast-leave-active {
    transition: transform .3s ease, opacity .3s ease;
}

.toast-enter-from,
.toast-leave-to {
    opacity: 0;
    transform: translateY(-130%);
}

.toast-move {
    transition: transform .3s ease;
}

@media (prefers-reduced-motion: reduce) {
    .toast-enter-active,
    .toast-leave-active,
    .toast-move {
        transition: opacity .2s ease;
    }

    .toast-enter-from,
    .toast-leave-to {
        transform: none;
    }
}
</style>
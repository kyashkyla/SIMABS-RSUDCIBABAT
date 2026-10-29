<script setup>
import { computed, watch, onUnmounted, nextTick, ref } from 'vue'
import { CheckCircle, Trash2, AlertTriangle } from 'lucide-vue-next'
import { confirmState, resolveConfirm } from '@/lib/confirm'

const confirmButton = ref(null)

const icon = computed(() => {
    if (confirmState.variant === 'danger') return Trash2
    if (confirmState.variant === 'warning') return AlertTriangle
    return CheckCircle
})

const onKeydown = (event) => {
    if (event.key === 'Escape') resolveConfirm(false)
}

watch(
    () => confirmState.open,
    async (open) => {
        if (open) {
            document.addEventListener('keydown', onKeydown)
            await nextTick()
            confirmButton.value?.focus()
        } else {
            document.removeEventListener('keydown', onKeydown)
        }
    }
)

onUnmounted(() => document.removeEventListener('keydown', onKeydown))
</script>

<template>
    <Transition name="confirm">
        <div
            v-if="confirmState.open"
            class="confirm-backdrop"
            @click.self="resolveConfirm(false)"
        >
            <div
                class="confirm-card"
                :class="`confirm-card--${confirmState.variant}`"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="confirm-title"
            >
                <div class="confirm-icon">
                    <component :is="icon" :size="28" />
                </div>

                <h3 id="confirm-title">{{ confirmState.title }}</h3>

                <p v-if="confirmState.message">{{ confirmState.message }}</p>

                <div class="confirm-actions">
                    <button
                        type="button"
                        class="btn btn--ghost"
                        @click="resolveConfirm(false)"
                    >
                        {{ confirmState.cancelText }}
                    </button>

                    <button
                        ref="confirmButton"
                        type="button"
                        class="btn btn--primary"
                        @click="resolveConfirm(true)"
                    >
                        {{ confirmState.confirmText }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.confirm-backdrop {
    position: fixed;
    inset: 0;
    z-index: 10000;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 16px;

    background: rgba(15, 30, 32, .55);
    backdrop-filter: blur(2px);
}

.confirm-card {
    --accent: #13b8a6;
    --accent-soft: #e0f7f4;

    width: 100%;
    max-width: 400px;

    padding: 28px 24px 22px;

    background: #fff;
    border-radius: 20px;

    text-align: center;

    box-shadow: 0 24px 60px rgba(10, 40, 40, .3);
}

.confirm-card--danger  { --accent: #e5484d; --accent-soft: #fdecec; }
.confirm-card--warning { --accent: #e0a100; --accent-soft: #fff6dc; }

.confirm-icon {
    width: 60px;
    height: 60px;

    margin: 0 auto 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    background: var(--accent-soft);
    color: var(--accent);
}

.confirm-card h3 {
    margin: 0;

    color: #2d3b3f;

    font-size: 18px;
    font-weight: 700;
}

.confirm-card p {
    margin: 8px 0 0;

    color: #6b7b7f;

    font-size: 14px;
    line-height: 1.55;
}

.confirm-actions {
    margin-top: 22px;

    display: flex;
    gap: 10px;
}

.btn {
    flex: 1;

    padding: 11px 14px;

    border: none;
    border-radius: 12px;

    font-size: 14px;
    font-weight: 600;

    cursor: pointer;

    transition: filter .15s ease, background .15s ease;
}

.btn--ghost {
    background: #eef3f3;
    color: #4d5e62;
}

.btn--ghost:hover {
    background: #e3eaea;
}

.btn--primary {
    background: var(--accent);
    color: #fff;
}

.btn--primary:hover {
    filter: brightness(.93);
}

/* Tombol utama (variant success) memakai gradasi hijau-teal aplikasi */
.confirm-card:not(.confirm-card--danger):not(.confirm-card--warning) .btn--primary {
    background: linear-gradient(135deg, #13b8a6, #18c768);
}

.btn:focus-visible {
    outline: 3px solid var(--accent-soft);
    outline-offset: 1px;
}

/* Animasi: backdrop fade, kartu membesar dari tengah */
.confirm-enter-active,
.confirm-leave-active {
    transition: opacity .2s ease;
}

.confirm-enter-active .confirm-card,
.confirm-leave-active .confirm-card {
    transition: transform .25s cubic-bezier(.2, .9, .3, 1.2), opacity .2s ease;
}

.confirm-enter-from,
.confirm-leave-to {
    opacity: 0;
}

.confirm-enter-from .confirm-card,
.confirm-leave-to .confirm-card {
    opacity: 0;
    transform: scale(.9) translateY(10px);
}
</style>
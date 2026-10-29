import { reactive } from 'vue'

/*
| Pemakaian di komponen mana pun:
|
|   import { toast } from '@/lib/toast'
|
|   toast.success('Password berhasil diubah.')
|   toast.error('Kode OTP tidak valid.')
|   toast.warning('Isi alasan penolakan dulu.')
|   toast.info('Kode OTP dikirim ke notifikasi Anda.')
|   toast.error('Pesan', { title: 'Judul Custom', duration: 5000 })
|
| Muncul dari bawah header aplikasi, hilang otomatis setelah 7 detik.
*/

export const TOAST_DURATION = 7000
const MAX_VISIBLE = 4

export const toasts = reactive([])

let nextId = 1

export function dismissToast(id) {
    const index = toasts.findIndex((t) => t.id === id)
    if (index !== -1) toasts.splice(index, 1)
}

function push(type, message, options = {}) {
    if (!message) return null

    const id = nextId++
    const duration = options.duration ?? TOAST_DURATION

    // Pesan yang sama persis & masih tampil -> jangan ditumpuk.
    const duplicate = toasts.find((t) => t.type === type && t.message === message)
    if (duplicate) dismissToast(duplicate.id)

    toasts.push({ id, type, message, title: options.title ?? null, duration })

    while (toasts.length > MAX_VISIBLE) toasts.shift()

    if (duration > 0) setTimeout(() => dismissToast(id), duration)

    return id
}

export const toast = {
    success: (message, options) => push('success', message, options),
    error: (message, options) => push('error', message, options),
    warning: (message, options) => push('warning', message, options),
    info: (message, options) => push('info', message, options),
}
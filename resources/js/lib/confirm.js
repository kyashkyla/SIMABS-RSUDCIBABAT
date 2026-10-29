import { reactive } from 'vue'

/*
| Dialog konfirmasi (pengganti window.confirm). Muncul di tengah layar.
| Mengembalikan Promise<boolean>.
|
|   import { confirmDialog } from '@/lib/confirm'
|
|   const ok = await confirmDialog({
|       title: 'Setujui Pengajuan?',
|       message: 'Pengajuan akan disetujui.',
|       confirmText: 'Ya, Setujui',
|       cancelText: 'Batal',
|       variant: 'success',      // success | danger | warning
|   })
|   if (!ok) return
*/

export const confirmState = reactive({
    open: false,
    title: '',
    message: '',
    confirmText: 'Ya',
    cancelText: 'Batal',
    variant: 'success',
})

let resolver = null

export function confirmDialog(options = {}) {
    // Kalau ada dialog yang masih terbuka, anggap dibatalkan.
    if (resolver) resolver(false)

    Object.assign(confirmState, {
        title: options.title ?? 'Konfirmasi',
        message: options.message ?? '',
        confirmText: options.confirmText ?? 'Ya',
        cancelText: options.cancelText ?? 'Batal',
        variant: options.variant ?? 'success',
        open: true,
    })

    return new Promise((resolve) => {
        resolver = resolve
    })
}

export function resolveConfirm(result) {
    confirmState.open = false

    if (resolver) {
        resolver(result)
        resolver = null
    }
}
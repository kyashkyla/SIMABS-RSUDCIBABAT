<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    employee: Object
})

const password = ref('')
const password_confirmation = ref('')
const processing = ref(false)
const errors = ref({})

const submit = () => {
    processing.value = true
    errors.value = {}

    router.put(
        `/admin/pegawai/${props.employee.id}/reset-password`,
        {
            password: password.value,
            password_confirmation: password_confirmation.value
        },
        {
            onError: (error) => {
                errors.value = error
            },
            onFinish: () => {
                processing.value = false
            }
        }
    )
}
</script>

<template>
    <Head title="Reset Password Pegawai" />

    <div class="min-h-screen bg-slate-100">

        <!-- HEADER -->
        <div class="bg-white border-b">
            <div class="max-w-5xl mx-auto px-6 py-5">

                <div class="flex items-center gap-4">

                    <Link
                        :href="`/admin/pegawai/${employee.id}`"
                        class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-emerald-100 hover:text-emerald-600 transition"
                    >
                        ←
                    </Link>

                    <div>
                        <h1 class="text-2xl font-bold text-slate-800">
                            Reset Password
                        </h1>

                        <p class="text-sm text-slate-500">
                            Ubah password akun pegawai
                        </p>
                    </div>

                </div>

            </div>
        </div>


        <!-- CONTENT -->
        <div class="max-w-5xl mx-auto px-6 py-10">

            <div class="grid md:grid-cols-3 gap-6">

                <!-- INFORMASI PEGAWAI -->

                <div class="bg-white rounded-2xl shadow-sm p-6">

                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center text-2xl mb-5">
                        👤
                    </div>

                    <h2 class="text-lg font-bold text-slate-800">
                        {{ employee.name }}
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        {{ employee.position }}
                    </p>

                    <div class="border-t mt-6 pt-5 space-y-4">

                        <div>
                            <p class="text-xs text-slate-400">
                                NIP
                            </p>

                            <p class="font-medium text-slate-700">
                                {{ employee.nip }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">
                                Departemen
                            </p>

                            <p class="font-medium text-slate-700">
                                {{ employee.department }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-400">
                                Username
                            </p>

                            <p class="font-medium text-emerald-600">
                                {{ employee.user?.username ?? '-' }}
                            </p>
                        </div>

                    </div>

                </div>


                <!-- FORM PASSWORD -->

                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm p-8">

                    <div class="mb-8">

                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-xl mb-4">
                            🔐
                        </div>

                        <h2 class="text-2xl font-bold text-slate-800">
                            Ubah Password Akun
                        </h2>

                        <p class="text-slate-500 mt-1">
                            Admin dapat mengatur ulang password pegawai.
                        </p>

                    </div>


                    <!-- WARNING -->

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">

                        <div class="flex gap-3">

                            <span class="text-xl">
                                ⚠️
                            </span>

                            <div>

                                <p class="font-semibold text-yellow-800">
                                    Perhatian
                                </p>

                                <p class="text-sm text-yellow-700 mt-1">
                                    Setelah password diubah, gunakan password baru
                                    tersebut untuk login sebagai pegawai.
                                </p>

                            </div>

                        </div>

                    </div>


                    <form
                        @submit.prevent="submit"
                        class="space-y-6"
                    >

                        <!-- PASSWORD BARU -->

                        <div>

                            <label class="block font-medium text-slate-700 mb-2">
                                Password Baru
                            </label>

                            <input
                                v-model="password"
                                type="password"
                                placeholder="Masukkan password baru"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            />

                            <p
                                v-if="errors.password"
                                class="text-sm text-red-500 mt-2"
                            >
                                {{ errors.password }}
                            </p>

                            <p class="text-xs text-slate-400 mt-2">
                                Minimal 8 karakter.
                            </p>

                        </div>


                        <!-- KONFIRMASI PASSWORD -->

                        <div>

                            <label class="block font-medium text-slate-700 mb-2">
                                Konfirmasi Password
                            </label>

                            <input
                                v-model="password_confirmation"
                                type="password"
                                placeholder="Masukkan kembali password"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                            />

                            <p
                                v-if="errors.password_confirmation"
                                class="text-sm text-red-500 mt-2"
                            >
                                {{ errors.password_confirmation }}
                            </p>

                        </div>


                        <!-- BUTTON -->

                        <div class="flex justify-end gap-3 pt-4">

                            <Link
                                :href="`/admin/pegawai/${employee.id}`"
                                class="px-6 py-3 rounded-xl border border-slate-300 text-slate-600 font-medium hover:bg-slate-50 transition"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                :disabled="processing"
                                class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 text-white font-semibold hover:scale-[1.02] transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ processing ? 'Menyimpan...' : 'Simpan Password' }}
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
</template>
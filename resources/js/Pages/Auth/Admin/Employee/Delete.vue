<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    employee: {
        type: Object,
        required: true,
    },
})

const form = useForm({})

const deleteEmployee = () => {
    form.delete(route('admin.employees.destroy', props.employee.id))
}
</script>

<template>
    <Head title="Hapus Pegawai" />

    <AdminLayout>

        <!-- HEADER -->

        <div class="flex items-center gap-4 mb-8">

            <Link
                :href="`/admin/pegawai`"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200
                       flex items-center justify-center
                       text-slate-600 hover:bg-slate-50 transition">

                ←

            </Link>

            <div>

                <h1 class="text-3xl font-bold text-slate-800">
                    Hapus Pegawai
                </h1>

                <p class="text-slate-500 mt-1">
                    Konfirmasi penghapusan data pegawai.
                </p>

            </div>

        </div>


        <!-- CONFIRMATION CARD -->

        <div class="max-w-3xl mx-auto">

            <div
                class="bg-white rounded-2xl shadow-sm
                       border border-red-100 p-8">

                <!-- ICON -->

                <div class="flex justify-center mb-6">

                    <div
                        class="w-20 h-20 rounded-full
                               bg-red-100 text-red-600
                               flex items-center justify-center
                               text-4xl">

                        ⚠️

                    </div>

                </div>


                <!-- TITLE -->

                <div class="text-center">

                    <h2 class="text-2xl font-bold text-slate-800">

                        Hapus Data Pegawai?

                    </h2>

                    <p class="text-slate-500 mt-2">

                        Apakah kamu yakin ingin menghapus data pegawai berikut?

                    </p>

                </div>


                <!-- EMPLOYEE -->

                <div
                    class="mt-8 bg-slate-50 rounded-2xl p-6">

                    <div class="flex items-center gap-4">

                        <div
                            class="w-16 h-16 rounded-xl
                                   bg-emerald-100 text-emerald-700
                                   flex items-center justify-center
                                   text-2xl font-bold">

                            {{ employee.name.charAt(0) }}

                        </div>

                        <div>

                            <h3 class="text-lg font-bold text-slate-800">

                                {{ employee.name }}

                            </h3>

                            <p class="text-sm text-slate-500">

                                NIP: {{ employee.nip }}

                            </p>

                            <p class="text-sm text-slate-500">

                                {{ employee.position }} ·
                                {{ employee.department }}

                            </p>

                        </div>

                    </div>

                </div>


                <!-- WARNING -->

                <div
                    class="mt-6 bg-red-50 border border-red-100
                           rounded-xl p-4">

                    <p class="text-sm text-red-700">

                        <strong>Perhatian:</strong>

                        Data pegawai yang telah dihapus tidak dapat
                        dikembalikan. Pastikan kamu benar-benar ingin
                        menghapus data pegawai ini.

                    </p>

                </div>


                <!-- BUTTON -->

                <div
                    class="flex flex-col-reverse sm:flex-row
                           justify-center gap-3 mt-8">

                    <Link
                        :href="`/admin/pegawai`"
                        class="px-6 py-3 rounded-xl
                               bg-slate-100 hover:bg-slate-200
                               text-slate-700 font-semibold
                               text-center transition">

                        Batal

                    </Link>

                    <button
                        type="button"
                        @click="deleteEmployee"
                        :disabled="form.processing"
                        class="px-6 py-3 rounded-xl
                               bg-red-500 hover:bg-red-600
                               disabled:opacity-50
                               disabled:cursor-not-allowed
                               text-white font-semibold
                               transition">

                        {{ form.processing
                            ? 'Menghapus...'
                            : 'Ya, Hapus Pegawai'
                        }}

                    </button>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>
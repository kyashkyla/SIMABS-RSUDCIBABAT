<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'

const props = defineProps({
    jamKerja: { type: Object, required: true },
    notifikasi: { type: Object, required: true },
})

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success)

// ================= JAM KERJA =================
const jamKerjaForm = useForm({
    work_start_pagi: props.jamKerja.workStartPagi,
    work_end_pagi: props.jamKerja.workEndPagi,
    
    work_start_siang: props.jamKerja.workStartSiang,
    work_end_siang: props.jamKerja.workEndSiang,
    
    work_start_malam: props.jamKerja.workStartMalam,
    work_end_malam: props.jamKerja.workEndMalam,
    
    late_tolerance: props.jamKerja.lateTolerance,
})

const saveJamKerja = () => {
    jamKerjaForm.put(route('admin.settings.jam-kerja'), { preserveScroll: true })
}

// ================= NOTIFIKASI =================
const notifikasiForm = useForm({
    notify_admin_new_request: props.notifikasi.notifyAdminNewRequest,
    notify_employee_decision: props.notifikasi.notifyEmployeeDecision,
})

const saveNotifikasi = () => {
    notifikasiForm.put(route('admin.settings.notifikasi'), { preserveScroll: true })
}

// ================= PASSWORD ADMIN =================
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const savePassword = () => {
    passwordForm.put(route('admin.settings.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    })
}
</script>

<template>
    <Head title="Pengaturan" />

    <AdminLayout>

        <div class="max-w-4xl mx-auto space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Pengaturan Umum
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Kelola jam kerja, notifikasi, dan keamanan akun admin.
                    </p>
                </div>

                <a
                    :href="route('admin.settings.index')"
                    class="text-sm text-emerald-600 hover:text-emerald-800 font-medium whitespace-nowrap">
                    Atur Lokasi & Radius Absensi &rarr;
                </a>
            </div>

            <!-- FLASH SUCCESS -->
            <div
                v-if="flashSuccess"
                class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-5 py-3 text-sm">
                {{ flashSuccess }}
            </div>

            <!-- ===================== JAM KERJA ===================== -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <h2 class="text-lg font-semibold text-slate-800 mb-1">
                    Jam Kerja & Toleransi Keterlambatan
                </h2>

                <p class="text-sm text-slate-500 mb-5">
                    Menentukan status "Terlambat" saat pencatatan kehadiran pegawai.
                </p>

                <form @submit.prevent="saveJamKerja" class="space-y-4">

                    <!-- SHIFT PAGI -->
                    <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 mb-4">
                        <h3 class="font-medium text-slate-700 mb-3 text-sm">Shift Pagi</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Jam Masuk</label>
                                <input
                                    v-model="jamKerjaForm.work_start_pagi"
                                    type="time"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                />
                                <p v-if="jamKerjaForm.errors.work_start_pagi" class="mt-1 text-sm text-red-600">
                                    {{ jamKerjaForm.errors.work_start_pagi }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Jam Pulang</label>
                                <input
                                    v-model="jamKerjaForm.work_end_pagi"
                                    type="time"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                />
                                <p v-if="jamKerjaForm.errors.work_end_pagi" class="mt-1 text-sm text-red-600">
                                    {{ jamKerjaForm.errors.work_end_pagi }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SHIFT SIANG -->
                    <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 mb-4">
                        <h3 class="font-medium text-slate-700 mb-3 text-sm">Shift Siang</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Jam Masuk</label>
                                <input
                                    v-model="jamKerjaForm.work_start_siang"
                                    type="time"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                />
                                <p v-if="jamKerjaForm.errors.work_start_siang" class="mt-1 text-sm text-red-600">
                                    {{ jamKerjaForm.errors.work_start_siang }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Jam Pulang</label>
                                <input
                                    v-model="jamKerjaForm.work_end_siang"
                                    type="time"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                />
                                <p v-if="jamKerjaForm.errors.work_end_siang" class="mt-1 text-sm text-red-600">
                                    {{ jamKerjaForm.errors.work_end_siang }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SHIFT MALAM -->
                    <div class="p-4 border border-slate-100 rounded-xl bg-slate-50/50 mb-4">
                        <h3 class="font-medium text-slate-700 mb-3 text-sm">Shift Malam</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium text-slate-600">Jam Masuk</label>
                                <input
                                    v-model="jamKerjaForm.work_start_malam"
                                    type="time"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                />
                                <p v-if="jamKerjaForm.errors.work_start_malam" class="mt-1 text-sm text-red-600">
                                    {{ jamKerjaForm.errors.work_start_malam }}
                                </p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-slate-600">Jam Pulang</label>
                                <input
                                    v-model="jamKerjaForm.work_end_malam"
                                    type="time"
                                    class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                                />
                                <p v-if="jamKerjaForm.errors.work_end_malam" class="mt-1 text-sm text-red-600">
                                    {{ jamKerjaForm.errors.work_end_malam }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="text-sm font-medium text-slate-600">Toleransi (menit)</label>
                            <input
                                v-model="jamKerjaForm.late_tolerance"
                                type="number"
                                min="0"
                                max="120"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            />
                            <p v-if="jamKerjaForm.errors.late_tolerance" class="mt-1 text-sm text-red-600">
                                {{ jamKerjaForm.errors.late_tolerance }}
                            </p>
                        </div>

                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="jamKerjaForm.processing"
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition disabled:opacity-50">
                            {{ jamKerjaForm.processing ? 'Menyimpan...' : 'Simpan Jam Kerja' }}
                        </button>
                    </div>

                </form>

            </div>

            <!-- ===================== NOTIFIKASI ===================== -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <h2 class="text-lg font-semibold text-slate-800 mb-1">
                    Notifikasi Sistem
                </h2>

                <p class="text-sm text-slate-500 mb-5">
                    Atur notifikasi terkait pengajuan absensi alternatif.
                </p>

                <form @submit.prevent="saveNotifikasi" class="space-y-4">

                    <label class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3.5 cursor-pointer">
                        <span class="text-sm text-slate-700">
                            Beri tahu admin saat ada pengajuan absensi alternatif baru
                        </span>
                        <input
                            v-model="notifikasiForm.notify_admin_new_request"
                            type="checkbox"
                            class="w-5 h-5 accent-emerald-600"
                        />
                    </label>

                    <label class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3.5 cursor-pointer">
                        <span class="text-sm text-slate-700">
                            Beri tahu pegawai saat pengajuannya disetujui/ditolak
                        </span>
                        <input
                            v-model="notifikasiForm.notify_employee_decision"
                            type="checkbox"
                            class="w-5 h-5 accent-emerald-600"
                        />
                    </label>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="notifikasiForm.processing"
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition disabled:opacity-50">
                            {{ notifikasiForm.processing ? 'Menyimpan...' : 'Simpan Notifikasi' }}
                        </button>
                    </div>

                </form>

            </div>

            <!-- ===================== PASSWORD ADMIN ===================== -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

                <h2 class="text-lg font-semibold text-slate-800 mb-1">
                    Ubah Password Admin
                </h2>

                <p class="text-sm text-slate-500 mb-5">

                </p>

                <form @submit.prevent="savePassword" class="space-y-4">

                    <div>
                        <label class="text-sm font-medium text-slate-600">Password Saat Ini</label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                        />
                        <p v-if="passwordForm.errors.current_password" class="mt-1 text-sm text-red-600">
                            {{ passwordForm.errors.current_password }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="text-sm font-medium text-slate-600">Password Baru</label>
                            <input
                                v-model="passwordForm.password"
                                type="password"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            />
                            <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                {{ passwordForm.errors.password }}
                            </p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-slate-600">Konfirmasi Password Baru</label>
                            <input
                                v-model="passwordForm.password_confirmation"
                                type="password"
                                class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            />
                        </div>

                    </div>

                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="passwordForm.processing"
                            class="px-6 py-2.5 bg-slate-800 text-white rounded-xl font-medium hover:bg-slate-900 transition disabled:opacity-50">
                            {{ passwordForm.processing ? 'Menyimpan...' : 'Ubah Password' }}
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </AdminLayout>
</template>
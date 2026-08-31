<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    approval: { type: Object, required: true },
})

const approveForm = useForm({
    approval_type: 'sementara',
    admin_note: '',
})

const rejectForm = useForm({
    admin_note: '',
})

const isPending = computed(() => props.approval.status === 'Menunggu')

const approve = () => {
    if (!confirm('Setujui pengajuan absensi alternatif ini?')) return

    approveForm.put(route('admin.approvals.approve', props.approval.id))
}

const reject = () => {
    rejectForm.admin_note = approveForm.admin_note

    if (!rejectForm.admin_note) {
        alert('Isi alasan penolakan pada kolom Catatan Admin terlebih dahulu.')
        return
    }

    rejectForm.put(route('admin.approvals.reject', props.approval.id))
}
</script>

<template>

    <Head title="Detail Persetujuan Absensi" />

    <AdminLayout>

        <div class="max-w-6xl mx-auto space-y-6">

            <!-- HEADER -->

            <div class="flex items-center gap-4">

                <Link
                    href="/admin/persetujuan-absensi"
                    class="w-10 h-10 rounded-xl bg-white border border-slate-200
                           flex items-center justify-center text-slate-500
                           hover:bg-slate-50 transition">

                    ←

                </Link>

                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Detail Persetujuan Absensi
                    </h1>

                    <p class="text-slate-500 mt-1">
                        Periksa pengajuan absensi alternatif pegawai.
                    </p>

                </div>

            </div>


            <!-- STATUS -->

            <div
                v-if="isPending"
                class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5
                       flex items-center justify-between">

                <div class="flex items-center gap-4">

                    <div
                        class="w-11 h-11 rounded-xl bg-yellow-100
                               flex items-center justify-center text-yellow-600 text-xl">

                        ⏳

                    </div>

                    <div>

                        <p class="font-semibold text-yellow-800">
                            Menunggu Persetujuan Admin
                        </p>

                        <p class="text-sm text-yellow-700 mt-1">
                            Pengajuan belum diberikan keputusan.
                        </p>

                    </div>

                </div>

                <span
                    class="px-4 py-2 rounded-full bg-yellow-100
                           text-yellow-700 text-sm font-semibold">

                    Menunggu

                </span>

            </div>

            <div
                v-else
                class="rounded-2xl p-5 flex items-center justify-between border"
                :class="approval.status === 'Disetujui'
                    ? 'bg-emerald-50 border-emerald-200'
                    : 'bg-red-50 border-red-200'">

                <div>
                    <p class="font-semibold" :class="approval.status === 'Disetujui' ? 'text-emerald-800' : 'text-red-800'">
                        Pengajuan {{ approval.status }}{{ approval.type ? ' • ' + approval.type : '' }}
                    </p>
                    <p class="text-sm mt-1" :class="approval.status === 'Disetujui' ? 'text-emerald-700' : 'text-red-700'">
                        {{ approval.admin_note || 'Tidak ada catatan tambahan.' }}
                    </p>
                </div>

            </div>


            <!-- DATA PEGAWAI -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Data Pegawai
                    </h2>

                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>

                        <p class="text-sm text-slate-500">
                            Nama Pegawai
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ approval.name }}
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            NIP
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ approval.nip }}
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Departemen
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ approval.department }}
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Jabatan
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ approval.position }}
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Shift
                        </p>

                        <p class="font-semibold text-slate-800 mt-1">
                            {{ approval.shift }}
                        </p>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500">
                            Metode Absensi Utama
                        </p>

                        <p class="font-semibold text-emerald-600 mt-1">
                            Face ID
                        </p>

                    </div>

                </div>

            </div>


            <!-- ALASAN PENGAJUAN -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Detail Pengajuan
                    </h2>

                </div>

                <div class="p-6 space-y-5">

                    <div>

                        <p class="text-sm text-slate-500 mb-2">
                            Alasan Pengajuan
                        </p>

                        <div
                            class="bg-slate-50 rounded-xl p-4 text-slate-700">

                            {{ approval.reason }}

                        </div>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500 mb-2">
                            Metode Absensi Alternatif
                        </p>

                        <span
                            class="inline-flex items-center px-4 py-2
                                   rounded-xl bg-blue-50 text-blue-700
                                   font-semibold">

                            🔐 OTP

                        </span>

                    </div>

                    <div>

                        <p class="text-sm text-slate-500 mb-2">
                            Tanggal Pengajuan
                        </p>

                        <p class="text-slate-700">
                            {{ approval.date }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- BUKTI -->

            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Bukti Pengajuan
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Foto yang dikirim oleh pegawai sebagai bukti pengajuan.
                    </p>

                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- SELFIE -->

                    <div>

                        <p class="font-semibold text-slate-700 mb-3">
                            Foto Selfie
                        </p>

                        <div
                            class="aspect-[4/3] rounded-2xl bg-slate-100
                                   border border-slate-200 overflow-hidden
                                   flex items-center justify-center">

                            <img
                                v-if="approval.selfie_photo"
                                :src="approval.selfie_photo"
                                alt="Foto selfie pegawai"
                                class="w-full h-full object-cover">

                            <div v-else class="text-center text-slate-400">

                                <div class="text-5xl mb-3">
                                    📸
                                </div>

                                <p class="text-sm">
                                    Belum ada foto selfie
                                </p>

                            </div>

                        </div>

                        <a
                            v-if="approval.selfie_photo"
                            :href="approval.selfie_photo"
                            target="_blank"
                            class="mt-3 block text-center w-full py-2.5 rounded-xl
                                   border border-slate-200
                                   text-slate-600 text-sm font-medium
                                   hover:bg-slate-50 transition">

                            Lihat Foto Selfie

                        </a>

                    </div>


                    <!-- ID CARD -->

                    <div>

                        <p class="font-semibold text-slate-700 mb-3">
                            Foto ID Card
                        </p>

                        <div
                            class="aspect-[4/3] rounded-2xl bg-slate-100
                                   border border-slate-200 overflow-hidden
                                   flex items-center justify-center">

                            <img
                                v-if="approval.id_card_photo"
                                :src="approval.id_card_photo"
                                alt="Foto ID Card pegawai"
                                class="w-full h-full object-cover">

                            <div v-else class="text-center text-slate-400">

                                <div class="text-5xl mb-3">
                                    🪪
                                </div>

                                <p class="text-sm">
                                    Belum ada foto ID Card
                                </p>

                            </div>

                        </div>

                        <a
                            v-if="approval.id_card_photo"
                            :href="approval.id_card_photo"
                            target="_blank"
                            class="mt-3 block text-center w-full py-2.5 rounded-xl
                                   border border-slate-200
                                   text-slate-600 text-sm font-medium
                                   hover:bg-slate-50 transition">

                            Lihat Foto ID Card

                        </a>

                    </div>

                </div>

            </div>


            <!-- KEPUTUSAN ADMIN -->

            <div v-if="isPending" class="bg-white rounded-2xl shadow-sm border border-slate-100">

                <div class="p-6 border-b border-slate-100">

                    <h2 class="text-lg font-semibold text-slate-800">
                        Keputusan Admin
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Tentukan jenis approval untuk pegawai ini.
                    </p>

                </div>

                <div class="p-6 space-y-6">

                    <!-- PILIHAN APPROVAL -->

                    <div>

                        <label class="text-sm font-semibold text-slate-700">
                            Jenis Approval
                        </label>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">

                            <!-- SEMENTARA -->

                            <label
                                class="relative cursor-pointer">

                                <input
                                    v-model="approveForm.approval_type"
                                    type="radio"
                                    value="sementara"
                                    class="peer sr-only">

                                <div
                                    class="border-2 border-slate-200
                                           rounded-2xl p-5
                                           peer-checked:border-emerald-500
                                           peer-checked:bg-emerald-50
                                           transition">

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="w-11 h-11 rounded-xl
                                                   bg-blue-100
                                                   flex items-center justify-center
                                                   text-blue-600 text-xl">

                                            🕒

                                        </div>

                                        <div>

                                            <h3 class="font-semibold text-slate-800">
                                                Approval Sementara
                                            </h3>

                                            <p class="text-sm text-slate-500 mt-1">
                                                Pegawai dapat menggunakan OTP
                                                selama periode yang ditentukan.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </label>


                            <!-- PERMANEN -->

                            <label
                                class="relative cursor-pointer">

                                <input
                                    v-model="approveForm.approval_type"
                                    type="radio"
                                    value="permanen"
                                    class="peer sr-only">

                                <div
                                    class="border-2 border-slate-200
                                           rounded-2xl p-5
                                           peer-checked:border-emerald-500
                                           peer-checked:bg-emerald-50
                                           transition">

                                    <div class="flex items-start gap-4">

                                        <div
                                            class="w-11 h-11 rounded-xl
                                                   bg-emerald-100
                                                   flex items-center justify-center
                                                   text-emerald-600 text-xl">

                                            ✓

                                        </div>

                                        <div>

                                            <h3 class="font-semibold text-slate-800">
                                                Approval Permanen
                                            </h3>

                                            <p class="text-sm text-slate-500 mt-1">
                                                Pegawai dapat menggunakan OTP
                                                tanpa batas waktu.
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </label>

                        </div>

                    </div>


                    <!-- TANGGAL -->

                    <div
                        v-if="approveForm.approval_type === 'sementara'"
                        class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>

                            <label class="text-sm font-semibold text-slate-700">
                                Tanggal Mulai
                            </label>

                            <input
                                v-model="approveForm.start_date"
                                type="date"
                                class="mt-2 w-full rounded-xl
                                       border border-slate-300 px-4 py-3
                                       focus:outline-none
                                       focus:ring-2 focus:ring-emerald-500">

                        </div>

                        <div>

                            <label class="text-sm font-semibold text-slate-700">
                                Tanggal Berakhir
                            </label>

                            <input
                                v-model="approveForm.end_date"
                                type="date"
                                class="mt-2 w-full rounded-xl
                                       border border-slate-300 px-4 py-3
                                       focus:outline-none
                                       focus:ring-2 focus:ring-emerald-500">

                        </div>

                    </div>


                    <!-- INFO PERMANEN -->

                    <div
                        v-if="approveForm.approval_type === 'permanen'"
                        class="bg-emerald-50 border border-emerald-200
                               rounded-xl p-4">

                        <div class="flex gap-3">

                            <span class="text-emerald-600 text-xl">
                                ✓
                            </span>

                            <div>

                                <p class="font-semibold text-emerald-800">
                                    Approval Permanen
                                </p>

                                <p class="text-sm text-emerald-700 mt-1">
                                    Pegawai akan dapat menggunakan OTP sebagai
                                    metode absensi alternatif tanpa batas waktu.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- CATATAN -->

                    <div>

                        <label class="text-sm font-semibold text-slate-700">
                            Catatan Admin
                        </label>

                        <textarea
                            v-model="approveForm.admin_note"
                            rows="4"
                            placeholder="Tambahkan catatan atau alasan keputusan..."
                            class="mt-2 w-full rounded-xl border border-slate-300
                                   px-4 py-3 resize-none
                                   focus:outline-none
                                   focus:ring-2 focus:ring-emerald-500">
                        </textarea>

                    </div>

                </div>

            </div>


            <!-- ACTION -->

            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-100
                       p-6 flex flex-col md:flex-row
                       md:items-center md:justify-between gap-4">

                <Link
                    href="/admin/persetujuan-absensi"
                    class="text-slate-500 hover:text-slate-700 font-medium">

                    ← Kembali ke Daftar

                </Link>

                <div v-if="isPending" class="flex gap-3">

                    <button
                        @click="reject"
                        :disabled="rejectForm.processing"
                        class="px-6 py-3 rounded-xl
                               border border-red-200
                               text-red-600 font-semibold
                               hover:bg-red-50 transition disabled:opacity-50">

                        Tolak Pengajuan

                    </button>

                    <button
                        @click="approve"
                        :disabled="approveForm.processing"
                        class="px-6 py-3 rounded-xl
                               bg-emerald-600 text-white
                               font-semibold
                               hover:bg-emerald-700 transition disabled:opacity-50">

                        Setujui Pengajuan

                    </button>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>
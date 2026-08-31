<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import LocationMapPicker from './LocationMapPicker.vue'
import {
    MapPinIcon,
    ChevronDownIcon,
    ChevronUpIcon,
    TrashIcon,
    PlusIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    // null => kartu ini dipakai untuk menambah lokasi baru
    location: {
        type: Object,
        default: null,
    },
})

const isNew = !props.location

// ==============================
// EXPAND / COLLAPSE
// ==============================
// Defaultnya SELALU tertutup. Admin harus klik teks "Radius Lokasi"
// dulu baru form edit koordinat & radiusnya muncul.
const expanded = ref(false)

const toggleExpanded = () => {
    expanded.value = !expanded.value
}

// ==============================
// FORM
// ==============================
const form = reactive({
    name: props.location?.name ?? '',
    latitude: props.location?.latitude ?? '',
    longitude: props.location?.longitude ?? '',
    radius_meters: props.location?.radius_meters ?? 60,
})

const errors = ref({})
const processing = ref(false)

const resetForm = () => {
    form.name = ''
    form.latitude = ''
    form.longitude = ''
    form.radius_meters = 60
}

const save = () => {
    processing.value = true
    errors.value = {}

    const options = {
        preserveScroll: true,
        onError: (e) => {
            errors.value = e
        },
        onSuccess: () => {
            if (isNew) {
                resetForm()
                expanded.value = false
            }
        },
        onFinish: () => {
            processing.value = false
        },
    }

    if (isNew) {
        router.post('/admin/pengaturan/lokasi', form, options)
    } else {
        router.put(`/admin/pengaturan/lokasi/${props.location.id}`, form, options)
    }
}

const toggleActive = () => {
    router.patch(
        `/admin/pengaturan/lokasi/${props.location.id}/toggle-aktif`,
        {},
        { preserveScroll: true }
    )
}

const destroyLocation = () => {
    if (
        !confirm(
            `Hapus lokasi "${props.location.name}"? Pegawai tidak akan bisa absen dari titik ini lagi.`
        )
    ) {
        return
    }

    router.delete(`/admin/pengaturan/lokasi/${props.location.id}`, {
        preserveScroll: true,
    })
}
</script>

<template>

    <div
        class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden"
        :class="isNew ? 'border-dashed border-2 border-emerald-200' : ''">


        <!-- ================= HEADER (nama lokasi + status) ================= -->

        <div
            v-if="!isNew"
            class="flex items-center justify-between gap-3 px-6 pt-5">

            <div class="flex items-center gap-3 min-w-0">

                <div
                    class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600
                           flex items-center justify-center flex-shrink-0">

                    <MapPinIcon class="w-5 h-5" />

                </div>

                <div class="min-w-0">

                    <h3 class="font-bold text-slate-800 truncate">
                        {{ location.name }}
                    </h3>

                    <p class="text-xs text-slate-400">
                        {{ location.latitude }}, {{ location.longitude }}
                    </p>

                </div>

            </div>

            <div class="flex items-center gap-2 flex-shrink-0">

                <button
                    type="button"
                    @click="toggleActive"
                    class="text-xs font-semibold px-3 py-1.5 rounded-full transition"
                    :class="location.is_active
                        ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'
                        : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">

                    {{ location.is_active ? 'Aktif' : 'Nonaktif' }}

                </button>

                <button
                    type="button"
                    @click="destroyLocation"
                    title="Hapus lokasi"
                    class="w-8 h-8 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500
                           flex items-center justify-center transition">

                    <TrashIcon class="w-4 h-4" />

                </button>

            </div>

        </div>


        <!-- ================= TOGGLE "RADIUS LOKASI" ================= -->
        <!-- Ini yang tampil duluan. Form edit koordinat & radius BARU -->
        <!-- muncul setelah bagian ini diklik. -->

        <button
            type="button"
            @click="toggleExpanded"
            class="w-full flex items-center justify-between gap-3 px-6 py-4
                   text-left hover:bg-slate-50 transition"
            :class="isNew ? '' : 'mt-3 border-t border-slate-100'">

            <span class="flex items-center gap-2">

                <PlusIcon
                    v-if="isNew"
                    class="w-5 h-5 text-emerald-500" />

                <span
                    class="font-semibold"
                    :class="isNew ? 'text-emerald-600' : 'text-slate-700'">
                    {{ isNew ? 'Tambah Lokasi Baru' : 'Radius Lokasi' }}
                </span>

                <span
                    v-if="!isNew"
                    class="text-sm text-slate-400">
                    ({{ location.radius_meters }} m)
                </span>

            </span>

            <ChevronUpIcon
                v-if="expanded"
                class="w-5 h-5 text-slate-400 flex-shrink-0" />

            <ChevronDownIcon
                v-else
                class="w-5 h-5 text-slate-400 flex-shrink-0" />

        </button>


        <!-- ================= FORM EDIT (muncul saat expanded) ================= -->

        <div
            v-show="expanded"
            class="px-6 pb-6 pt-1 border-t border-slate-100">

            <div
                v-if="isNew"
                class="mb-4">

                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Nama Lokasi
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Contoh: RSUD Cibabat - Gerbang Utama"
                    class="w-full rounded-xl border border-slate-200
                           px-4 py-3
                           focus:outline-none
                           focus:ring-2 focus:ring-emerald-500"
                />

                <p
                    v-if="errors.name"
                    class="text-sm text-red-500 mt-1">

                    {{ errors.name }}

                </p>

            </div>

            <div class="mb-5">
                <LocationMapPicker 
                    v-model:latitude="form.latitude"
                    v-model:longitude="form.longitude"
                    :radius="form.radius_meters"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <!-- Latitude -->

                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Latitude
                    </label>

                    <input
                        v-model="form.latitude"
                        type="text"
                        inputmode="decimal"
                        placeholder="-6.87911457"
                        class="w-full rounded-xl border border-slate-200
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2 focus:ring-emerald-500"
                    />

                    <p
                        v-if="errors.latitude"
                        class="text-sm text-red-500 mt-1">

                        {{ errors.latitude }}

                    </p>

                </div>


                <!-- Longitude -->

                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Longitude
                    </label>

                    <input
                        v-model="form.longitude"
                        type="text"
                        inputmode="decimal"
                        placeholder="107.55098746"
                        class="w-full rounded-xl border border-slate-200
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2 focus:ring-emerald-500"
                    />

                    <p
                        v-if="errors.longitude"
                        class="text-sm text-red-500 mt-1">

                        {{ errors.longitude }}

                    </p>

                </div>


                <!-- Radius -->

                <div>

                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Radius (meter)
                    </label>

                    <input
                        v-model.number="form.radius_meters"
                        type="number"
                        min="5"
                        max="5000"
                        placeholder="60"
                        class="w-full rounded-xl border border-slate-200
                               px-4 py-3
                               focus:outline-none
                               focus:ring-2 focus:ring-emerald-500"
                    />

                    <p
                        v-if="errors.radius_meters"
                        class="text-sm text-red-500 mt-1">

                        {{ errors.radius_meters }}

                    </p>

                </div>

            </div>

            <p class="text-xs text-slate-400 mt-3">
                Pegawai dianggap berada di lokasi jika jaraknya dari titik koordinat
                ini tidak lebih dari radius yang diatur di atas. Pegawai tidak bisa
                mengubah nilai ini sendiri.
            </p>

            <div class="flex justify-end gap-3 mt-5">

                <button
                    v-if="!isNew"
                    type="button"
                    @click="expanded = false"
                    class="px-5 py-2.5 rounded-xl
                           border border-slate-200
                           bg-white text-slate-600
                           font-semibold text-sm
                           hover:bg-slate-50 transition">

                    Batal

                </button>

                <button
                    type="button"
                    :disabled="processing"
                    @click="save"
                    class="px-5 py-2.5 rounded-xl
                           bg-emerald-500 hover:bg-emerald-600
                           text-white font-semibold text-sm
                           transition disabled:opacity-50">

                    {{ processing ? 'Menyimpan...' : (isNew ? 'Tambah Lokasi' : 'Simpan Radius') }}

                </button>

            </div>

        </div>

    </div>

</template>
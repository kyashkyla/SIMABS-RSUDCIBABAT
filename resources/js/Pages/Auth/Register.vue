<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="username" value="Username" />

                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    required
                />

                <InputError :message="form.errors.username" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    BuildingOffice2Icon,
    UserIcon,
    IdentificationIcon,
    EnvelopeIcon,
    PhoneIcon,
    MapPinIcon,
    LockClosedIcon,
    EyeIcon,
    EyeSlashIcon
} from '@heroicons/vue/24/outline'

import { ref } from 'vue'

const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = useForm({
    name: '',
    nip: '',
    username: '',
    email: '',
    phone: '',
    address: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'))
}
</script>
<template>

<Head title="Daftar Akun"/>

<div class="min-h-screen bg-slate-100 flex items-center justify-center p-6">

<div class="w-full max-w-7xl bg-white rounded-3xl overflow-hidden shadow-2xl grid lg:grid-cols-2">

<!-- ================= LEFT ================= -->

<div
class="bg-gradient-to-br from-emerald-500 to-green-500 text-white p-12 relative overflow-hidden">

<div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-white/10"></div>

<div class="flex items-center gap-4">

<div class="bg-white/15 p-3 rounded-2xl">

<BuildingOffice2Icon class="w-8 h-8"/>

</div>

<div>

<h2 class="text-2xl font-bold">

SIMABS

</h2>

<p class="text-white/80">

RSUD Cibabat Cimahi

</p>

</div>

</div>

<h1 class="text-5xl font-bold mt-16 leading-tight">

Sistem Informasi
<br>
Manajemen Absensi

</h1>

<p class="mt-8 text-lg leading-8 text-white/90">

Daftarkan akun Anda untuk mengakses sistem absensi digital RSUD Cibabat
dengan teknologi Face Recognition berbasis Geolocation.

</p>

<div class="mt-24 space-y-5">

<div class="bg-white/10 rounded-xl p-4">

✔ Absensi berbasis Face Recognition

</div>

<div class="bg-white/10 rounded-xl p-4">

✔ Validasi lokasi GPS real-time

</div>

<div class="bg-white/10 rounded-xl p-4">

✔ Laporan kehadiran otomatis

</div>

</div>

</div>

<!-- ================= RIGHT ================= -->

<div class="p-10 overflow-y-auto">

<h2 class="text-4xl font-bold">

Daftar Akun Baru

</h2>

<p class="text-gray-500 mt-2">

Isi semua data di bawah ini dengan benar.

</p>

<form
@submit.prevent="submit"
class="space-y-5 mt-8">

<!-- Nama -->

<div>

<label>Nama Lengkap</label>

<div class="relative mt-2">

<UserIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.name"
type="text"
placeholder="Nama Lengkap"
class="w-full pl-12 py-3 border rounded-xl"/>

</div>

</div>

<!-- NIP -->

<div>

<label>NIP</label>

<div class="relative mt-2">

<IdentificationIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.nip"
type="text"
placeholder="1985xxxxxxxx"
class="w-full pl-12 py-3 border rounded-xl"/>

</div>

</div>

<!-- Username -->

<div>

<label>Username</label>

<div class="relative mt-2">

<UserIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.username"
type="text"
placeholder="Username"
class="w-full pl-12 py-3 border rounded-xl"/>

</div>

</div>

<!-- Email -->

<div>

<label>Email</label>

<div class="relative mt-2">

<EnvelopeIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.email"
type="email"
placeholder="nama@email.com"
class="w-full pl-12 py-3 border rounded-xl"/>

</div>

</div>

<!-- Phone -->

<div>

<label>No. Telepon</label>

<div class="relative mt-2">

<PhoneIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.phone"
type="text"
placeholder="08xxxxxxxxxx"
class="w-full pl-12 py-3 border rounded-xl"/>

</div>

</div>

<!-- Address -->

<div>

<label>Alamat</label>

<div class="relative mt-2">

<MapPinIcon
class="w-5 h-5 absolute left-4 top-4 text-gray-400"/>

<textarea
v-model="form.address"
rows="3"
placeholder="Alamat"
class="w-full pl-12 py-3 border rounded-xl"></textarea>

</div>

</div>

<!-- Password -->

<div class="grid md:grid-cols-2 gap-4">

<div>

<label>Password</label>

<div class="relative mt-2">

<LockClosedIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.password"
:type="showPassword?'text':'password'"
class="w-full pl-12 pr-12 py-3 border rounded-xl"/>

<button
type="button"
class="absolute right-4 top-1/2 -translate-y-1/2"
@click="showPassword=!showPassword">

<EyeIcon
v-if="!showPassword"
class="w-5"/>

<EyeSlashIcon
v-else
class="w-5"/>

</button>

</div>

</div>

<div>

<label>Konfirmasi Password</label>

<div class="relative mt-2">

<LockClosedIcon
class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"/>

<input
v-model="form.password_confirmation"
:type="showConfirmPassword?'text':'password'"
class="w-full pl-12 pr-12 py-3 border rounded-xl"/>

<button
type="button"
class="absolute right-4 top-1/2 -translate-y-1/2"
@click="showConfirmPassword=!showConfirmPassword">

<EyeIcon
v-if="!showConfirmPassword"
class="w-5"/>

<EyeSlashIcon
v-else
class="w-5"/>

</button>

</div>

</div>

</div>

<button
class="w-full mt-4 bg-gradient-to-r from-emerald-500 to-green-500 text-white py-4 rounded-xl font-semibold">

Daftar & Verifikasi Wajah

</button>

<div class="text-center mt-4 text-gray-500">

Sudah punya akun?

<Link
:href="route('login')"
class="text-emerald-600 font-semibold">

Masuk Sekarang

</Link>

</div>

</form>

</div>

</div>

</div>

</template>
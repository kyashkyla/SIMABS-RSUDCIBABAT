<script setup>
import { onMounted, onBeforeUnmount, ref, watch } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
    latitude: { type: [String, Number], required: true },
    longitude: { type: [String, Number], required: true },
    radius: { type: [String, Number], required: true },
})

const emit = defineEmits(['update:latitude', 'update:longitude'])

const mapContainer = ref(null)
let map = null
let marker = null
let circle = null

// Flag agar update dari watcher (input manual) tidak memicu loop balik
// saat kita sendiri yang menggerakkan marker via drag/klik.
let isInternalUpdate = false

// Ikon pin kustom (tidak bergantung pada file gambar bawaan Leaflet,
// jadi aman dipakai di Vite tanpa konfigurasi asset tambahan).
const pinIcon = L.divIcon({
    className: '',
    html: `
        <div style="
            width: 28px; height: 28px;
            background: #059669;
            border: 3px solid white;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            box-shadow: 0 2px 6px rgba(0,0,0,0.35);
        "></div>
    `,
    iconSize: [28, 28],
    iconAnchor: [14, 28],
})

const toNumber = (v) => parseFloat(v) || 0

onMounted(() => {
    const lat = toNumber(props.latitude)
    const lng = toNumber(props.longitude)

    map = L.map(mapContainer.value).setView([lat, lng], 17)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map)

    marker = L.marker([lat, lng], { draggable: true, icon: pinIcon }).addTo(map)

    circle = L.circle([lat, lng], {
        radius: toNumber(props.radius),
        color: '#059669',
        fillColor: '#10B981',
        fillOpacity: 0.15,
        weight: 2,
    }).addTo(map)

    const applyPosition = (latlng) => {
        isInternalUpdate = true
        marker.setLatLng(latlng)
        circle.setLatLng(latlng)
        emit('update:latitude', latlng.lat.toFixed(6))
        emit('update:longitude', latlng.lng.toFixed(6))
    }

    marker.on('dragend', () => applyPosition(marker.getLatLng()))
    map.on('click', (e) => applyPosition(e.latlng))
})

onBeforeUnmount(() => {
    map?.remove()
})

// Saat latitude/longitude berubah dari luar (input manual atau tombol
// "Gunakan Lokasi Saat Ini"), sinkronkan posisi marker & peta.
watch(() => [props.latitude, props.longitude], ([lat, lng]) => {
    if (isInternalUpdate) {
        isInternalUpdate = false
        return
    }

    if (!map || !marker) return

    const latlng = [toNumber(lat), toNumber(lng)]
    marker.setLatLng(latlng)
    circle.setLatLng(latlng)
    map.setView(latlng)
})

// Radius berubah lewat input angka -> perbesar/perkecil lingkaran di peta.
watch(() => props.radius, (radius) => {
    circle?.setRadius(toNumber(radius))
})
</script>

<template>
    <div class="rounded-2xl overflow-hidden border border-slate-200">

        <div ref="mapContainer" style="height: 320px; width: 100%;"></div>

        <div class="bg-slate-50 px-4 py-2.5 text-xs text-slate-500">
            Klik pada peta atau geser pin untuk memindahkan titik lokasi absensi.
            Lingkaran hijau menunjukkan radius yang diizinkan.
        </div>

    </div>
</template>

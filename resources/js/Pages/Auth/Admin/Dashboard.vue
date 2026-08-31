<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import AttendanceChart from '@/Components/Admin/AttendanceChart.vue'
import StatusCard from '@/Components/Admin/StatusCard.vue'
import AttendanceTable from '@/Components/Admin/AttendanceTable.vue'

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalPegawai: 0,
            hadir: 0,
            terlambat: 0,
            tidakHadir: 0,
            belumAbsen: 0,
        }),
    },
    chart: {
        type: Object,
        default: () => ({ labels: [], hadir: [], terlambat: [], tidakHadir: [] }),
    },
    latestAttendance: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <AdminLayout>

        <!-- Statistik -->

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <StatCard
                title="Total Pegawai"
                :value="props.stats.totalPegawai"
                subtitle="Pegawai Aktif"
                color="emerald"
                icon="users"
            />

            <StatCard
                title="Hadir Hari Ini"
                :value="props.stats.hadir"
                :subtitle="props.stats.totalPegawai ? Math.round((props.stats.hadir / props.stats.totalPegawai) * 100) + '% Kehadiran' : '0% Kehadiran'"
                color="green"
                icon="hadir"
            />

            <StatCard
                title="Terlambat"
                :value="props.stats.terlambat"
                subtitle="Hari Ini"
                color="yellow"
                icon="terlambat"
            />

            <StatCard
                title="Tidak Hadir"
                :value="props.stats.tidakHadir"
                subtitle="Hari Ini"
                color="red"
                icon="tidakhadir"
            />

        </div>

        <div class="grid lg:grid-cols-3 gap-6 mt-8">

            <!-- Grafik -->
            <div class="lg:col-span-2">

                <AttendanceChart
                    :labels="props.chart.labels"
                    :hadir="props.chart.hadir"
                    :terlambat="props.chart.terlambat"
                    :tidakHadir="props.chart.tidakHadir"
                />

            </div>

            <!-- Status -->
            <StatusCard
                :hadir="props.stats.hadir"
                :terlambat="props.stats.terlambat"
                :tidakHadir="props.stats.tidakHadir"
                :belumAbsen="props.stats.belumAbsen"
                :total="props.stats.totalPegawai"
            />

        </div>

        <!-- Tabel -->

        <AttendanceTable :attendances="props.latestAttendance" />

    </AdminLayout>
</template>

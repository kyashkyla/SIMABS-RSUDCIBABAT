<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Admin/StatCard.vue'
import AttendanceChart from '@/Components/Admin/AttendanceChart.vue'
import StatusCard from '@/Components/Admin/StatusCard.vue'
import AttendanceTable from '@/Components/Admin/AttendanceTable.vue'
</script>

<template>
    <AdminLayout>

        <!-- Statistik -->

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <StatCard
                title="Total Pegawai"
                :value="132"
                subtitle="Pegawai Aktif"
                color="emerald"
                icon="users"
            />

            <StatCard
                title="Hadir Hari Ini"
                :value="120"
                subtitle="91% Kehadiran"
                color="green"
                icon="hadir"
            />

            <StatCard
                title="Terlambat"
                :value="8"
                subtitle="Hari Ini"
                color="yellow"
                icon="terlambat"
            />

            <StatCard
                title="Tidak Hadir"
                :value="4"
                subtitle="Hari Ini"
                color="red"
                icon="tidakhadir"
            />

        </div>

        <div class="grid lg:grid-cols-3 gap-6 mt-8">

            <!-- Grafik -->
            <div class="lg:col-span-2">

                <AttendanceChart />

            </div>

            <!-- Status -->
            <StatusCard
                :hadir="120"
                :terlambat="8"
                :tidakHadir="4"
                :belumAbsen="0"
                :total="132"
            />

        </div>

        <!-- Tabel -->

        <AttendanceTable />

    </AdminLayout>
</template>
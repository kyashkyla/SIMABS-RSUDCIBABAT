<script setup>
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js'

import { Bar } from 'vue-chartjs'

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend
)

const chartData = {
    labels: [
        'Sen',
        'Sel',
        'Rab',
        'Kam',
        'Jum',
        'Sab'
    ],

    datasets: [
        {
            label: 'Hadir',
            backgroundColor: '#10B981',
            data: [120, 118, 125, 122, 119, 115],
            borderRadius: 8,
        },

        {
            label: 'Terlambat',
            backgroundColor: '#F59E0B',
            data: [8, 10, 6, 7, 9, 5],
            borderRadius: 8,
        },

        {
            label: 'Tidak Hadir',
            backgroundColor: '#EF4444',
            data: [4, 3, 2, 5, 4, 6],
            borderRadius: 8,
        },
    ],
}

const chartOptions = {

    responsive: true,

    maintainAspectRatio: false,

    plugins: {

        legend: {

            position: 'top',

            labels: {

                usePointStyle: true,

                pointStyle: 'circle',

                padding: 20,

            },

        },

    },

    scales: {

        y: {

            beginAtZero: true,

            ticks: {

                stepSize: 20,

            },

            grid: {

                color: '#E5E7EB',

            },

        },

        x: {

            grid: {

                display: false,

            },

        },

    },

}
</script>

<template>

<div class="bg-white rounded-2xl shadow-lg p-6">

    <div class="flex justify-between items-center mb-6">

        <div>

            <h2 class="text-xl font-bold text-slate-800">

                Tren Absensi Mingguan

            </h2>

            <p class="text-gray-500 text-sm">

                Statistik kehadiran pegawai

            </p>

        </div>

        <button
            class="px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition">

            Detail

        </button>

    </div>

    <div class="h-96">

        <Bar
            :data="chartData"
            :options="chartOptions" />

    </div>

</div>

</template>
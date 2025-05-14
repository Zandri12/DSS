<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  RadarController,
  RadialLinearScale,
  PointElement,
  ArcElement, // <-- Add this for Pie chart
  PieController,
  LineController,
  LineElement,
  DoughnutController,
  DoughnutControllerChartOptions,
} from 'chart.js';
import { Bar, Radar, Pie, Line, Doughnut} from 'vue-chartjs';

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  RadarController,
  RadialLinearScale,
  PointElement,
  LineElement,
  ArcElement, // <-- Add this for Pie chart
  PieController,
  LineController,
  DoughnutController,
);

// Ambil data dari backend
const page = usePage();
const rawData = page.props.grafikData as {
  nama_siswa: string;
  nama_kriteria: string;
  jawaban: number;
}[];

// Proses data untuk Bar Chart
const siswaSet = [...new Set(rawData.map(d => d.nama_siswa))];
const kriteriaSet = [...new Set(rawData.map(d => d.nama_kriteria))];

const barChartData = {
  labels: siswaSet,
  datasets: kriteriaSet.map(kriteria => ({
    label: kriteria,
    data: siswaSet.map(siswa => {
      const found = rawData.find(r => r.nama_siswa === siswa && r.nama_kriteria === kriteria);
      return found ? found.jawaban : 0;
    }),
    backgroundColor: `hsl(${Math.random() * 360}, 70%, 70%)`,
  })),
};

const barChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Perbandingan Jawaban Siswa per Kriteria' },
  },
};

// Radar Chart (ambil satu siswa saja, misalnya siswa pertama)
const firstSiswa = siswaSet[0];

const radarChartData = {
  labels: kriteriaSet,
  datasets: [
    {
      label: `Nilai ${firstSiswa}`,
      data: kriteriaSet.map(kriteria => {
        const found = rawData.find(r => r.nama_siswa === firstSiswa && r.nama_kriteria === kriteria);
        return found ? found.jawaban : 0;
      }),
      backgroundColor: 'rgba(54, 162, 235, 0.2)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1,
    },
  ],
};

const radarChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: `Radar Chart: ${firstSiswa}` },
  },
};

// Pie Chart (status siswa)
const statusData = page.props.statusData as {
  status: string;
  total: number;
}[];

const pieChartData = {
  labels: statusData.map(d => d.status),
  datasets: [
    {
      label: 'Jumlah Siswa',
      data: statusData.map(d => d.total),
      backgroundColor: ['#f87171', '#34d399', '#60a5fa', '#fbbf24'],
    },
  ],
};

const pieChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'right' as const },
    title: { display: true, text: 'Distribusi Status Siswa' },
  },
};

const lineChartData = {
  labels: kriteriaSet,
  datasets: siswaSet.map(siswa => ({
    label: siswa,
    data: kriteriaSet.map(kriteria => {
      const found = rawData.find(
        r => r.nama_siswa === siswa && r.nama_kriteria === kriteria
      );
      return found ? found.jawaban : 0;
    }),
    fill: false,
    borderColor: `hsl(${Math.random() * 360}, 70%, 50%)`,
    tension: 0.1,
  })),
};

const lineChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Line Chart: Jawaban Siswa per Kriteria' },
  },
};

const doughnutChartData = {
  labels: siswaSet,
  datasets: [
    {
      label: 'Total Jawaban',
      data: siswaSet.map(siswa =>
        rawData
          .filter(r => r.nama_siswa === siswa)
          .reduce((sum, curr) => sum + curr.jawaban, 0)
      ),
      backgroundColor: siswaSet.map(
        (_, i) => `hsl(${(i * 360) / siswaSet.length}, 70%, 70%)`
      ),
    },
  ],
};

const doughnutChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Doughnut Chart: Total Jawaban per Siswa' },
  },
};

</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/dashboard' }]">
     <div class="grid grid-cols-1 gap-6 md:grid-cols-2 p-4">
      <!-- Bar Chart -->
      <div class="rounded-xl border p-4 shadow bg-white dark:bg-background">
        <Bar :data="barChartData" :options="barChartOptions" />
      </div>

      <!-- Radar Chart -->
      <div class="rounded-xl border p-4 shadow bg-white dark:bg-background">
        <Radar :data="radarChartData" :options="radarChartOptions" />
      </div>

      <!-- Pie Chart -->
      <div class="rounded-xl border p-4 shadow bg-white dark:bg-background">
        <div class="w-64 h-64 mx-auto">
            <Pie :data="pieChartData" :options="pieChartOptions" />
        </div>
        </div>

      <div class="grid gap-6 p-4 md:grid-cols-2">
    <!-- Chart sebelumnya (Bar, Radar, Pie) -->
    <div class="rounded-xl border p-4 shadow">
      <Line :data="lineChartData" :options="lineChartOptions" />
    </div>

    <div class="rounded-xl border p-4 shadow">
      <Doughnut :data="doughnutChartData" :options="doughnutChartOptions" />
    </div>
  </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { Input } from '@/components/ui/input';
import { Calendar } from 'lucide-vue-next' ;
import { CalendarRange } from 'lucide-vue-next';
import { format } from 'date-fns';



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

type DateRange = {
  start: Date | undefined
  end: Date | undefined
}



const filters = usePage().props.filters as {
  from: string | null;
  to: string | null;
};



// Konversi string ke Date jika tersedia
function parseDate(dateStr: string | null): Date | undefined {
  return dateStr ? new Date(dateStr) : undefined
}

const selectedRange = ref<any>(null)

const displayRange = computed(() => {
  if (!selectedRange.value?.from || !selectedRange.value?.to) return ''
  return `${selectedRange.value.from.toLocaleDateString()} - ${selectedRange.value.to.toLocaleDateString()}`
})

function toJsDate(d: any): Date | undefined {
  if (!d) return undefined;
  // bulan di JS Date itu 0-based, jadi dikurangi 1
  return new Date(d.year, d.month - 1, d.day);
}

function applyFilter() {
  const startDate = toJsDate(selectedRange.value.start);
  const endDate = toJsDate(selectedRange.value.end);

  router.get('/dashboard', {
    from: startDate ? startDate.toISOString().slice(0, 10) : undefined,
    to: endDate ? endDate.toISOString().slice(0, 10) : undefined,
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      // Hilangkan query string setelah data berhasil dimuat
      window.history.replaceState({}, '', '/dashboard');
    },
  });
}



// Ambil data dari backend
const page = usePage();

const rawData = computed(() => page.props.grafikData as {
  nama_siswa: string;
  nama_kriteria: string;
  jawaban: number;
}[])

const siswaSet = computed(() => [...new Set(rawData.value.map(d => d.nama_siswa))]);
const kriteriaSet = computed(() => [...new Set(rawData.value.map(d => d.nama_kriteria))]);

const barChartData = computed(() => ({
  labels: siswaSet.value,
  datasets: kriteriaSet.value.map(kriteria => ({
    label: kriteria,
    data: siswaSet.value.map(siswa => {
      const found = rawData.value.find(r => r.nama_siswa === siswa && r.nama_kriteria === kriteria);
      return found ? found.jawaban : 0;
    }),
    backgroundColor: `hsl(${Math.random() * 360}, 70%, 70%)`,
  })),
}));

const barChartOptions = {
  responsive: true,
  indexAxis: 'y' as 'x' | 'y', // Explicitly type as 'x' | 'y'
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Perbandingan Jawaban Siswa per Kriteria' },
  },
};


// Radar Chart (ambil satu siswa saja, misalnya siswa pertama)
const radarChartData = computed(() => ({
  labels: kriteriaSet.value, // Unwrap the ComputedRef here
  datasets: siswaSet.value.map((siswa: any, index: number) => {
    const colorHue = (index * 60) % 360;
    return {
      label: `Nilai ${siswa}`,
      data: kriteriaSet.value.map((kriteria: any) => {
        const found = rawData.value.find((r: { nama_siswa: any; nama_kriteria: any; }) => r.nama_siswa === siswa && r.nama_kriteria === kriteria);
        return found ? found.jawaban : 0;
      }),
      backgroundColor: `hsla(${colorHue}, 70%, 70%, 0.2)`,
      borderColor: `hsl(${colorHue}, 70%, 50%)`,
      borderWidth: 1,
    };
  }),
}));


const radarChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Radar Chart: Semua Siswa' },
  },
};



// Pie Chart (status siswa)
const statusData = page.props.statusData as {
  status: string;
  total: number;
}[];

const generateColor = (index: number, total: number) =>
  `hsl(${(index * 360) / total}, 70%, 60%)`;

const pieChartData = computed(() => ({
  labels: statusData.map(d => d.status),
  datasets: [
    {
      label: 'Jumlah Siswa',
      data: statusData.map(d => d.total),
      backgroundColor: statusData.map((_, index) =>
        generateColor(index, statusData.length)
      ),
      borderColor: '#fff',
      borderWidth: 2,
      hoverOffset: 12,
    },
  ],
}));


const pieChartOptions = {
  responsive: true,
  plugins: {
    
    legend: { position: 'right' as const },
    title: {
      display: true,
      text: 'Distribusi Status Siswa',
      font: {
        size: 18,
        weight: 'bold' as const,
      },
    },
    
    tooltip: {
      callbacks: {
        label: function (tooltipItem: any) {
          const total = statusData.reduce((sum, item) => sum + item.total, 0);
          const value = statusData[tooltipItem.dataIndex].total;
          const percentage = ((value / total) * 100).toFixed(1);
          return `${tooltipItem.label}: ${value} siswa (${percentage}%)`;
        },
      },
    },
  },
  animation: {
    animateRotate: true,
    animateScale: true,
  },
};





const lineChartData = computed(() => ({
  labels: kriteriaSet.value, // Unwrap the ComputedRef here
  datasets: siswaSet.value.map((siswa: any) => ({
    label: siswa,
    data: kriteriaSet.value.map((kriteria: any) => {
      const found = rawData.value.find((r: { nama_siswa: any; nama_kriteria: any; }) => r.nama_siswa === siswa && r.nama_kriteria === kriteria);
      return found ? found.jawaban : 0;
    }),
    fill: false,
    borderColor: `hsl(${Math.random() * 360}, 70%, 50%)`,
    tension: 0.1,
  })),
}));


const lineChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Line Chart: Jawaban Siswa per Kriteria' },
  },
};

const doughnutChartData = computed(() => ({
  labels: siswaSet.value, // Unwrap the ComputedRef here
  datasets: [
    {
      label: 'Total Jawaban',
      data: siswaSet.value.map((siswa: any) =>
        rawData
          .value.filter((r: { nama_siswa: any; }) => r.nama_siswa === siswa)
          .reduce((sum: any, curr: { jawaban: any; }) => sum + curr.jawaban, 0)
      ),
      backgroundColor: siswaSet.value.map(
        (_: any, i: number) => `hsl(${(i * 360) / siswaSet.value.length}, 70%, 70%)`
      ),
    },
  ],
}));


const doughnutChartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' as const },
    title: { display: true, text: 'Doughnut Chart: Total Jawaban per Siswa' },
  },
};

const isLoading = ref(true)
const showChart = ref(false) // kontrol kapan chart ditampilkan setelah delay

const checkDataReady = () => {
  const isDataReady =
    rawData.value.length > 0 && siswaSet.value.length > 0 && kriteriaSet.value.length > 0

  if (isDataReady) {
    isLoading.value = false
    // tambahkan delay sebelum menampilkan chart
    setTimeout(() => {
      showChart.value = true
    }, 500) // 500ms delay
  } else {
    isLoading.value = true
    showChart.value = false
  }
}

onMounted(() => {
  // Cek awal saat mounted
  checkDataReady()

  // Tambahan delay untuk memastikan data benar-benar siap
  setTimeout(() => {
    checkDataReady()
  }, 300) // kamu bisa sesuaikan delay ini, misal 300ms–500ms
})
watch([rawData, siswaSet, kriteriaSet], checkDataReady)

</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/dashboard' }]">
    <div class="flex flex-1 flex-col gap-4 p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3">
        <div class="aspect-video rounded-xl bg-muted/50" />
        <div class="aspect-video rounded-xl bg-muted/50" />
        <div class="aspect-video rounded-xl bg-muted/50" />
      </div>
      <!-- <div class="min-h-[100vh] flex-1 rounded-xl bg-muted/50 md:min-h-min" /> -->
    </div>
   <div class="flex gap-2 p-4 w-full max-w-full items-center">
    <Popover class="flex-1">
      <PopoverTrigger as-child>
        <div class="relative w-50">
          <Input
            readonly
            :value="displayRange"
            placeholder="Pilih rentang tanggal"
            class="cursor-pointer pl-10 w-50"
          />
          <!-- Ikon Kalender -->
          <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 h-4 w-4" />
        </div>
        <button
          @click="applyFilter"
          class="inline-flex items-center gap-1 bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 transition text-sm"
        >
          <CalendarRange class="h-4 w-4" />
          Filter
        </button>
      </PopoverTrigger>
      <PopoverContent class="w-72">
        <RangeCalendar v-model="selectedRange" />
      </PopoverContent>
    </Popover>
  </div>
     <div class="grid grid-cols-1 gap-6 md:grid-cols-2 p-4">
      <!-- Bar Chart -->
      <div class="rounded-xl border p-4 shadow bg-white dark:bg-background">
        <Skeleton v-if="isLoading" class="w-full h-64 rounded-md" />
        <div v-if="showChart" class="relative w-full h-full"> <!-- Atur tinggi chart di sini -->
          <Bar :data="barChartData" :options="barChartOptions" />
        </div>
      </div>

      <!-- Radar Chart -->
      <div class="rounded-xl border p-4 shadow bg-white dark:bg-background">
         <Skeleton v-if="isLoading" class="w-full h-64 rounded-md" />
        <Radar v-if="showChart" :data="radarChartData" :options="radarChartOptions" />
      </div>

      <!-- Pie Chart -->
      <div class="rounded-xl border p-4 shadow bg-white dark:bg-background">
        <div class="w-78 h-full mx-auto">
           <Skeleton v-if="isLoading" class="w-full h-64 rounded-md" />
            <Pie v-if="showChart" :data="pieChartData" :options="pieChartOptions" />
        </div>
        </div>

      <div class="grid gap-6 p-4 md:grid-cols-2">
    <!-- Chart sebelumnya (Bar, Radar, Pie) -->
    <div class="rounded-xl border p-4 shadow">
       <Skeleton v-if="isLoading" class="w-full h-64 rounded-md" />
      <Line v-if="showChart" :data="lineChartData" :options="lineChartOptions" />
    </div>

    <div class="rounded-xl border p-4 shadow">
       <Skeleton v-if="isLoading" class="w-full h-64 rounded-md" />
      <Doughnut v-if="showChart" :data="doughnutChartData" :options="doughnutChartOptions" />
    </div>
  </div>
    </div>
  </AppLayout>
</template>

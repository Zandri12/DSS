<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head } from '@inertiajs/vue3'
import { h, ref, computed, reactive, onMounted } from 'vue'

const openFormJawaban = ref(false)
const jawaban = reactive<{ [key: string]: string }>({})

// Load jawaban dari localStorage saat komponen dimuat
onMounted(() => {
  const savedJawaban = localStorage.getItem('dss_jawaban')
  if (savedJawaban) {
    const parsed = JSON.parse(savedJawaban)
    Object.assign(jawaban, parsed)
  } else {
    initJawaban()
  }
})

// Inisialisasi jawaban berdasarkan kriteria
const initJawaban = () => {
  Object.keys(jawaban).forEach(key => delete jawaban[key])
  props.kriteria.forEach((k) => {
    jawaban[k.nama_kriteria] = ''
  })
}

const selectedSiswaNama = ref('')


const submitJawaban = async () => {
  if (!selectedSiswaNama.value) {
    alert('Silakan pilih siswa terlebih dahulu.');
    return;
  }

  // Ubah objek jawaban menjadi array of objects
const jawabanArray = props.kriteria.map((kriteria) => ({
  kriteria_id: kriteria.id,
  nilai: parseFloat(jawaban[kriteria.nama_kriteria]) || 0,
}));

// Matriks Normalisasi
const normalisasiMatriks = computed(() => {
  const result: Record<number, Record<number, number>> = {}
  const kriteriaList = props.kriteria

  // Hitung max dan min untuk setiap kriteria
  const minMaxPerKriteria: Record<number, { min: number, max: number }> = {}
  kriteriaList.forEach(k => {
    const nilai = props.jawaban
      .filter(j => j.kriteria_id === k.id)
      .map(j => j.jawaban)

    minMaxPerKriteria[k.id] = {
      min: Math.min(...nilai),
      max: Math.max(...nilai)
    }
  })

  // Normalisasi
  props.siswa.forEach(s => {
    result[s.id] = {}
    kriteriaList.forEach(k => {
      const jawabanSiswa = jawabanMatriks.value[s.id]?.[k.id] ?? 0
      const { min, max } = minMaxPerKriteria[k.id]
      let normalized = 0

      if (max === min) {
        normalized = 1 // hindari pembagian nol
      } else {
        normalized = k.tipe === 'benefit'
          ? (jawabanSiswa - min) / (max - min)
          : (max - jawabanSiswa) / (max - min)
      }

      result[s.id][k.id] = normalized
    })
  })

  return result
})

// Matriks Tertimbang
const nilaiTertimbang = computed(() => {
  const result: Record<number, Record<number, number>> = {}

  props.siswa.forEach(s => {
    result[s.id] = {}
    props.kriteria.forEach(k => {
      const bobot = k.bobot
      const normal = normalisasiMatriks.value[s.id]?.[k.id] ?? 0
      result[s.id][k.id] = normal * bobot
    })
  })

  return result
})

// Hitung Si dan Ri
const hasilSiRi = computed(() => {
  const result: Record<number, { nama: string, Si: number, Ri: number }> = {}

  props.siswa.forEach(s => {
    const nilai = nilaiTertimbang.value[s.id]
    const values = Object.values(nilai)

    result[s.id] = {
      nama: s.nama_siswa,
      Si: values.reduce((a, b) => a + b, 0),
      Ri: values.length ? Math.max(...values) : 0
    }
  })

  return result
})



  console.log("Jawaban Array:", jawabanArray); // Cek hasil konversi jawaban

  // Format data yang akan dikirim
  const data = {
    siswa_id: selectedSiswaNama.value,  // Pastikan selectedSiswaNama berisi ID yang benar
    jawaban: jawabanArray,  // Kirim array jawaban yang sudah benar
    pilihan_dropdown:
      form.value.tipe_form === 'select'
        ? Array.isArray(form.value.pilihan_dropdown)
          ? form.value.pilihan_dropdown
          : JSON.parse(form.value.pilihan_dropdown || '[]')
        : undefined,
  };

  console.log("Data yang dikirim:", data); // Log data untuk memastikan formatnya benar

  try {
    const response = await axios.post('/jawaban', data);
    console.log('Response:', response);
    Swal.fire({
      title: 'Berhasil!',
      text: 'Jawaban berhasil disimpan.',
      icon: 'success',
      timer: 1500,
      showConfirmButton: false,
    });
  } catch (error) {
    console.error(error);
    Swal.fire({
      title: 'Gagal!',
      text: 'Terjadi kesalahan saat menyimpan jawaban.',
      icon: 'error',
      timer: 1500,
      showConfirmButton: false,
    });
  }
};


import type {
  ColumnDef,
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import {
  getCoreRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  getFilteredRowModel,
  useVueTable,
  getExpandedRowModel,
  FlexRender,
} from '@tanstack/vue-table'
import { valueUpdater } from '@/lib/utils'

import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Proses Perhitungan Seleksi Penerima Beasiswa',
    href: '/seleksi',
  },
]

const props = defineProps<{
  kriteria: Array<{
    id: number
    nama_kriteria: string
    bobot: number
    tipe: string
    deskripsi: string
    tipe_form: string
    pilihan_dropdown?: string[]
  }>
  siswa: Array<{
    nama_siswa: string
    [key: string]: any
  }>
  jawaban: Array<{
    siswa_id: number
    kriteria_id: number
    jawaban: number
  }>
}>()

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'

import {
  AlertDialog,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Plus, MoreHorizontal } from 'lucide-vue-next'
import axios from 'axios'
import Swal from 'sweetalert2'
import KriteriaAction from './KriteriaAction.vue'

const openDialog = ref(false)
const editingKriteria = ref(null)

const form = ref<{
  id: number | null
  nama_kriteria: string
  bobot: number
  tipe: string
  deskripsi: string
  tipe_form: string
  pilihan_dropdown?: string[]
  faktor?: string
}>({
  id: null,
  nama_kriteria: '',
  bobot: 0,
  tipe: '',
  deskripsi: '',
  tipe_form: '',
  pilihan_dropdown: [],
  faktor: '',
})

const rawBobot = ref('')
const handleBobotInput = (e: Event) => {
  const target = e.target as HTMLInputElement
  const value = target.value.replace(',', '.')
  form.value.bobot = parseFloat(value) || 0
}

const openCreate = () => {
  form.value = {
    id: null,
    nama_kriteria: '',
    bobot: 0,
    tipe: '',
    deskripsi: '',
    tipe_form: ''
  }
  rawBobot.value = ''
  openDialog.value = true
}

function normalizeDropdown(value: string[] | string | undefined): string[] {
  if (Array.isArray(value)) return value
  if (value === "[]") return []
  try {
    const parsed = JSON.parse(value ?? '[]')
    return Array.isArray(parsed) ? parsed : []
  } catch {
    return []
  }
}
const openEdit = (kriteria: {
  id: number
  nama_kriteria: string
  bobot: number
  tipe: string
  deskripsi: string
  tipe_form: string
  pilihan_dropdown?: string[]
}) => {
  form.value = {
    id: kriteria.id,
    nama_kriteria: kriteria.nama_kriteria,
    bobot: kriteria.bobot,
    tipe: kriteria.tipe,
    deskripsi: kriteria.deskripsi,
    tipe_form: kriteria.tipe_form,
    pilihan_dropdown: normalizeDropdown(kriteria.pilihan_dropdown),

  }
  rawBobot.value = kriteria.bobot.toString()
  openDialog.value = true
}


function getLabelFromDropdown(kriteriaId: number, value: number): string {
  const kriteria = props.kriteria.find(k => k.id === kriteriaId);
  const options = normalizeDropdown(kriteria?.pilihan_dropdown);
  
  if (!options || !Array.isArray(options)) return value.toString();

  // Jika options belum terparse ke objek dengan label dan value
  const parsed = options.map(opt => {
    if (typeof opt === 'string') {
      try {
        return JSON.parse(opt); // jika sudah berupa JSON string
      } catch {
        return { label: opt, value: opt };
      }
    }
    return opt;
  });

  const matched = parsed.find(opt => opt.value === value || Number(opt.value) === Number(value));
  return matched?.label ?? value.toString();
}

const normalisasiMatriks = computed(() => {
  const result: Record<number, Record<number, number>> = {}
  const minMaxPerKriteria: Record<number, { min: number, max: number }> = {}

  // Hitung nilai min dan max per kriteria
  props.kriteria.forEach(k => {
    const nilai = props.siswa.map(s => jawabanMatriks.value[s.id]?.[k.id] ?? 0)
    minMaxPerKriteria[k.id] = {
      min: Math.min(...nilai),
      max: Math.max(...nilai)
    }
  })

  // Normalisasi nilai siswa per kriteria
  props.siswa.forEach(s => {
    result[s.id] = {}
    props.kriteria.forEach(k => {
      const { min, max } = minMaxPerKriteria[k.id]
      const val = jawabanMatriks.value[s.id]?.[k.id] ?? 0
      let norm = 0

      if (max === min) {
        norm = 1
      } else {
        norm = k.tipe === 'benefit'
          ? (val - min) / (max - min)
          : (max - val) / (max - min)
      }

      result[s.id][k.id] = norm
    })
  })

  return result
})


const nilaiTertimbang = computed(() => {
  const result: Record<number, Record<number, number>> = {}

  props.siswa.forEach(s => {
    result[s.id] = {}
    props.kriteria.forEach(k => {
      const normal = normalisasiMatriks.value[s.id]?.[k.id] ?? 0
      result[s.id][k.id] = normal * k.bobot
    })
  })

  return result
})

const hasilSiRi = computed(() => {
  const result: Record<number, { nama: string, Si: number, Ri: number }> = {}

  props.siswa.forEach(s => {
    const nilai = nilaiTertimbang.value[s.id] ?? {}
    const values = Object.values(nilai)
    const total = values.reduce((a, b) => a + b, 0)
    const max = values.length > 0 ? Math.max(...values) : 0

    result[s.id] = {
      nama: s.nama_siswa,
      Si: total,
      Ri: max
    }
  })

  return result
})

const hasilQRanking = computed(() => {
  const siswaArray = Object.values(hasilSiRi.value)

  // Ambil nilai minimum dan maksimum
  const SiValues = siswaArray.map((item) => item.Si)
  const RiValues = siswaArray.map((item) => item.Ri)
  const SiMin = Math.min(...SiValues)
  const SiMax = Math.max(...SiValues)
  const RiMin = Math.min(...RiValues)
  const RiMax = Math.max(...RiValues)

  const v = 0.5 // konstanta VIKOR (bisa diganti)

  // Hitung Qi untuk setiap siswa
  const hasil = siswaArray.map((item) => {
    const Qi = v * ((item.Si - SiMin) / (SiMax - SiMin || 1)) +
               (1 - v) * ((item.Ri - RiMin) / (RiMax - RiMin || 1))
    return {
      nama: item.nama,
      Si: item.Si,
      Ri: item.Ri,
      Qi,
    }
  })

  // Urutkan berdasarkan nilai Qi terkecil (ranking)
  return hasil.sort((a, b) => a.Qi - b.Qi)
})



const jawabanMatriks = computed(() => {
  const matrix: Record<number, Record<number, number>> = {}
  props.jawaban.forEach(j => {
    if (!matrix[j.siswa_id]) {
      matrix[j.siswa_id] = {}
    }
    matrix[j.siswa_id][j.kriteria_id] = j.jawaban
  })
  return matrix
})
const handleDelete = async (id: number) => {
  const result = await Swal.fire({
    title: 'Yakin mau hapus?',
    text: "Data yang dihapus tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal',
  })

  if (result.isConfirmed) {
    try {
      axios.delete(`/kriteria/${id}`, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      })

      await Swal.fire({
        title: 'Terhapus!',
        text: 'Data berhasil dihapus.',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
      })
      
      window.location.reload()
    } catch (error) {
      await Swal.fire({
        title: 'Gagal!',
        text: 'Terjadi kesalahan saat menghapus data.',
        icon: 'error',
        timer: 1500,
        showConfirmButton: false,
      })
    }
  }
}

const siswa = props.siswa

// Mapping field di model Siswa ke kriteria
const kriteriaFieldMap: Record<string, keyof typeof siswa[0]> = {
  "nilai rapor": "nilai_rapor",
  "penghasilan": "penghasilan",
  "prestasi": "prestasi",
  "tanggungan": "tanggungan",
}

// Membentuk nilai matriks dari data siswa dan kriteria

const parsePilihanDropdown = (dropdown: any[]): Array<{ label: string, value: number }> => {
  return dropdown.map((item, index) => ({
    label: item,
    value: 5 - index  // Misalnya 5 untuk yang pertama, 4 untuk yang kedua, dst.
  }))
}
const submitForm = async () => {
  try {
    const formData = {
      ...form.value,
      bobot: parseFloat(rawBobot.value),
      tipe: form.value.tipe.toLowerCase(),
       pilihan_dropdown: form.value.pilihan_dropdown ? parsePilihanDropdown(form.value.pilihan_dropdown) : undefined
    };

    console.log('Form data:', formData);  // Log form data untuk memastikan
    
    let response;
    if (form.value.id) {
      // Update existing kriteria
      response = await axios.put(`/kriteria/${form.value.id}`, formData);
      console.log('Response:', response);  // Log response dari API
      await Swal.fire({
        title: 'Berhasil!',
        text: 'Kriteria berhasil diperbarui.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      });
    } else {
      // Create new kriteria
      response = await axios.post('/kriteria', formData);
      console.log('Response:', response);  // Log response dari API
      await Swal.fire({
        title: 'Berhasil!',
        text: 'Kriteria berhasil ditambahkan.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false,
      });
    }
    openDialog.value = false;
    window.location.reload();
  } catch (error) {
    console.error(error);  // Log error untuk debugging
    Swal.fire({
      title: 'Gagal!',
      text: 'Terjadi kesalahan saat menyimpan kriteria.',
      icon: 'error',
      timer: 1500,
      showConfirmButton: false,
    });
  }
}

function parseDropdown(val: string[] | string | undefined): { value: string, label: string }[] {
  if (Array.isArray(val)) {
    // anggap sudah dalam bentuk array of { value, label }
    return val as any
  }
  try {
    const parsed = JSON.parse(val ?? '[]')
    return Array.isArray(parsed) ? parsed : []
  } catch {
    return []
  }
}

const maxMinJawabanPerKriteria = computed(() => {
  const grouped: Record<number, number[]> = {}

  props.jawaban.forEach(j => {
    if (!grouped[j.kriteria_id]) {
      grouped[j.kriteria_id] = []
    }
    grouped[j.kriteria_id].push(j.jawaban)
  })

  const result = props.kriteria.map(k => {
    const nilai = grouped[k.id] || []
    return {
      nama_kriteria: k.nama_kriteria,
      max: Math.max(...nilai),
      min: Math.min(...nilai)
    }
  })

  return result
})

const wijMatrix = computed(() => {
  const matrix: Record<number, Record<number, number>> = {}

  props.kriteria.forEach(kriteria => {
    const kriteriaId = kriteria.id
    const isBenefit = kriteria.tipe.toLowerCase() === 'benefit'

    // Ambil semua nilai jawaban dari matriks yang sudah distuktur
    const allValues = props.siswa.map(s => {
      return Number(jawabanMatriks.value[s.id]?.[kriteriaId] ?? 0)
    })

    const fMax = Math.max(...allValues)
    const fMin = Math.min(...allValues)
    const range = fMax - fMin || 1 // Hindari pembagian nol

    props.siswa.forEach(s => {
      const siswaId = s.id
      const jawabanSiswa = Number(jawabanMatriks.value[siswaId]?.[kriteriaId] ?? 0)

      if (!matrix[siswaId]) {
        matrix[siswaId] = {}
      }

      let wij = 0
      if (isBenefit) {
        wij = (jawabanSiswa - fMin) / range
      } else {
        wij = (fMax - jawabanSiswa) / range
      }
      console.log({
        siswaId,
        kriteriaId,
        jawabanSiswa,
        fMin,
        fMax,
        wij
      })

      matrix[siswaId][kriteriaId] = parseFloat(wij.toFixed(4))
    })
    
  })
  
  return matrix
})




// State
const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})

// Columns config
const columns: ColumnDef<typeof props.kriteria[0]>[] = [
  {
    accessorKey: 'nama_kriteria',
    header: ({ column }) =>
      h(
        Button,
        {
          variant: 'ghost',
          onClick: () =>
            column.toggleSorting(column.getIsSorted() === 'asc'),
        },
        () => ['Nama Kriteria', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
      ),
  },
  {
    accessorKey: 'bobot',
    header: ({ column }) =>
      h(
        Button,
        {
          variant: 'ghost',
          onClick: () =>
            column.toggleSorting(column.getIsSorted() === 'asc'),
        },
        () => ['Bobot', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
      ),
    cell: ({ row }) => 
      h('div', { class: 'text-right font-medium' }, row.getValue('bobot')),
  },
  {
    accessorKey: 'deskripsi',
    header: 'Deskripsi',
    cell: ({ row }) => h('div', row.getValue('deskripsi')),
  },
  {
    accessorKey: 'tipe_form',
    header: 'Tipe Form',
    cell: ({ row }) => h('div', row.getValue('tipe_form')),
  },
  {
    accessorKey: 'tipe',
    header: 'Tipe',
    cell: ({ row }) =>
      h('div', { class: 'capitalize' }, row.getValue('tipe')),
  },
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }) => {
      return h(KriteriaAction, {
        kriteria: row.original,
        onEdit: openEdit,
        onDelete: handleDelete,
      })
    },
  },
]

// Table instance
const table = useVueTable({
  data: props.kriteria,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue =>
    valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue =>
    valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue =>
    valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue =>
    valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue =>
    valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() {
      return sorting.value
    },
    get columnFilters() {
      return columnFilters.value
    },
    get columnVisibility() {
      return columnVisibility.value
    },
    get rowSelection() {
      return rowSelection.value
    },
    get expanded() {
      return expanded.value
    },
  },
})


</script>

<template>
  <Head title="Seleksi" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 rounded-xl flex flex-col gap-4">
      <div class="flex items-center gap-2 py-4">
        <Button @click="() => { initJawaban(); openFormJawaban = true }" variant="secondary" class="ml-2">
          Pertanyaan
        </Button>
        <Button @click="openCreate"  class="flex items-center gap-2">
          <Plus class="w-4 h-4 mr-2" /> Tambah Kriteria
        </Button>
        <AlertDialog :open="openDialog" @update:open="openDialog = $event">
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>
                {{ form.id ? 'Edit Kriteria' : 'Tambah Kriteria' }}
              </AlertDialogTitle>
              <AlertDialogDescription>
                {{ form.id ? 'Perbarui data kriteria di bawah ini.' : 'Masukkan data kriteria baru di bawah ini.' }}
              </AlertDialogDescription>
            </AlertDialogHeader>

            <form @submit.prevent="submitForm">
              <div class="space-y-2">
                <Input v-model="form.nama_kriteria" placeholder="Nama Kriteria" />
                <Input v-model="form.faktor" placeholder="Faktor (contoh: akademik, non-akademik)" />

                <Input
                  v-model="rawBobot"
                  type="text"
                  placeholder="Bobot (gunakan titik/koma)"
                  @input="handleBobotInput"
                />
                <Input v-model="form.deskripsi" placeholder="Deskripsi Pertanyaan" />

                <!-- Pilih tipe form terlebih dahulu -->
                <Select v-model="form.tipe_form">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Pilih tipe form" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="input">Input</SelectItem>
                    <SelectItem value="select">Select</SelectItem>
                    <SelectItem value="textarea">Textarea</SelectItem>
                  </SelectContent>
                </Select>

                <!-- Tampilkan opsi sesuai tipe form yang dipilih -->
                <div v-if="form.tipe_form === 'select'" class="space-y-2">
                  <p class="text-sm text-gray-500">Pilihan untuk dropdown:</p>
                  <Input 
                  :model-value="form.pilihan_dropdown?.join(',') || ''"
                  @update:model-value="(value) => form.pilihan_dropdown = (typeof value === 'string' ? value.split(',').map(item => item.trim()) : [])"
                  placeholder="Contoh: rendah,sedang,tinggi" 
                  @input="(e: Event) => form.pilihan_dropdown = ((e.target as HTMLInputElement).value.split(',').map(item => item.trim()))"
                />
                </div>

                <Select v-model="form.tipe">
                  <SelectTrigger class="w-full">
                    <SelectValue placeholder="Pilih Tipe" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Benefit">Benefit</SelectItem>
                    <SelectItem value="Cost">Cost</SelectItem>
                  </SelectContent>
                </Select>


              </div>
              <AlertDialogFooter class="mt-4">
                <Button type="button" variant="outline" @click="openDialog = false">Batal</Button>
                <Button type="submit">Simpan</Button>
              </AlertDialogFooter>
            </form>
          </AlertDialogContent>
        </AlertDialog>
        <Input
          class="max-w-sm"
          placeholder="Filter nama kriteria..."
          :model-value="table.getColumn('nama_kriteria')?.getFilterValue() as string"
          @update:model-value="table.getColumn('nama_kriteria')?.setFilterValue($event)"
        />
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="ml-auto">
              Columns <ChevronDown class="ml-2 h-4 w-4" />
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuCheckboxItem
              v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
              :key="column.id"
              class="capitalize"
              :model-value="column.getIsVisible()"
              @update:model-value="(value) => column.toggleVisibility(!!value)"
            >
              {{ column.id }}
            </DropdownMenuCheckboxItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
              <TableHead v-for="header in headerGroup.headers" :key="header.id">
                <FlexRender
                  v-if="!header.isPlaceholder"
                  :render="header.column.columnDef.header"
                  :props="header.getContext()"
                />
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="table.getRowModel().rows?.length">
              <template v-for="row in table.getRowModel().rows" :key="row.id">
                <TableRow :data-state="row.getIsSelected() && 'selected'">
                  <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                    <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                  </TableCell>
                </TableRow>
              </template>
            </template>
            <TableRow v-else>
              <TableCell :colspan="columns.length" class="h-24 text-center">
                No results.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <div class="flex items-center justify-between py-4">
        <div class="text-sm text-muted-foreground">
          {{ table.getFilteredSelectedRowModel().rows.length }} dari
          {{ table.getFilteredRowModel().rows.length }} data dipilih.
        </div>
        <div class="space-x-2">
          <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanPreviousPage()"
            @click="table.previousPage()"
          >
            Previous
          </Button>
          <Button
            variant="outline"
            size="sm"
            :disabled="!table.getCanNextPage()"
            @click="table.nextPage()"
          >
            Next
          </Button>
        </div>
      </div>
      <div class="flex gap-4 mt-6">
      
        <div class="w-1/2">
          <h2 class="text-xl font-semibold mb-2">Matriks Data Alternatif (Terstruktur)</h2>
          <div class="overflow-auto rounded-md border">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-gray-100 text-left">
                  <th class="p-2 border">Nama Siswa</th>
                  <th
                    v-for="k in props.kriteria"
                    :key="k.id"
                    class="p-2 border"
                  >
                    {{ k.nama_kriteria }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="s in props.siswa"
                  :key="s.id"
                  class="hover:bg-gray-50"
                >
                  <td class="p-2 border">{{ s.nama_siswa }}</td>
                  <td
                    v-for="k in props.kriteria"
                    :key="k.id"
                    class="p-2 border text-center"
                  >
                    {{ jawabanMatriks[s.id]?.[k.id] ?? '-' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="w-1/2">
          <h2 class="text-xl font-semibold mb-2">Matriks Data Alternatif (Tidak Terstruktur)</h2>
          <div class="overflow-auto rounded-md border">
            <table class="min-w-full text-sm">
              <thead>
                <tr class="bg-gray-100 text-left">
                  <th class="p-2 border">Nama Siswa</th>
                  <th
                    v-for="k in props.kriteria"
                    :key="k.id"
                    class="p-2 border"
                  >
                    {{ k.nama_kriteria }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="s in props.siswa"
                  :key="s.id"
                  class="hover:bg-gray-50"
                >
                  <td class="p-2 border">{{ s.nama_siswa }}</td>
                  <td
                    v-for="k in props.kriteria"
                    :key="k.id"
                    class="p-2 border text-center"
                  >
                    {{ getLabelFromDropdown(k.id, jawabanMatriks[s.id]?.[k.id] ?? '-') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="my-6">
        <h2 class="text-lg font-bold mb-2">Nilai Maksimum dan Minimum per Kriteria</h2>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Kriteria</TableHead>
              <TableHead>Nilai Maksimum</TableHead>
              <TableHead>Nilai Minimum</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="(item, idx) in maxMinJawabanPerKriteria" :key="idx">
              <TableCell>{{ item.nama_kriteria }}</TableCell>
              <TableCell>{{ item.max }}</TableCell>
              <TableCell>{{ item.min }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
      <h2 class="text-xl font-bold mt-6 mb-2">Tabel Nilai Wij</h2>
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Nama Siswa</TableHead>
            <TableHead v-for="k in props.kriteria" :key="k.id">{{ k.nama_kriteria }}</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="s in props.siswa" :key="s.id">
            <TableCell>{{ s.nama_siswa }}</TableCell>
            <TableCell v-for="k in props.kriteria" :key="k.id">
              {{ wijMatrix[s.id]?.[k.id] ?? '-' }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      <h2 class="text-lg font-semibold mb-2">Tabel Nilai Si dan Ri</h2>
      <Table class="mt-4">
        <TableHeader>
          <TableRow>
            <TableHead>Nama Siswa</TableHead>
            <TableHead>Si</TableHead>
            <TableHead>Ri</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="(hasil, siswaId) in hasilSiRi" :key="siswaId">
            <TableCell>{{ hasil.nama }}</TableCell>
            <TableCell>{{ hasil.Si.toFixed(4) }}</TableCell>
            <TableCell>{{ hasil.Ri.toFixed(4) }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <!-- Keterangan Label -->
      <div class="mt-4 text-sm text-muted-foreground">
        <p><strong>Keterangan:</strong></p>
        <ul class="list-disc list-inside">
          <li><strong>Si</strong>: jumlah total dari nilai tertimbang per siswa</li>
          <li><strong>Ri</strong>: nilai maksimum dari bobot kriteria tertimbang per siswa</li>
        </ul>
      </div>
      <h2 class="text-lg font-semibold mb-4">Tabel Nilai Q, Ranking, dan Rekomendasi</h2>

      <Table class="mt-4">
        <TableHeader>
          <TableRow>
            <TableHead>Nama Siswa</TableHead>
            <TableHead>Si</TableHead>
            <TableHead>Ri</TableHead>
            <TableHead>Qi</TableHead>
            <TableHead>Ranking</TableHead>
            <TableHead>Rekomendasi</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow
            v-for="(item, index) in hasilQRanking"
            :key="item.nama"
          >
            <TableCell>{{ item.nama }}</TableCell>
            <TableCell>{{ item.Si.toFixed(4) }}</TableCell>
            <TableCell>{{ item.Ri.toFixed(4) }}</TableCell>
            <TableCell>{{ item.Qi.toFixed(4) }}</TableCell>
            <TableCell>{{ index + 1 }}</TableCell>
            <TableCell>
              <Badge v-if="index === 0" variant="success">Direkomendasikan</Badge>
              <span v-else>-</span>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
      <!-- Keterangan Lengkap -->
      <div class="mt-6 text-sm text-muted-foreground space-y-2">
        <h2 class="text-md font-semibold">Keterangan Perhitungan VIKOR</h2>
        <ul class="list-disc list-inside">
          <li><strong>Matriks Data Alternatif (Terstruktur)</strong>: Nilai numerik jawaban siswa terhadap kriteria.</li>
          <li><strong>Matriks Data Alternatif (Tidak Terstruktur)</strong>: Jawaban siswa dalam bentuk deskriptif (label).</li>
          <li><strong>Nilai Maksimum dan Minimum</strong>: Digunakan untuk normalisasi sesuai jenis kriteria (Benefit atau Cost).</li>
          <li><strong>Wij</strong>: Nilai normalisasi tertimbang untuk setiap siswa dan kriteria.</li>
          <li><strong>Si</strong>: Jumlah total nilai Wij per siswa (indikator performa keseluruhan).</li>
          <li><strong>Ri</strong>: Nilai maksimum Wij per siswa (indikator kelemahan tertinggi).</li>
          <li><strong>Qi</strong>: Nilai agregat VIKOR yang mempertimbangkan Si dan Ri (dengan parameter v).</li>
          <li><strong>Ranking</strong>: Urutan siswa dari terbaik ke terburuk berdasarkan nilai Qi.</li>
          <li><strong>Rekomendasi</strong>: Alternatif dengan Qi terkecil direkomendasikan untuk dipilih.</li>
        </ul>
      </div>

    </div>
    <AlertDialog :open="openFormJawaban" @update:open="openFormJawaban = $event">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Isi Jawaban Siswa</AlertDialogTitle>
            <AlertDialogDescription>
              Pilih siswa dan isi jawaban untuk setiap kriteria.
            </AlertDialogDescription>
          </AlertDialogHeader>

          <div class="space-y-4">
            <!-- Pilih siswa -->
            <Select v-model="selectedSiswaNama">
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Pilih siswa" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="s in siswa"
                  :key="s.nama_siswa"
                  :value="String(s.id)"
                >
                  {{ s.nama_siswa }}
                </SelectItem>
              </SelectContent>
            </Select>

            <!-- Loop kriteria -->
            <div v-for="k in props.kriteria" :key="k.id" class="space-y-1">
              <label class="block font-medium">{{ k.nama_kriteria }}</label>

              <!-- Jika tipe form select -->
              <Select
              v-if="k.tipe_form === 'select'"
              v-model="jawaban[k.nama_kriteria]"
            >
              <SelectTrigger class="w-full">
                <SelectValue placeholder="Pilih jawaban" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="option in parseDropdown(k.pilihan_dropdown)"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </SelectItem>

              </SelectContent>
            </Select>

              <!-- Jika tipe form input -->
              <Input
                v-else
                v-model="jawaban[k.nama_kriteria]"
                placeholder="Isi jawaban"
              />
            </div>
          </div>

          <AlertDialogFooter class="mt-4">
            <Button type="button" variant="outline" @click="openFormJawaban = false">Batal</Button>
            <Button type="button" @click="submitJawaban">Simpan Jawaban</Button>
          </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
  </AppLayout>
</template>
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
  const jawabanArray = Object.entries(jawaban).map(([kriteria_id, nilai]) => ({
    kriteria_id: parseInt(kriteria_id),
    nilai: parseFloat(nilai),
  }));

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
const matrix = computed(() => {
  const result = siswa.map((s) => {
    const nilai: Record<string, number> = {}
    props.kriteria.forEach((k) => {
      const field = kriteriaFieldMap[k.nama_kriteria.toLowerCase()]
      nilai[k.nama_kriteria] = parseFloat(s[field]) || 0
    })
    return {
      nama: s.nama_siswa,
      nilai,
    }
  })
  return result
})

const submitForm = async () => {
  try {
    const formData = {
      ...form.value,
      bobot: parseFloat(rawBobot.value),
      tipe: form.value.tipe.toLowerCase(),
      pilihan_dropdown: form.value.tipe_form === 'select' ? form.value.pilihan_dropdown : undefined
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
  <Button @click="() => { initJawaban(); openFormJawaban = true }" class="ml-4">
  Isi Jawaban
</Button>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col space-y-4 p-4">
     
      <div class="flex items-center py-2">
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
      <h2 class="text-xl font-bold mb-4">Matriks Keputusan</h2>
      <div class="rounded-md border">
      
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Nama Siswa</TableHead>
            <TableHead v-for="k in props.kriteria" :key="k.id">
              {{ k.nama_kriteria }}
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="(row, i) in matrix" :key="i">
            <TableCell class="font-medium">{{ row.nama }}</TableCell>
            <TableCell
              v-for="k in props.kriteria"
              :key="k.id"
            >
              {{ row.nilai[k.nama_kriteria] }}
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
    </div>
    <Button @click="() => { initJawaban(); openFormJawaban = true }" variant="secondary" class="ml-2">
      Isi Pertanyaan Kriteria
    </Button>

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
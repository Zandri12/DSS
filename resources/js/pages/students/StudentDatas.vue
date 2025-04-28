<script setup lang="ts">
import type {
  ColumnFiltersState,
  ExpandedState,
  SortingState,
  VisibilityState,
} from '@tanstack/vue-table'
import { cn, valueUpdater } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import StudentFormDialog from '@/components/StudentFormDialog.vue'
import { Plus, Trash } from 'lucide-vue-next'  
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  createColumnHelper,
  FlexRender,
  getCoreRowModel,
  getExpandedRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useVueTable,
} from '@tanstack/vue-table'
import { ChevronDown, ChevronsUpDown } from 'lucide-vue-next'
import { h, ref } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import DropdownAction from './DataTableDemoColumn.vue'  // Add DropdownAction import here
import PlaceholderPattern from '../../components/PlaceholderPattern.vue'
import axios from 'axios'

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Data Siswa', href: '/siswa' },
]

// Dapetin props dari Inertia
const page = usePage()
const siswa = page.props.siswa as Siswa[]

// Tipe data siswa
export interface Siswa {
  id: number
  nama_siswa: string
  nisn: string
  kelas: string
  alamat: string
  penghasilan: number
  prestasi: string
  nilai_rapor: number
  status: string
  tanggungan: number
}

const columnHelper = createColumnHelper<Siswa>()

const columns = [
  columnHelper.display({
    id: 'select',
    header: ({ table }) => h(Checkbox, {
      'modelValue': table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
      'ariaLabel': 'Select all',
    }),
    cell: ({ row }) => h(Checkbox, {
      'modelValue': row.getIsSelected(),
      'onUpdate:modelValue': value => row.toggleSelected(!!value),
      'ariaLabel': 'Select row',
    }),
    enableSorting: false,
    enableHiding: false,
  }),
  columnHelper.accessor('nama_siswa', {
    header: ({ column }) => h(Button, {
      variant: 'ghost',
      onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
    }, () => ['Nama Siswa', h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' })]),
    cell: ({ row }) => h('div', {}, row.getValue('nama_siswa')),
  }),
  columnHelper.accessor('nisn', {
    header: 'NISN',
    cell: ({ row }) => h('div', {}, row.getValue('nisn')),
  }),
  columnHelper.accessor('kelas', {
    header: 'Kelas',
    cell: ({ row }) => h('div', {}, row.getValue('kelas')),
  }),
  columnHelper.accessor('alamat', {
    header: 'Alamat',
    cell: ({ row }) => h('div', {}, row.getValue('alamat')),
  }),
  columnHelper.accessor('penghasilan', {
    header: 'Penghasilan',
    cell: ({ row }) => h('div', { class: 'text-right' },
      new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(row.getValue('penghasilan'))
    ),
  }),
  columnHelper.accessor('nilai_rapor', {
    header: 'Nilai Rapor',
    cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('nilai_rapor')),
  }),
  columnHelper.accessor('status', {
    header: 'Status',
    cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('status')),
  }),
  columnHelper.accessor('tanggungan', {
    header: 'Tanggungan',
    cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('tanggungan')),
  }),
  {
    id: 'actions',
    enableHiding: false,
    cell: ({ row }: { row: { original: Siswa } }) => {
  const siswaData = row.original
  return h(DropdownAction, {
    siswa: siswaData,
    onEdit: openEdit,
    onDelete: async (id: number) => {
      if (confirm('Yakin mau hapus?')) {
        await axios.delete(`/siswa/${id}`)
        reloadPage()
      }
    }
  })
},
  }
]

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})

const table = useVueTable({
  data: siswa,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
  onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
  onExpandedChange: updaterOrValue => valueUpdater(updaterOrValue, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
})



const showDialog = ref(false)
const editingSiswa = ref<Siswa | null>(null)

const openCreate = () => {
  editingSiswa.value = null
  showDialog.value = true
}

const openEdit = (siswa: Siswa) => {
  editingSiswa.value = siswa
  showDialog.value = true
}

const reloadPage = () => {
  window.location.reload()
}


const showConfirmDialog = ref(false)
const selectedIdsToDelete = ref<number[]>([])
  const handleBatchDelete = () => {
  const selectedRowIds = Object.keys(table.getState().rowSelection)

  // Ambil ID siswa berdasarkan selected row
  const selectedIds = selectedRowIds.map(rowId => {
    const row = table.getRowModel().rows.find(r => r.id === rowId)
    return row?.original.id
  }).filter(id => id !== undefined)

  if (selectedIds.length === 0) {
    alert('Tidak ada data yang dipilih.')
    return
  }

  selectedIdsToDelete.value = selectedIds
  showConfirmDialog.value = true
}
const confirmBatchDelete = async () => {
  try {
    await axios.delete('/siswa-batch', {
      data: { ids: selectedIdsToDelete.value }
    })
    alert('Data berhasil dihapus!')
    reloadPage()
  } catch (error) {
    console.error(error)
    alert('Gagal menghapus data.')
  }

  showConfirmDialog.value = false  // Menutup modal setelah aksi selesai
}

</script>

<template>
  <Head title="Data Siswa" />

  <StudentFormDialog
    v-model="showDialog"
    :siswaData="editingSiswa"
    @saved="reloadPage"
  />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="w-full">
        <div class="flex gap-2 items-center py-4">
          <div v-if="showConfirmDialog" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
              <h3 class="text-xl font-bold mb-4">Konfirmasi Penghapusan</h3>
              <p>Anda yakin ingin menghapus data yang terpilih?</p>
              <div class="flex justify-end gap-2 mt-4">
                <Button variant="outline" @click="showConfirmDialog = false">Batal</Button>
                <Button variant="destructive" @click="confirmBatchDelete">Hapus</Button>
              </div>
            </div>
          </div>
          <Button @click="openCreate" class="ml-2 flex items-center gap-2">
            <Plus class="w-4 h-4 text-white" />
            Tambah Data
          </Button>
          <Button
            variant="destructive"
            :disabled="!Object.keys(table.getState().rowSelection).length"
            @click="handleBatchDelete"
          >
            <Trash class="w-4 h-4 text-white" />
            Hapus Terpilih
          </Button>
          <Input
            class="max-w-sm"
            placeholder="Filter Nama Siswa..."
            :model-value="table.getColumn('nama_siswa')?.getFilterValue() as string"
            @update:model-value="table.getColumn('nama_siswa')?.setFilterValue($event)"
          />
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline" class="ml-auto">
                Columns <ChevronDown class="ml-2 h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuCheckboxItem
                v-for="column in table.getAllColumns().filter(column => column.getCanHide())"
                :key="column.id"
                class="capitalize"
                :model-value="column.getIsVisible()"
                @update:model-value="value => column.toggleVisibility(!!value)"
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
                <TableHead
                  v-for="header in headerGroup.headers"
                  :key="header.id"
                  :data-pinned="header.column.getIsPinned()"
                  :class="cn(
                    { 'sticky bg-background/95': header.column.getIsPinned() },
                    header.column.getIsPinned() === 'left' ? 'left-0' : 'right-0',
                  )"
                >
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
                    <TableCell
                      v-for="cell in row.getVisibleCells()"
                      :key="cell.id"
                      :data-pinned="cell.column.getIsPinned()"
                      :class="cn(
                        { 'sticky bg-background/95': cell.column.getIsPinned() },
                        cell.column.getIsPinned() === 'left' ? 'left-0' : 'right-0',
                      )"
                    >
                      <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                    </TableCell>
                  </TableRow>
                  <TableRow v-if="row.getIsExpanded()">
                    <TableCell :colspan="row.getAllCells().length">
                      {{ JSON.stringify(row.original) }}
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

        <div class="flex items-center justify-end space-x-2 py-4">
          <div class="flex-1 text-sm text-muted-foreground">
            {{ table.getFilteredSelectedRowModel().rows.length }} of
            {{ table.getFilteredRowModel().rows.length }} row(s) selected.
          </div>
          <div class="space-x-2">
            <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
              Previous
            </Button>
            <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
              Next
            </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
<style scoped>
/* Modal styles */
.fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}
.bg-opacity-50 {
  background-color: rgba(0, 0, 0, 0.5);
}
.z-50 {
  z-index: 50;
}
</style>


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
import { Input } from '@/components/ui/input'
import StudentFormDialog from '@/components/StudentFormDialog.vue'
import { Plus, Trash, ChevronDown, ChevronsUpDown } from 'lucide-vue-next'
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
import { h, ref, watch } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import DropdownAction from './DataTableDemoColumn.vue'
import Swal from 'sweetalert2'
import axios from 'axios'

// Breadcrumb
const breadcrumbs = [{ title: 'Data Siswa', href: '/siswa' }]
const page = usePage()
const siswa = page.props.siswa as Siswa[]

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
      modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
      'onUpdate:modelValue': value => table.toggleAllPageRowsSelected(!!value),
    }),
    cell: ({ row }) => h(Checkbox, {
      modelValue: row.getIsSelected(),
      'onUpdate:modelValue': value => row.toggleSelected(!!value),
    }),
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
    cell: info => h('div', {}, info.getValue()),
  }),
  columnHelper.accessor('kelas', {
    header: 'Kelas',
    cell: info => h('div', {}, info.getValue()),
  }),
  columnHelper.accessor('alamat', {
    header: 'Alamat',
    cell: info => h('div', {}, info.getValue()),
  }),
  columnHelper.accessor('penghasilan', {
    header: 'Penghasilan',
    cell: info => h('div', { class: 'text-right' },
      new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(info.getValue())
    ),
  }),
  columnHelper.accessor('nilai_rapor', {
    header: 'Nilai Rapor',
    cell: info => h('div', { class: 'text-center' }, info.getValue()),
  }),
  columnHelper.accessor('status', {
    header: 'Status',
    cell: info => h('div', { class: 'capitalize' }, info.getValue()),
  }),
  columnHelper.accessor('tanggungan', {
    header: 'Tanggungan',
    cell: info => h('div', { class: 'text-center' }, info.getValue()),
  }),
  {
  id: 'actions',
  enableHiding: false,
  cell: ({ row }: { row: { original: Siswa } }) => {
    const handleDelete = async (id: number) => {
      if (confirm('Yakin mau hapus?')) {
        await axios.delete(`/siswa/${id}`)
        reloadPage()
      }
    }

    return h(DropdownAction, {
      siswa: row.original,
      onEdit: openEdit,
      onDelete: handleDelete,
    })
  },
},
]

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})
const expanded = ref<ExpandedState>({})
const pageSize = ref(10) // default page size

const table = useVueTable({
  data: siswa,
  columns,
  getCoreRowModel: getCoreRowModel(),
  getExpandedRowModel: getExpandedRowModel(),
  getFilteredRowModel: getFilteredRowModel(),
  getSortedRowModel: getSortedRowModel(),
  getPaginationRowModel: getPaginationRowModel(),
  onSortingChange: updater => valueUpdater(updater, sorting),
  onColumnFiltersChange: updater => valueUpdater(updater, columnFilters),
  onColumnVisibilityChange: updater => valueUpdater(updater, columnVisibility),
  onRowSelectionChange: updater => valueUpdater(updater, rowSelection),
  onExpandedChange: updater => valueUpdater(updater, expanded),
  state: {
    get sorting() { return sorting.value },
    get columnFilters() { return columnFilters.value },
    get columnVisibility() { return columnVisibility.value },
    get rowSelection() { return rowSelection.value },
    get expanded() { return expanded.value },
  },
})

table.setPageSize(pageSize.value)
watch(pageSize, newSize => table.setPageSize(newSize))

const showDialog = ref(false)
const editingSiswa = ref<Siswa | null>(null)

const openCreate = () => {
  editingSiswa.value = null
  showDialog.value = true
}

const openEdit = (s: Siswa) => {
  editingSiswa.value = s
  showDialog.value = true
}

const reloadPage = () => {
  window.location.reload()
}

const showConfirmDialog = ref(false)
const selectedIdsToDelete = ref<number[]>([])

const handleBatchDelete = () => {
  const selectedIds = Object.keys(table.getState().rowSelection).map(id => {
    return table.getRowModel().rows.find(r => r.id === id)?.original.id
  }).filter(Boolean) as number[]

  if (!selectedIds.length) return alert('Tidak ada data yang dipilih.')

  selectedIdsToDelete.value = selectedIds
  showConfirmDialog.value = true
}

const confirmBatchDelete = async () => {
  try {
    await axios.delete('/siswa-batch', {
      data: { ids: selectedIdsToDelete.value }
    })
    Swal.fire('Sukses', 'Data berhasil dihapus', 'info')
    reloadPage()
  } catch (err) {
    console.error(err)
    Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus', 'error')
  }
  showConfirmDialog.value = false
}

</script>

<template>
  <Head title="Data Siswa" />
  <StudentFormDialog v-model="showDialog" :siswaData="editingSiswa" @saved="reloadPage" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 rounded-xl flex flex-col gap-4">
      <div class="flex items-center gap-2 py-4">
        <Button @click="openCreate" class="flex items-center gap-2">
          <Plus class="w-4 h-4" /> Tambah
        </Button>
        <Button
          variant="destructive"
          :disabled="!Object.keys(table.getState().rowSelection).length"
          @click="handleBatchDelete"
        >
          <Trash class="w-4 h-4" /> Hapus
        </Button>
        <Input
          class="max-w-sm"
          placeholder="Cari Nama Siswa..."
          :model-value="table.getColumn('nama_siswa')?.getFilterValue() as string | number | undefined"
          @update:model-value="val => table.getColumn('nama_siswa')?.setFilterValue(val)"
        />
        <DropdownMenu>
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="ml-auto">Columns <ChevronDown class="ml-2 h-4 w-4" /></Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end">
            <DropdownMenuCheckboxItem
              v-for="column in table.getAllColumns().filter(c => c.getCanHide())"
              :key="column.id"
              :model-value="column.getIsVisible()"
              @update:model-value="val => column.toggleVisibility(!!val)"
            >
              {{ column.id }}
            </DropdownMenuCheckboxItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>

      <div class="border rounded-md">
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
            <template v-if="table.getRowModel().rows.length">
              <TableRow v-for="row in table.getRowModel().rows" :key="row.id" :data-state="row.getIsSelected() && 'selected'">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
              </TableRow>
            </template>
            <TableRow v-else>
              <TableCell :colspan="columns.length" class="text-center h-24">Tidak ada hasil.</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <div class="flex items-center justify-between py-4">
        <div class="text-sm text-muted-foreground">
          {{ table.getFilteredSelectedRowModel().rows.length }} dari {{ table.getFilteredRowModel().rows.length }} dipilih
        </div>
        <div class="flex items-center gap-2">
          <Button size="sm" variant="outline" :disabled="!table.getCanPreviousPage()" @click="table.setPageIndex(0)">
            « First
          </Button>
          <Button size="sm" variant="outline" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
            ‹ Prev
          </Button>
          <span class="text-sm">
            Halaman {{ table.getState().pagination.pageIndex + 1 }} dari {{ table.getPageCount() }}
          </span>
          <Button size="sm" variant="outline" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
            Next ›
          </Button>
          <Button size="sm" variant="outline" :disabled="!table.getCanNextPage()" @click="table.setPageIndex(table.getPageCount() - 1)">
            Last »
          </Button>
          <select v-model.number="pageSize" class="ml-2 text-sm border rounded px-2 py-1">
            <option :value="5">5</option>
            <option :value="10">10</option>
            <option :value="20">20</option>
            <option :value="50">50</option>
          </select>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

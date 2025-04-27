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
    cell: ({ row }) => {
      const siswaData = row.original
      return h(DropdownAction, {
        siswa: siswaData,
        onExpand: row.toggleExpanded,
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
</script>

<template>
  <Head title="Data Siswa" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="w-full">
        <div class="flex gap-2 items-center py-4">
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

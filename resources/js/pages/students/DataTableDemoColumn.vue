<script setup>
import { MoreHorizontal } from 'lucide-vue-next'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'

const props = defineProps({
  siswa: Object,    // sebelumnya payment, ubah ke siswa
  onEdit: Function,
  onDelete: Function,
})

const edit = () => {
  props.onEdit(props.siswa)
}

const del = () => {
  if (confirm('Yakin mau hapus data ini?')) {
    props.onDelete(props.siswa.id)
  }
}



</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="w-8 h-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="w-4 h-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Actions</DropdownMenuLabel>
            <DropdownMenuItem @click="edit">
  Edit
</DropdownMenuItem>
<DropdownMenuItem @click="del">
  Delete
</DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
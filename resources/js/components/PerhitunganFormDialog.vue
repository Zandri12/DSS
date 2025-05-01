<script setup lang="ts">
import Swal from 'sweetalert2'
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'

import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { ref, watch } from 'vue'
import axios from 'axios'

interface Kriteria {
  id?: number
  nama_kriteria: string
  bobot: number
  tipe: string
}

// Props dari parent
const props = defineProps<{
  modelValue: boolean,
  kriteriaData?: Kriteria | null
}>()

const emit = defineEmits(['update:modelValue', 'saved'])

const form = ref<Kriteria>({
  nama_kriteria: '',
  bobot: 0,
  tipe: 'Benefit', // Default tipe
})

// Watch jika data kriteria ingin di-edit
watch(() => props.kriteriaData, (newData) => {
  if (newData) {
    form.value = { ...newData }
  } else {
    form.value = {
      nama_kriteria: '',
      bobot: 0,
      tipe: 'Benefit',
    }
  }
})

// Submit form
const submit = async () => {
  try {
    if (form.value.id) {
      await axios.put(`/api/kriteria/${form.value.id}`, form.value)
    } else {
      await axios.post('/api/kriteria', form.value)
    }
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: `Data kriteria berhasil ${form.value.id ? 'diperbarui' : 'ditambahkan'}.`,
      timer: 3000,
      showConfirmButton: false,
    })
    emit('saved')
    emit('update:modelValue', false)
  } catch (err) {
    console.error(err)
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: 'Terjadi kesalahan saat menyimpan data kriteria.',
    })
  }
}
</script>

<template>
  <AlertDialog :open="modelValue" @update:open="val => emit('update:modelValue', val)">
    <AlertDialogContent class="max-w-4xl p-6">
      <AlertDialogHeader>
        <AlertDialogTitle class="text-2xl font-bold">
          {{ form.id ? 'Edit Kriteria' : 'Tambah Kriteria' }}
        </AlertDialogTitle>
        <AlertDialogDescription class="text-muted-foreground">
          Lengkapi data kriteria untuk perhitungan beasiswa.
        </AlertDialogDescription>
      </AlertDialogHeader>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Nama Kriteria</label>
          <Input v-model="form.nama_kriteria" placeholder="Masukkan nama kriteria" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Bobot</label>
          <Input v-model.number="form.bobot" placeholder="Masukkan bobot" type="number" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Tipe</label>
          <Select v-model="form.tipe">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Pilih Tipe" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>Pilih Tipe</SelectLabel>
                <SelectItem value="Benefit">Benefit</SelectItem>
                <SelectItem value="Cost">Cost</SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>
      </div>

      <AlertDialogFooter class="mt-8">
        <AlertDialogCancel class="hover:bg-muted">Batal</AlertDialogCancel>
        <Button @click="submit" class="font-semibold">
          Simpan
        </Button>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

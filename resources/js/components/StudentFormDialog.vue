<script setup lang="ts">
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { ref, watch } from 'vue'
import axios from 'axios'

interface Siswa {
  id?: number
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

// Props dari parent
const props = defineProps<{
  modelValue: boolean,
  siswaData?: Siswa | null
}>()

const emit = defineEmits(['update:modelValue', 'saved'])

const form = ref<Siswa>({
  nama_siswa: '',
  nisn: '',
  kelas: '',
  alamat: '',
  penghasilan: 0,
  prestasi: '',
  nilai_rapor: 0,
  status: '',
  tanggungan: 0,
})

// Watch kalau mau edit siswa
watch(() => props.siswaData, (newData) => {
  if (newData) {
    form.value = { ...newData }
  } else {
    form.value = {
      nama_siswa: '',
      nisn: '',
      kelas: '',
      alamat: '',
      penghasilan: 0,
      prestasi: '',
      nilai_rapor: 0,
      status: '',
      tanggungan: 0,
    }
  }
})

// Submit form
const submit = async () => {
  try {
    if (form.value.id) {
      await axios.put(`/siswa/${form.value.id}`, form.value)
    } else {
      await axios.post('/siswa', form.value)
    }
    emit('saved')
    emit('update:modelValue', false)
  } catch (err) {
    console.error(err)
  }
}
</script>

<template>
  <AlertDialog :open="modelValue" @update:open="val => emit('update:modelValue', val)">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{ form.id ? 'Edit Data Siswa' : 'Tambah Data Siswa' }}</AlertDialogTitle>
        <AlertDialogDescription>
          Lengkapi data berikut
        </AlertDialogDescription>
      </AlertDialogHeader>

      <div class="space-y-2">
        <Input v-model="form.nama_siswa" placeholder="Nama Siswa" />
        <Input v-model="form.nisn" placeholder="NISN" />
        <Input v-model="form.kelas" placeholder="Kelas" />
        <Input v-model="form.alamat" placeholder="Alamat" />
        <Input v-model.number="form.penghasilan" placeholder="Penghasilan" type="number" />
        <Input v-model="form.prestasi" placeholder="Prestasi" />
        <Input v-model.number="form.nilai_rapor" placeholder="Nilai Rapor" type="number" />
        <Input v-model="form.status" placeholder="Status" />
        <Input v-model.number="form.tanggungan" placeholder="Tanggungan" type="number" />
      </div>

      <AlertDialogFooter class="mt-4">
        <AlertDialogCancel>Batal</AlertDialogCancel>
        <Button @click="submit">
          Simpan
        </Button>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>

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
      Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      text: `Data siswa berhasil ${form.value.id ? 'diperbarui' : 'ditambahkan'}.`,
      timer: 2000,
      showConfirmButton: false,
    })
    emit('saved')
    emit('update:modelValue', false)
  } catch (err) {
    console.error(err)
  }
}
</script>

<template>
  <AlertDialog :open="modelValue" @update:open="val => emit('update:modelValue', val)">
    <AlertDialogContent class="max-w-4xl p-6">
      <AlertDialogHeader>
        <AlertDialogTitle class="text-2xl font-bold">
          {{ form.id ? 'Edit Data Siswa' : 'Tambah Data Siswa' }}
        </AlertDialogTitle>
        <AlertDialogDescription class="text-muted-foreground">
          Lengkapi data siswa dengan informasi yang akurat.
        </AlertDialogDescription>
      </AlertDialogHeader>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Nama Siswa</label>
          <Input v-model="form.nama_siswa" placeholder="Masukkan nama siswa" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">NISN</label>
          <Input v-model="form.nisn" placeholder="Masukkan NISN" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Kelas</label>
          <Select v-model="form.kelas">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Pilih Kelas" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>Pilih Kelas</SelectLabel>
                <SelectItem value="7">7</SelectItem>
                <SelectItem value="8">8</SelectItem>
                <SelectItem value="9">9</SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Status</label>
          <Select v-model="form.status">
            <SelectTrigger class="w-full">
              <SelectValue placeholder="Pilih Status" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>Status</SelectLabel>
                <SelectItem value="Diterima">Diterima</SelectItem>
                <SelectItem value="Tidak Diterima">Tidak Diterima</SelectItem>
                <SelectItem value="Diproses">Diproses</SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>

        <div class="flex flex-col gap-2 md:col-span-2">
          <label class="text-sm text-muted-foreground font-medium">Alamat</label>
          <textarea
            v-model="form.alamat"
            placeholder="Tulis alamat lengkap"
            class="w-full rounded-md border p-3 min-h-[120px] resize-none"
          />
          <p class="text-xs text-muted-foreground italic mt-1">
            Masukkan alamat domisili siswa secara lengkap.
          </p>
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Penghasilan</label>
          <Input v-model.number="form.penghasilan" placeholder="Penghasilan orang tua" type="number" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Prestasi</label>
          <Input v-model="form.prestasi" placeholder="Contoh: Juara 1 Lomba Matematika" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Nilai Rapor</label>
          <Input v-model.number="form.nilai_rapor" placeholder="Rata-rata nilai rapor" type="number" />
        </div>

        <div class="flex flex-col gap-2">
          <label class="text-sm text-muted-foreground font-medium">Tanggungan</label>
          <Input v-model.number="form.tanggungan" placeholder="Jumlah tanggungan orang tua" type="number" />
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


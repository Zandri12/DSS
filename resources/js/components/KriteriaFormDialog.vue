<template>
  <div v-if="visible" class="modal">
    <div class="modal-content">
      <span class="close" @click="close">&times;</span>
      <h2>{{ isEditing ? 'Edit Kriteria' : 'Tambah Kriteria' }}</h2>
      <form @submit.prevent="submitForm">
        <div>
          <label for="nama_kriteria">Nama Kriteria:</label>
          <input type="text" v-model="form.nama_kriteria" required />
        </div>
        <div>
          <label for="bobot">Bobot:</label>
          <input type="number" v-model="form.bobot" required />
        </div>
        <div>
          <label for="tipe">Tipe:</label>
          <select v-model="form.tipe" required>
            <option value="benefit">Benefit</option>
            <option value="cost">Cost</option>
          </select>
        </div>
        <button type="submit">{{ isEditing ? 'Update' : 'Tambah' }}</button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits, watch } from 'vue'

interface Kriteria {
  nama_kriteria: string
  bobot: number
  tipe: string
}

const props = defineProps<{
  visible: boolean,
  kriteriaData: Kriteria | null
}>()

const emit = defineEmits(['saved', 'close'])

const form = ref<Kriteria>({
  nama_kriteria: '',
  bobot: 0,
  tipe: 'benefit'
})

const isEditing = ref(false)

const close = () => {
  emit('close')
}

const submitForm = () => {
  // Simpan data ke server atau lakukan aksi lain
  emit('saved', form.value)
  close()
}

// Watch for changes in kriteriaData to update the form
watch(() => props.kriteriaData, (newData) => {
  if (newData) {
    form.value = { ...newData }
    isEditing.value = true
  } else {
    form.value = {
      nama_kriteria: '',
      bobot: 0,
      tipe: 'benefit'
    }
    isEditing.value = false
  }
})
</script>

<style scoped>
.modal {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 5px;
  width: 400px;
}

.close {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}
</style>
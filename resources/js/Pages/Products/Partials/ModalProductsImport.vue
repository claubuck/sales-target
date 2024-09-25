<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(["close"]);

const form = useForm({
  file: null,
});

// Función para manejar el cierre del modal
const closeModal = () => {
  emit("close");
  form.reset();
};

// Función para manejar el cambio de archivo
const handleFileChange = (event) => {
  const fileInput = event.target;
  form.file = fileInput.files[0];
};

// Función para manejar la importación del archivo
const importFile = () => {
  if (!form.file) {
    alert("Por favor, selecciona un archivo primero.");
    return;
  }

  const formData = new FormData();
  formData.append("file", form.file);

  form.post("/products/import", formData, {
    onFinish: () => {
      closeModal();
    },
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};


</script>


<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Importar listado de productos
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Se debe seleccionar un archivo CSV con el listado de productos a
          importar. Esto actualizará la lista de productos en el sistema.
        </p>

        <form @submit.prevent="importFile" enctype="multipart/form-data" class="mt-6">
          <div>
            <InputLabel for="file" value="Seleccionar archivo CSV" />
            <input
              type="file"
              id="file"
              name="file"
              @change="handleFileChange"
              class="mt-1 block w-full"
              accept=".csv, .xlsx, .xls"
            />
            <InputError :message="form.errors.file" class="mt-2" />
          </div>

          <div class="mt-6 flex justify-end">
            <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>

            <PrimaryButton
              class="ms-3"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              type="submit"
            >
              Importar
            </PrimaryButton>
          </div>
        </form>

      </div>
    </Modal>
  </section>
</template>
  

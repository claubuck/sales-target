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
import Spinner from "@/Components/Spinner.vue";

const props = defineProps({
  show: Boolean,
  type: String,
});

const emit = defineEmits(["close"]);
const isLoading = ref(false);

const form = useForm({
  file: null,
  month: null,
  year: null,
  type: props.type,
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

  // Determina el nombre de la ruta según el valor de props.type
  let routeName = null;
  if (props.type === "sellout") {
    routeName = "sellout.import";
  } else if (props.type === "sellout_commercial") {
    routeName = "sellout.import.commercial";
  } else {
    alert("Tipo de importación no válido.");
    return;
  }

  form.post(route(routeName), {
    preserveScroll: true,
    onStart: () => {
      isLoading.value = true;
    },
    onFinish: () => {
      isLoading.value = false;
      closeModal();
    },
    onError: () => {
      alert("Ocurrió un error al importar el archivo.");
    },

    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};

const months = [
  "Enero",
  "Febrero",
  "Marzo",
  "Abril",
  "Mayo",
  "Junio",
  "Julio",
  "Agosto",
  "Septiembre",
  "Octubre",
  "Noviembre",
  "Diciembre",
];

const years = Array.from(
  { length: 10 },
  (_, i) => new Date().getFullYear() - i
); // Últimos 10 años
</script>


<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Importar reporte Sell Out
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Se debe seleccionar el periodo y un archivo CSV con el reporte Sell
          Out. Solo se tomaran los datos que correspondan al periodo
          seleccionado.
        </p>

        <form
          @submit.prevent="importFile"
          enctype="multipart/form-data"
          class="mt-6"
        >
          <div class="mt-4">
            <InputLabel for="month" value="Mes" />
            <select
              id="month"
              v-model="form.month"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
              required
            >
              <option disabled value="">Seleccione un mes</option>
              <option
                v-for="(month, index) in months"
                :key="index"
                :value="index + 1"
              >
                {{ month }}
              </option>
            </select>
            <InputError :message="form.errors.month" class="mt-2" />
          </div>

          <div class="mt-4">
            <InputLabel for="year" value="Año" />
            <select
              id="year"
              v-model="form.year"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
              required
            >
              <option disabled value="">Seleccione un año</option>
              <option v-for="year in years" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
            <InputError :message="form.errors.year" class="mt-2" />
          </div>

          <div class="mt-4">
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
        <!-- Spinner centrado -->
        <Spinner v-if="isLoading" text="Extrayendo información por favor aguarde..." />
      </div>
    </Modal>
  </section>
</template>
  

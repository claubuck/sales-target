<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { nextTick, ref } from "vue";

const confirmingUserDeletion = ref(false);

const props = defineProps({
  show: Boolean,
});

const form = useForm({
  objectiveMonth: "",
  objectiveYear: "",
  comparisonMonth: "",
  comparisonYear: "",
  comparisonMonthSecondary: "",
  comparisonYearSecondary: "",
  period: "",
  compare_period: "",
  compare_period_secondary: "",
});

// Convert month and year to 'Y-m-d H:i:s' format
const formatDateTime = (month, year) => {
  if (!month || !year) return null;
  const monthIndex = months.indexOf(month) + 1;
  const date = new Date(year, monthIndex - 1, 1); // First day of the month
  return date.toISOString(); // Converts to 'Y-m-dTH:i:s.sssZ'
};

const storeObjetive = () => {
  form.period = formatDateTime(form.objectiveMonth, form.objectiveYear);
  form.compare_period = formatDateTime(form.comparisonMonth, form.comparisonYear);
  form.compare_period_secondary = formatDateTime(form.comparisonMonthSecondary, form.comparisonYearSecondary);

  form.post(route("objetives.store"), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: (error) => {
      console.log("Error",error);
    }, // Focus on the first field on error
  });
};

const emit = defineEmits(["close"]);

const closeModal = () => {
  emit("close");

  form.reset();
};

const months = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
      ];
// Generar el rango de años dinámicamente
const currentYear = new Date().getFullYear();
const years = Array.from({ length: 10 }, (_, i) => currentYear - i);


</script>

<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Nuevo objetivo de venta
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Seleccione el mes para el nuevo objetivo de venta e ingrese un
          porcentaje, este seteo sera general para todas las marcas, luego podra
          modificarse de forma individual.
        </p>

        <!-- Periodo de Objetivo -->
        <div class="mt-6">
          <InputLabel
            for="objective-period-month"
            value="Periodo de Objetivo (Mes)"
          />
          <div class="mt-1 flex space-x-4">
            <select
              id="objective-period-month"
              v-model="form.objectiveMonth"
              class="form-select mt-1 block w-1/2"
            >
              <option value="" disabled selected>Seleccione el mes</option>
              <option v-for="month in months" :key="month" :value="month">
                {{ month }}
              </option>
            </select>

            <select
              id="objective-period-year"
              v-model="form.objectiveYear"
              class="form-select mt-1 block w-1/2"
            >
              <option value="" disabled selected>Seleccione el año</option>
              <option v-for="year in years" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
          <!-- error -->
          <InputError :message="form.errors.period" />
        </div>

        <!-- Periodo de Comparación -->
        <div class="mt-6">
          <InputLabel
            for="comparison-period-month"
            value="Periodo de Comparación (Mes)"
          />
          <div class="mt-1 flex space-x-4">
            <select
              id="comparison-period-month"
              v-model="form.comparisonMonth"
              class="form-select mt-1 block w-1/2"
            >
              <option value="" disabled selected>Seleccione el mes</option>
              <option v-for="month in months" :key="month" :value="month">
                {{ month }}
              </option>
            </select>

            <select
              id="comparison-period-year"
              v-model="form.comparisonYear"
              class="form-select mt-1 block w-1/2"
            >
              <option value="" disabled selected>Seleccione el año</option>
              <option v-for="year in years" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
          <!-- error -->
          <InputError :message="form.errors.compare_period" />
        </div>

        <!-- Periodo de Comparación qlik -->
        <div class="mt-6">
          <InputLabel
            for="comparison-period-month"
            value="Periodo de Comparación (Mes)"
          />
          <div class="mt-1 flex space-x-4">
            <select
              id="comparison-period-month"
              v-model="form.comparisonMonthSecondary"
              class="form-select mt-1 block w-1/2"
            >
              <option value="" disabled selected>Seleccione el mes</option>
              <option v-for="month in months" :key="month" :value="month">
                {{ month }}
              </option>
            </select>

            <select
              id="comparison-period-year"
              v-model="form.comparisonYearSecondary"
              class="form-select mt-1 block w-1/2"
            >
              <option value="" disabled selected>Seleccione el año</option>
              <option v-for="year in years" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
          <!-- error -->
          <InputError :message="form.errors.compare_period_secondary" />
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

          <PrimaryButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="storeObjetive"
          >
            Generar
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </section>
</template>

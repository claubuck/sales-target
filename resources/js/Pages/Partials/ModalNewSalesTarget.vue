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
const passwordInput = ref(null);

const props = defineProps({
  show: Boolean,
});

const form = useForm({
  password: "",
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;

  nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
  form.delete(route("profile.destroy"), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
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
          Seleccione el mes para el nuevo objetivo de venta e ingrese un porcentaje,
          este seteo sera general para todas las marcas, luego podra modificarse
          de forma individual.
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
        </div>

        <!-- Porcentaje -->
        <div class="mt-6">
          <InputLabel for="percentage" value="Porcentaje" />
          <TextInput
            id="percentage"
            v-model="form.percentage"
            type="text"
            class="mt-1 block w-full"
            placeholder="Ingrese el porcentaje. Ejemplo: 10"
          />
          <InputError :message="form.errors.percentage" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

          <PrimaryButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="deleteUser"
          >
            Generar
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </section>
</template>

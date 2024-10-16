<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { nextTick, ref, watch } from "vue";
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
  show: Boolean,
  objetive: Object,
  brand: String,
});

const form = useForm({
  brand: props.brand,
  objetive_id: props.objetive.id,
  comparison_period: (dayjs(props.objetive.comparison_period).format('MM-YYYY') === dayjs(props.objetive.compare_period).format('MM-YYYY'))
    ? props.objetive.compare_period
    : props.objetive.compare_period_secondary || "",
});


watch(() => props.brand, (newValue, oldValue) => {
  form.brand = newValue;
});

const savePeriod = () => {
  console.log("form", form),
  form.post(route("set-period"), {
    preserveScroll: true,
    onSuccess: () =>
    {
      form.reset();
      closeModal();
    },
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

const formatDate = (date) => {
  return dayjs(date).format('MMMM YYYY'); 
};

// Lista de columnas con valor y texto
const columns = [
  { value: props.objetive.compare_period, text: props.objetive.compare_period ? formatDate(props.objetive.compare_period) : "Sin periodo" },
  { value: props.objetive.compare_period_secondary, text: props.objetive.compare_period_secondary ? formatDate(props.objetive.compare_period_secondary) : "Sin periodo" },
];

</script>

<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Periodo de comparación
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Sobre el periodo de comparación se calculara el porcentaje
        </p>
        <div class="mt-4 pb-6">
          <InputLabel for="selectedColumn" value="Seleccionar un periodo" />
          <select
            id="selectedColumn"
            v-model="form.comparison_period"
            class="mt-1 block w-full border-gray-300 rounded-md"
          >
            <option value="" disabled>Selecciona un perido</option>
            <option v-for="column in columns" :key="column" :value="column.value">{{ column.text }}</option>
          </select>
          <InputError :message="form.errors.comparison_period" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

          <PrimaryButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="savePeriod"
          >
            Guardar
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </section>
</template>

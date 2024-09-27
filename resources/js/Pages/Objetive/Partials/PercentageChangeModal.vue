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
import BrandTable from "./BrandTable.vue";
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
  show: Boolean,
  objetive: Object,
  brand: String,
});

const form = useForm({
  percentage: props.objetive.percentages[0]?.percentage || 0,
  brand: props.brand,
  objetive_id: props.objetive.id,
  scope: "",
});

watch(() => props.brand, (newValue, oldValue) => {
  form.brand = newValue;
});

watch(() => props.objetive.percentages[0]?.percentage, (newValue, oldValue) => {
  form.percentage = newValue;
});


const savePercentage = () => {
  console.log("form", form),
  form.post(route("percentage.store"), {
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
  { value: "quantity", text: formatDate(props.objetive.compare_period) },
  { value: "quantity_secondary", text: formatDate(props.objetive.compare_period_secondary) },
];

</script>

<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Porcentaje
        </h2>

        <p class="mt-1 text-sm text-gray-600">
          Editar el porcentaje para {{ brand }}
        </p>

        <div class="mt-4">
          <InputLabel for="selectedColumn" value="Seleccionar un periodo" />
          <select
            id="selectedColumn"
            v-model="form.scope"
            class="mt-1 block w-full border-gray-300 rounded-md"
          >
            <option value="" disabled>Selecciona un perido</option>
            <option v-for="column in columns" :key="column" :value="column.value">{{ column.text }}</option>
          </select>
          <InputError :message="form.errors.scope" class="mt-2" />
        </div>

        <div class="mt-4">
          <InputLabel for="percentage" value="Porcentaje" />
          <TextInput
            id="percentage"
            type="number"
            v-model="form.percentage"
            class="mt-1 block w-full"
            placeholder="Ingresa el porcentaje"
          />
          <InputError :message=form.errors.percentage class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

          <PrimaryButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="savePercentage"
          >
            Guardar
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </section>
</template>

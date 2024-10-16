<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Spinner from "@/Components/Spinner.vue";
import { nextTick, ref, watch } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/es";
dayjs.locale("es");

const props = defineProps({
  show: Boolean,
  objetive: Object,
  brand: String,
});

const isLoading = ref(false);

const form = useForm({
  percentage: props.objetive.percentages[0]?.percentage,
  brand: props.brand,
  objetive_id: props.objetive.id,
});

watch(
  () => props.brand,
  (newValue, oldValue) => {
    form.brand = newValue;
  }
);

watch(
  () => props.objetive.percentages,
  (newValue) => {
    form.percentage = newValue[0]?.percentage || 0;
  },
  { immediate: true }
);

const savePercentage = () => {
  console.log("form", form), (isLoading.value = true);
  form.post(route("percentage.store"), {
    preserveScroll: true,
    onSuccess: () => {
      isLoading.value = false;
      form.reset();
      closeModal();
    },
    onError: (error) => {
      console.log("Error", error);
    }, // Focus on the first field on error
  });
};

const emit = defineEmits(["close"]);

const closeModal = () => {
  emit("close");

  form.reset();
};

const formatDate = (date) => {
  return dayjs(date).format("MMMM YYYY");
};
</script>

<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Porcentaje</h2>

        <p class="mt-1 text-sm text-gray-600">
          Editar el porcentaje para {{ brand }}
        </p>

        <div class="mt-4">
          <InputLabel for="percentage" value="Porcentaje" />
          <TextInput
            id="percentage"
            type="number"
            v-model="form.percentage"
            class="mt-1 block w-full"
            placeholder="Ingresa el porcentaje"
          />
          <InputError :message="form.errors.percentage" class="mt-2" />
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
        
        <!-- Spinner centrado -->
        <Spinner v-if="isLoading" text="Aplicando Porcentajes..." />

      </div>
    </Modal>
  </section>
</template>

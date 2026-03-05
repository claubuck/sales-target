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
import { ref, watch } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/es";
dayjs.locale("es");

const props = defineProps({
  show: Boolean,
  id: Number,
  quantity: Number,
});

const isLoading = ref(false);

const form = useForm({
  quantity: props.quantity || 0,
  sellout_detail_id: props.id,
});

// Sincronizar el formulario con la fila cada vez que se abre el modal o cambian props
watch(
  () => [props.show, props.quantity, props.id],
  ([show, newQuantity, newId]) => {
    if (show && props.id != null) {
      form.quantity = newQuantity ?? 0;
      form.sellout_detail_id = newId;
    }
  },
  { immediate: true }
);

const saveQuantity = () => {
  form.post(route("sellout-detail.edit-quantity"), {
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
  form.clearErrors();
};

</script>

<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Cantidad de unidades</h2>

        <p class="mt-1 text-sm text-gray-600">
          Editar la cantidad
        </p>
        <div class="mt-4">
          <InputLabel for="quantity" value="Cantidad de unidades" />
          <TextInput
            id="quantity"
            type="number"
            v-model="form.quantity"
            class="mt-1 block w-full"
            placeholder="Ingresa la cantidad de unidades"
          />
          <InputError :message="form.errors.quantity" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal"> Cancelar </SecondaryButton>

          <PrimaryButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="saveQuantity"
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

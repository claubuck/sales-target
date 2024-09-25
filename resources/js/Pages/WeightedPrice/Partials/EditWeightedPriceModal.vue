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
  brand: Object,
});

const emit = defineEmits(["close"]);

const form = useForm({
  weighted_price: parseFloat(props.brand?.weighted_price) || null,
});

// Función para manejar el cierre del modal
const closeModal = () => {
  emit("close");
  form.reset();
};

// Función para enviar el formulario
const submit = () => {
  form.put(route("weighted-price.update", props.brand.id), {
    // Usa el nombre de la ruta
    onSuccess: () => {
      closeModal(); // Cierra el modal si la actualización es exitosa
    },
  });
};
</script>


<template>
  <section class="space-y-6">
    <Modal :show="show" @close="closeModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          {{ brand.name }}
        </h2>
        <!-- <p class="mt-1 text-sm text-gray-600">Editar precio ponderado</p> -->
        <div class="mt-4">
          <InputLabel for="weighted_price" value="Precio ponderado" />
          <TextInput
            id="weighted_price"
            v-model="form.weighted_price"
            type="number"
            step="0.01"
            class="mt-1 block w-full"
            required
          />
          <InputError
            v-if="form.errors.weighted_price"
            :message="form.errors.weighted_price"
            class="mt-2"
          />
        </div>

        <div class="mt-6 flex justify-end">
          <SecondaryButton @click="closeModal">Cancelar</SecondaryButton>

          <PrimaryButton
            class="ms-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="submit"
          >
            Guardar
          </PrimaryButton>
        </div>
      </div>
    </Modal>
  </section>
</template>
  

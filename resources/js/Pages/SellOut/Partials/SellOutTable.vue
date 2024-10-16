<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">
          Reportes Sell Out
        </h1>
        <!-- <p class="mt-2 text-sm text-gray-700">A list of all the users in your account including their name, title, email and role.</p> -->
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
          type="button"
          @click="modalSellOutImport = true"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          Subir ultimo reporte
        </button>
      </div>
    </div>
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300">
            <thead>
              <tr>
                <th
                  scope="col"
                  class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                >
                  Periodo
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Fecha de carga
                </th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="sellout in sellouts" :key="sellout.id">
                <td
                  class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >
                  {{ formatMonthDate(sellout.period).toLocaleUpperCase() }}
                  {{ formatYear(sellout.period) }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ formatDate(sellout.created_at) }}
                </td>
                <td
                  class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                >
                  <button
                    @click="openDeleteModal(sellout.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    <TrashIcon class="h-5 w-5" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <ModalSellOutImport
    :show="modalSellOutImport"
    @close="modalSellOutImport = false"
    :type="type"
  />
  <ConfirmedModal
    :open="showDeleteModal"
    @close="showDeleteModal = false"
    @confirm="confirmDelete"
  />
</template>
    
    <script setup>
import { onErrorCaptured, ref } from "vue";
import ModalSellOutImport from "./ModalSellOutImport.vue";
import { TrashIcon } from "@heroicons/vue/24/outline";
import ConfirmedModal from "@/Components/ConfirmedModal.vue";
import { router } from '@inertiajs/vue3';
import dayjs from "dayjs";
import "dayjs/locale/es";
dayjs.locale("es");

const props = defineProps({
  sellouts: Array,
  type: String,
});
const modalSellOutImport = ref(false);
const showDeleteModal = ref(false);
const selloutIdToDelete = ref(null);

const formatMonthDate = (date) => {
  return dayjs(date).format("MMMM"); // Formato para obtener el día de la semana
};

const formatYear = (date) => {
  return dayjs(date).format("YYYY"); // Formato para obtener el año
};

const formatDate = (date) => {
  return dayjs(date).format("DD-MM-YYYY"); // Formato para obtener la fecha
};

// Abrir el modal y guardar el ID a eliminar
const openDeleteModal = (id) => {
  selloutIdToDelete.value = id;
  showDeleteModal.value = true;
  
};

const confirmDelete = () => {
  router.delete(route("sellout.destroy", {id : selloutIdToDelete.value}), {
    preserveScroll: true,
    onStart: () => {
      showDeleteModal.value = false;
    },
    onSuccess: () => {
    },
    onError: (error) => {
      console.error(error);
    },
  });
 
};
</script>
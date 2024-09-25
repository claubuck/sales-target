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
                <!-- <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">Edit</span>
                </th> -->
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="sellout in sellouts" :key="sellout.id">
                <td
                  class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0"
                >
                  {{ formatMonthDate(sellout.period).toLocaleUpperCase() }} {{ formatYear(sellout.period) }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ formatDate(sellout.created_at) }}
                </td>
                <td
                  class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                >
                  <!-- <a href="#" class="text-indigo-600 hover:text-indigo-900"
                    >Edit<span class="sr-only">, {{ person.name }}</span></a
                  > -->
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
  />
</template>
    
    <script setup>
import { ref } from "vue";
import ModalSellOutImport from "./ModalSellOutImport.vue";
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
  sellouts: Array,
});
const modalSellOutImport = ref(false);

const formatMonthDate = (date) => {
  return dayjs(date).format('MMMM'); // Formato para obtener el día de la semana
};

const formatYear = (date) => {
  return dayjs(date).format('YYYY'); // Formato para obtener el año
};

const formatDate = (date) => {
  return dayjs(date).format('DD-MM-YYYY'); // Formato para obtener la fecha
};
</script>
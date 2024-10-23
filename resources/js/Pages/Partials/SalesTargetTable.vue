<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">
          Historial de objetivos
        </h1>
        <p class="mt-2 text-sm text-gray-700">Ordenados por fecha.</p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
          type="button"
          @click="modalNewSalesTarget = true"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          Generar nuevo objetivo
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
                  Objetivo
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Status
                </th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">Edit</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <tr v-for="objetive in objetives.data" :key="objetive.id">
                <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                  <div class="flex items-center">
                    <!-- <div class="h-11 w-11 flex-shrink-0">
                      <img
                        class="h-11 w-11 rounded-full"
                        :src="person.image"
                        alt=""
                      />
                    </div> -->
                    <div class="ml-4">
                      <div class="font-medium text-gray-900">
                        {{ formatMonthDate(objetive.period).toUpperCase() }}
                      </div>
                      <div class="mt-1 text-gray-500">
                        {{ formatYear(objetive.period) }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                  <span
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                    >{{ objetive.status }}</span
                  >
                </td>
                <td
                  class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                >
                  <!-- Icono de Ver -->
                  <a
                    :href="route('set-objetive', objetive.id)"
                    class="text-gray-600 hover:text-gray-900"
                    aria-label="Editar"
                  >
                    <EyeIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Editar -->
                  <a
                    :href="route('set-objetive', objetive.id)"
                    class="text-indigo-600 hover:text-indigo-900"
                    aria-label="Editar"
                  >
                    <PencilIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Eliminar -->
                  <button
                    @click="openDeleteModal(objetive.id)"
                    class="text-red-600 hover:text-red-900"
                    aria-label="Eliminar"
                  >
                    <MinusCircleIcon class="w-5 h-5 inline" />
                  </button>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Exportar a Excel -->
                  <a
                    :href="route('soapp.export-borrador', objetive.id)"
                    class="text-green-600 hover:text-green-900"
                    aria-label="Exportar a Excel"
                  >
                    <DocumentChartBarIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Generar con Excel -->
                  <a
                    :href="route('soapp.export', objetive.id)"
                    class="text-blue-600 hover:text-blue-900"
                    aria-label="Generar con Excel"
                  >
                    <CloudArrowDownIcon class="w-5 h-5 inline" />
                    <span class="ml-1">Generar SO APP</span>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- Paginación -->
      <div class="py-2">
        <Pagination
          :currentPage="objetives.current_page"
          :totalPages="objetives.last_page"
          @page-changed="handlePageChange"
        />
      </div>
    </div>
  </div>
  <ModalNewSalesTarget
    :show="modalNewSalesTarget"
    @close="modalNewSalesTarget = false"
  />
  <ConfirmedModal
    :open="showDeleteModal"
    @close="showDeleteModal = false"
    @confirm="confirmDelete"
  />
</template>
  
  <script setup>
import {
  DocumentChartBarIcon,
  MinusCircleIcon,
  PencilIcon,
  CloudArrowDownIcon,
  EyeIcon,
} from "@heroicons/vue/24/solid";
import ModalNewSalesTarget from "./ModalNewSalesTarget.vue";
import Pagination from "@/Components/Pagination.vue";
import ConfirmedModal from "@/Components/ConfirmedModal.vue";
import { ref } from "vue";
import { router } from '@inertiajs/vue3'
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
  objetives: Object,
});

const modalNewSalesTarget = ref(false);
const showDeleteModal = ref(false);
const selloutIdToDelete = ref(null);

// Función para manejar el cambio de página desde el componente Pagination
const handlePageChange = (page) => {
  fetchObjetives(page);
};

// Función para obtener procedimientos según la página seleccionada
const fetchObjetives = (page) => {
  router.get(route("dashboard", { page }));
};

const formatMonthDate = (date) => {
  return dayjs(date).format('MMMM'); // Formato para obtener el día de la semana
};

const formatYear = (date) => {
  return dayjs(date).format('YYYY'); // Formato para obtener el año
};

// Abrir el modal y guardar el ID a eliminar
const openDeleteModal = (id) => {
  selloutIdToDelete.value = id;
  showDeleteModal.value = true;
  
};

const confirmDelete = () => {
  router.delete(route("objetives.destroy", {id : selloutIdToDelete.value}), {
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
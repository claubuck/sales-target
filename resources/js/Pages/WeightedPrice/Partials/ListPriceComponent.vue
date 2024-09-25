<template>
  <ul role="list" class="divide-y divide-gray-100">
    <li
      v-for="brand in brands"
      :key="brand.id"
      class="flex items-center justify-between gap-x-6 py-5"
    >
      <div class="min-w-0">
        <div class="flex items-start gap-x-3">
          <p class="text-sm font-semibold leading-6 text-gray-900">
            {{ brand.name }}
          </p>
          <p
            :class="[
              statuses[brand.status],
              'mt-0.5 p-1 whitespace-nowrap rounded-md px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset',
            ]"
          >
            {{ brand.status }}
          </p>
        </div>
        <div
          class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-gray-500"
        >
          <p class="whitespace-nowrap">
            Ultima modificación
            <time :datetime="brand.updated_at">{{ formatDate(brand.updated_at) }}</time>
          </p>
          <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current">
            <circle cx="1" cy="1" r="1" />
          </svg>
          <!-- <p class="truncate">Created by {{ project.createdBy }}</p> -->
        </div>
      </div>
      <div class="flex flex-none items-center gap-x-6">
        <p class="mt-1 text-sm text-gray-600">
          ${{
            (
              typeof brand.weighted_price === "number"
                ? brand.weighted_price
                : parseFloat(brand.weighted_price)
            )
              ? parseFloat(brand.weighted_price).toFixed(2)
              : "0.00"
          }}
        </p>
        <button
          @click="openEditModal(brand)"
          class="hidden p-1 rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:block"
        >
          Editar<span class="sr-only">, {{ brand.name }}</span>
        </button>
        <!-- <Menu as="div" class="relative flex-none">
            <MenuButton class="-m-2.5 block p-2.5 text-gray-500 hover:text-gray-900">
              <span class="sr-only">Open options</span>
              <EllipsisVerticalIcon class="h-5 w-5" aria-hidden="true" />
            </MenuButton>
            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
              <MenuItems class="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                <MenuItem v-slot="{ active }">
                  <a href="#" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                    >Edit<span class="sr-only">, {{ project.name }}</span></a
                  >
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <a href="#" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                    >Move<span class="sr-only">, {{ project.name }}</span></a
                  >
                </MenuItem>
                <MenuItem v-slot="{ active }">
                  <a href="#" :class="[active ? 'bg-gray-50' : '', 'block px-3 py-1 text-sm leading-6 text-gray-900']"
                    >Delete<span class="sr-only">, {{ project.name }}</span></a
                  >
                </MenuItem>
              </MenuItems>
            </transition>
          </Menu> -->
      </div>
    </li>
  </ul>
    <EditWeightedPriceModal :show = showEditWeightedPriceModal @close="showEditWeightedPriceModal = false" :brand="selectedBrand" />
</template>
  
  <script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from "@headlessui/vue";
import { EllipsisVerticalIcon } from "@heroicons/vue/20/solid";
import EditWeightedPriceModal from "./EditWeightedPriceModal.vue";
import { ref } from "vue";
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
  brands: Array,
});

const showEditWeightedPriceModal = ref(false);
const selectedBrand = ref(null); // Para almacenar el ID de la marca seleccionada

const statuses = {
  Complete: "text-green-700 bg-green-50 ring-green-600/20",
  "In progress": "text-gray-600 bg-gray-50 ring-gray-500/10",
  Archived: "text-yellow-800 bg-yellow-50 ring-yellow-600/20",
  active: "text-green-700 bg-green-50 ring-green-600/20",
};

// Función para abrir el modal
const openEditModal = (brand) => {
  selectedBrand.value = brand; // Almacena el ID de la marca
  showEditWeightedPriceModal.value = true; // Abre el modal
};


// Función para formatear la fecha
const formatDate = (date) => {
  return dayjs(date).format('D [de] MMMM [de] YYYY');
};
</script>
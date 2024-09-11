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
                  Retailer
                </th>
                <th
                  scope="col"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Marcas
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
              <tr v-for="person in people" :key="person.email">
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
                        {{ person.objetivo }}
                      </div>
                      <div class="mt-1 text-gray-500">{{ person.año }}</div>
                    </div>
                  </div>
                </td>
                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                  <div class="text-gray-900">{{ person.retailer }}</div>
                  <!-- <div class="mt-1 text-gray-500">{{ person.department }}</div> -->
                </td>
                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                  <div class="text-gray-900">{{ person.marcas }}</div>
                  <!-- <div class="mt-1 text-gray-500">{{ person.department }}</div> -->
                </td>
                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                  <span
                    class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
                    >Activo</span
                  >
                </td>
                <td
                  class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0"
                >
                <!-- Icono de Ver -->
                <a
                    :href="route('objetives.index')"
                    class="text-gray-600 hover:text-gray-900"
                    aria-label="Editar"
                  >
                    <EyeIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Editar -->
                  <a
                    href="#"
                    class="text-indigo-600 hover:text-indigo-900"
                    aria-label="Editar"
                  >
                    <PencilIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Eliminar -->
                  <a
                    href="#"
                    class="text-red-600 hover:text-red-900"
                    aria-label="Eliminar"
                  >
                    <MinusCircleIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Exportar a Excel -->
                  <a
                    href="#"
                    class="text-green-600 hover:text-green-900"
                    aria-label="Exportar a Excel"
                  >
                    <DocumentChartBarIcon class="w-5 h-5 inline" />
                  </a>

                  <!-- Espacio entre íconos -->
                  <span class="mx-2"></span>

                  <!-- Icono de Generar con Excel -->
                  <a
                    href="#"
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
    </div>
  </div>
  <ModalNewSalesTarget :show="modalNewSalesTarget" @close="modalNewSalesTarget = false" />
</template>
  
  <script setup>
  import { DocumentChartBarIcon, MinusCircleIcon, PencilIcon, CloudArrowDownIcon, EyeIcon } from '@heroicons/vue/24/solid'
  import ModalNewSalesTarget from './ModalNewSalesTarget.vue';
  import { ref } from 'vue';

  const modalNewSalesTarget = ref(false);
const people = [
  {
    objetivo: "Julio",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  {
    objetivo: "Junio",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  {
    objetivo: "Mayo",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  {
    objetivo: "Abril",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  {
    objetivo: "Marzo",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  {
    objetivo: "Enero",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  {
    objetivo: "Enero",
    año: "2024",
    retailer: "10",
    marcas: "11",
    title: "Front-end Developer",
    department: "Optimization",
    email: "lindsay.walton@example.com",
    role: "Member",
    image:
      "https://images.unsplash.com/photo-1517841905240-472988babdf9?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80",
  },
  // More people...
];
</script>
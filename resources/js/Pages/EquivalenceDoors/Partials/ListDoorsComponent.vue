<template>
    <div class="px-4 sm:px-6 lg:px-8">
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-base font-semibold text-gray-900">Puertas</h1>
          <p class="mt-2 text-sm text-gray-700">
            Listado de equivalencias de puertas de comercial.
          </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
          <Link
            :href="route('doors.create')"
            class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Nueva puerta
          </Link>
        </div>
      </div>
      <div class="mt-8 flow-root">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <table class="min-w-full divide-y divide-gray-300">
              <thead>
                <tr>
                  <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                    Cliente
                  </th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                    Sucursal Comercial
                  </th>
                  <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                    Sucursal Objetivo BA
                  </th>
                  <th>
                    <span class="sr-only">Acciones</span>
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="door in doors" :key="door.id">
                  <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                    {{ door.client }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ door.sucursal }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    {{ door.sucursal_objetivo_ba }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 flex space-x-3">
                    <div class="relative group">
                      <Link
                        :href="route('doors.edit', door.id)"
                        class="text-blue-600 hover:text-blue-900"
                        title="Editar"
                      >
                        <PencilIcon class="w-5 h-5" />
                      </Link>
                      <span class="absolute bottom-full mb-1 hidden w-max bg-gray-800 text-white text-xs rounded py-1 px-2 group-hover:block">
                        Editar
                      </span>
                    </div>
                    <div class="relative group">
                      <button
                        type="button"
                        @click="confirmDelete(door)"
                        class="text-red-600 hover:text-red-900"
                        title="Eliminar"
                      >
                        <TrashIcon class="w-5 h-5" />
                      </button>
                      <span class="absolute bottom-full mb-1 hidden w-max bg-gray-800 text-white text-xs rounded py-1 px-2 group-hover:block">
                        Eliminar
                      </span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { Link, router } from "@inertiajs/vue3";
  import { PencilIcon, TrashIcon } from "@heroicons/vue/24/solid";
  
  defineProps({
    doors: Object,
  });

  function confirmDelete(door) {
    if (confirm(`¿Eliminar la puerta "${door.sucursal_objetivo_ba}"?`)) {
      router.delete(route("doors.destroy", door.id), {
        preserveScroll: true,
      });
    }
  }
  </script>
  
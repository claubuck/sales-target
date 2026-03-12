<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold text-gray-900">Clientes</h1>
        <p class="mt-2 text-sm text-gray-700">
          Equivalencias entre nombre en comercial y nombre a mostrar en la plataforma.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <Link
          :href="route('clients.create')"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
        >
          Nuevo cliente
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
                  Nombre a mostrar
                </th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                  Nombre en comercial
                </th>
                <th>
                  <span class="sr-only">Acciones</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="item in clientEquivalences" :key="item.id">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                  {{ item.cliente_display }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ item.cliente_comercial }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 flex space-x-3">
                  <Link
                    :href="route('clients.edit', item.id)"
                    class="text-blue-600 hover:text-blue-900"
                    title="Editar"
                  >
                    <PencilIcon class="w-5 h-5" />
                  </Link>
                  <button
                    type="button"
                    @click="confirmDelete(item)"
                    class="text-red-600 hover:text-red-900"
                    title="Eliminar"
                  >
                    <TrashIcon class="w-5 h-5" />
                  </button>
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

const props = defineProps({
  clientEquivalences: Object,
});

function confirmDelete(item) {
  if (confirm(`¿Eliminar la equivalencia "${item.cliente_comercial}" → "${item.cliente_display}"?`)) {
    router.delete(route("clients.destroy", item.id), {
      preserveScroll: true,
    });
  }
}
</script>

<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">
          Objetivos de Ventas por Marca
        </h1>
        <p class="mt-2 text-sm text-gray-700">
          Listado de objetivos de ventas agrupados por cliente, mostrando la
          cantidad y el total por punto de venta.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex space-x-4">
        <a
          type="button"
          :href="route('soapp.export-borrador', objetive.id)"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          Exportar borrador
        </a>
        <a
          type="button"
          :href="route('soapp.export', objetive.id)"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          Exportar SO APP
        </a>
      </div>
    </div>
    <div class="mt-8 flow-root">
      <div
        class="-mx-4 -my-2 overflow-x-auto overflow-y-auto max-h-[700px] sm:-mx-6 lg:-mx-8"
      >
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="sticky top-0 bg-white z-20">
              <tr>
                <th
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky left-0 top-0 bg-white z-10"
                >
                  CLIENTE
                </th>
                <th
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 sticky left-[90px] bg-white z-10"
                >
                  PVD
                </th>
                <th
                  v-for="brand in brands"
                  :key="brand.name + '-header'"
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                  :style="{
                    backgroundColor: brand.axis ? colorMap[brand.axis] : '',
                    border: '1px solid white',
                  }"
                >
                  <!-- Nombre de la marca centrado y con altura específica -->
                  <div
                    class="flex items-center justify-center text-center font-bold mb-1 h-14"
                  >
                    {{ brand.name }}
                  </div>

                  <!-- Línea divisora -->
                  <hr class="border-gray-300 my-2" />

                  <!-- Porcentaje Configurado -->
                  <div class="text-xxxs text-gray-700">
                    <span class="block font-semibold">Seteado:</span>
                    <span
                      class="flex items-center justify-center text-indigo-600 bg-indigo-100 px-0.5 py-0.25 rounded"
                    >
                      {{ percentageForBrand(brand.name)[0] >= 0 ? "+" : ""}}
                      {{ percentageForBrand(brand.name)[0] }}%
                    </span>
                  </div>

                  <!-- Porcentaje Real -->
                  <div class="text-xxxs text-gray-700 mt-0.5">
                    <span class="block font-semibold">Real:</span>
                    <span
                      class="flex items-center justify-center px-0.5 py-0.25 rounded"
                      :class="{
                        'text-green-600 bg-green-100':
                          percentageForBrand(brand.name)[1] >= 0,
                        'text-red-600 bg-red-100':
                          percentageForBrand(brand.name)[1] < 0,
                      }"
                    >
                      {{ percentageForBrand(brand.name)[1] >= 0 ? "+" : ""
                      }}{{ percentageForBrand(brand.name)[1] }}%
                    </span>
                  </div>
                  <!-- Total por marca -->
                  <div class="text-xxxs text-gray-700 mt-0.5">
                    <span class="block font-semibold">Total:</span>
                    <span
                      class="flex items-center justify-center text-yellow-600 bg-green-100 px-0.5 py-0.25 rounded"
                    >
                      {{ calculateTotalUnitsForBrand(brand.name) }} u
                    </span>
                  </div>
                </th>
                <th
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Total Unidades
                </th>
                <th
                  class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Total Pesos
                </th>
              </tr>
              <tr>
                <th class="sticky left-[0] bg-white z-10"></th>
                <th class="sticky left-[90px] bg-white z-10"></th>
                <template
                  v-for="brand in brands"
                  :key="brand.name + '-subcolumns'"
                >
                  <th
                    :style="{
                      backgroundColor: brand.axis ? colorMap[brand.axis] : '',
                      border: '1px solid white',
                    }"
                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
                  >
                    Unidades
                  </th>
                </template>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <!-- Filas de datos -->
              <tr v-for="(item, index) in groupedData" :key="index">
                <td
                  class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0 sticky left-0 top-0 bg-white z-10"
                >
                  {{ item.client }}
                </td>
                <td
                  class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 sticky left-[90px] bg-white z-10"
                >
                  {{ item.point_of_sale }}
                </td>
                <!-- Recorre cada marca para agregar solo la columna de cantidad -->
                <template
                  v-for="brand in brands"
                  :key="brand.name + '-' + item.client"
                >
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 border-r border-gray-300 text-center">
                    {{ getBrandData(item, brand.name)?.quantity_with_percentage || "-" }}
                  </td>
                </template>
                <td
                  class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900"
                >
                  {{ calculateTotalUnits(item) }}
                </td>
                <td
                  class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900"
                >
                  $
                  {{ calculateTotalPrice(item).toLocaleString() }}
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
import { computed } from "vue";

// Datos de ejemplo
const props = defineProps({
  sellout: Array, // Asumes que los datos vienen como un array
  brands: Array, // Asumes que las marcas vienen como un array
  objetive: Object, // Asumes que el objetivo viene como un objeto
});

// Obtener marcas únicas
const uniqueBrands = computed(() => {
  const brands = props.sellout.map((item) => item.brand);
  return [...new Set(brands)]; // Eliminar marcas duplicadas
});

// Agrupar datos por cliente y punto de venta
const groupedData = computed(() => {
  const grouped = props.sellout.reduce((acc, curr) => {
    const key = `${curr.client}-${curr.point_of_sale}`;
    if (!acc[key]) {
      acc[key] = {
        client: curr.client,
        point_of_sale: curr.point_of_sale,
        brands: [],
      };
    }
    acc[key].brands.push(curr);
    return acc;
  }, {});
  return Object.values(grouped);
});

// Función para obtener los datos de una marca específica para cada cliente/punto de venta
const getBrandData = (item, brand) => {
  return item.brands.find((b) => b.brand === brand);
};

// Mapeo de colores según el eje
const colorMap = {
  "EJE 1": "#58d68d",
  "EJE 2": "#85c1e9",
  "EJE 3": "#a569bd",
};

// Función para calcular el total de unidades por fila
const calculateTotalUnits = (item) => {
  return item.brands.reduce((total, brand) => {
    return total + (brand.quantity || 0);
  }, 0);
};

// Función para calcular el total de pesos por fila
const calculateTotalPrice = (item) => {
  return item.brands.reduce((total, brand) => {
    return total + (parseFloat(brand.price) || 0); // Convertir a número
  }, 0);
};

// Función para obtener el porcentaje de una marca
const percentageForBrand = (brand) => {
  const percentage = props.objetive.percentages.find((p) => p.brand === brand);
  return [percentage?.percentage, percentage?.real_percentage];
};

// Función para calcular el total de unidades de una marca específica en todas las filas
const calculateTotalUnitsForBrand = (brandName) => {
  return props.sellout.reduce((total, item) => {
    return total + (item.brand === brandName ? item.quantity_with_percentage
    || 0 : 0);
  }, 0);
};
</script>

<style>
/* Estilos personalizados */
</style>
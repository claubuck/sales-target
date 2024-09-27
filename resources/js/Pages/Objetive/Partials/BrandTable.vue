<template>
  <div class="px-4 pt-5 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">
          Objetivos de venta
        </h1>
        <div class="flex items-center mt-2">
          <p class="text-sm text-gray-700">Porcentaje seteado {{ objetive.percentages[0]?.percentage ?? 0 }}%</p>
          <button
            @click="showPercentajeModal = true"
            type="button"
            class="ml-2 rounded-md bg-indigo-600 px-3 py-1 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
          >
            Cambiar
          </button>
        </div>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
          type="button"
          class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          Exportar
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
                  class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900"
                >
                  Cadena
                </th>
                <th
                  class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900"
                >
                  PDV
                </th>
                <th
                  class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Unidades {{ formatDate(objetive.compare_period) }}
                </th>
                <th
                  class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Unidades {{ formatDate(objetive.compare_period_secondary) }}
                </th>
                <th
                  class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Unidades {{ formatDate(objetive.period) }}
                </th>
                <th
                  class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Pesos
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <template v-for="[client, group] in Object.entries(groupedData)">
                <template
                  v-for="(selloutdata, index) in group.items"
                  :key="selloutdata.id"
                >
                  <tr>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-900"
                    >
                      {{ selloutdata.client }}
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-900"
                    >
                      {{ selloutdata.point_of_sale }}
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      {{ selloutdata.quantity }}
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      {{ selloutdata.quantity_secondary }}
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      {{ applyPercentage(selloutdata.quantity, selloutdata.quantity_secondary) }}
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      {{ selloutdata.price }}
                    </td>
                  </tr>
                </template>
                <tr class="font-bold">
                  <td colspan="2" class="text-gray-900">
                    Subtotal {{ client }}
                  </td>
                  <td class="text-gray-900">{{ group.totalQuantity }}</td>
                  <td class="text-gray-900">
                    {{ group.totalSecondaryQuantity }}
                  </td>
                  <td class="text-gray-900">
                    ${{ group.totalPrice.toFixed(2) }}
                  </td>
                </tr>
              </template>

              <tr class="font-bold">
                <td colspan="2" class="text-gray-900">Total SO</td>
                <td class="text-gray-900">
                  {{
                    Object.values(groupedData).reduce(
                      (acc, group) => acc + group.totalQuantity,
                      0
                    )
                  }}
                </td>
                <td class="text-gray-900">
                  {{
                    Object.values(groupedData).reduce(
                      (acc, group) => acc + group.totalSecondaryQuantity,
                      0
                    )
                  }}
                </td>
                <td class="text-gray-900">
                  ${{
                    Object.values(groupedData)
                      .reduce((acc, group) => acc + group.totalPrice, 0)
                      .toFixed(2)
                  }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <PercentageChangeModal
    :show="showPercentajeModal"
    @close="showPercentajeModal = false"
    :objetive="objetive"
    :brand="brand"
  />
</template>

  
  <script setup>
import { computed, ref, watch } from "vue";
import PercentageChangeModal from "./PercentageChangeModal.vue";
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
  sellout: Object,
  objetive: Object,
  brand: String,
});

const showPercentajeModal = ref(false);
const percentage = ref(props.objetive.percentages[0]?.percentage);
const activeBrand = ref(props.brand);

// Agrupar datos y calcular subtotales
const groupedData = computed(() => {
  const groups = {};

  props.sellout.forEach((data) => {
    if (!groups[data.client]) {
      groups[data.client] = {
        totalQuantity: 0,
        totalSecondaryQuantity: 0,
        totalPrice: 0,
        items: [],
      };
    }
    groups[data.client].totalQuantity += Number(data.quantity);
    groups[data.client].totalSecondaryQuantity += Number(
      data.quantity_secondary
    );
    // Validar y procesar el precio
    if (data.price) {
      const priceValue = parseFloat(data.price.replace(/[$,]/g, ""));
      if (!isNaN(priceValue)) {
        groups[data.client].totalPrice += priceValue;
      }
    }
    groups[data.client].items.push(data);
  });

  return groups;
});

const formatDate = (date) => {
  return dayjs(date).format('MMMM YYYY'); 
};

//aplicar porcenje

const applyPercentage = (quantity, quantity_secondary) => {
  let percentageValue = props.objetive.percentages[0]?.percentage || 0;
  let column = props.objetive.percentages[0]?.scope;

  if (column === "quantity") {
    return parseFloat((quantity + (quantity * (percentageValue / 100))).toFixed(2));
  } else if (column === "quantity_secondary") {
    return parseFloat((quantity_secondary + (quantity_secondary * (percentageValue / 100))).toFixed(2));
  } else {
    return 0; // O un valor por defecto si no hay coincidencia
  }
};
</script>
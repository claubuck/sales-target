<template>
  <div class="px-4 pt-5 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-base font-semibold leading-6 text-gray-900">
          Objetivos de venta
        </h1>
        <div class="flex items-center mt-2">
          <p class="text-sm text-gray-700">
            Porcentaje seteado {{ objetive.percentages[0]?.percentage ?? 0 }}%
          </p>
          <button
            @click="showPercentajeModal = true"
            type="button"
            class="ml-2 rounded-md bg-indigo-600 px-3 py-1 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
          >
            Cambiar
          </button>

          <!-- Mostrar totales dentro del div -->
          <div class="mt-4 ml-8">
            <p
              v-if="showColumnComparePeriod"
              class="text-sm font-bold text-gray-900"
            >
              Total Unidades de
              {{
                showColumnComparePeriod
                  ? formatDate(objetive.compare_period)
                  : formatDate(objetive.compare_period_secondary)
              }}
              : {{ totalQuantity }}
            </p>
            <p
              v-if="showColumnComparePeriodSecondary"
              class="text-sm font-bold text-gray-900"
            >
              Total Unidades de
              {{
                showColumnComparePeriodSecondary
                  ? formatDate(objetive.compare_period_secondary)
                  : formatDate(objetive.compare_period)
              }}
              : {{ totalQuantity }}
            </p>
            <p class="text-sm font-bold text-gray-900">
              Total Unidades de {{ formatDate(objetive.period) }} :
              {{ totalQuantityPeriod }}
            </p>
            <p class="text-sm font-bold text-gray-900">
              Total Pesos: ${{ Number(totalPrice).toLocaleString() }}
            </p>

            <!-- Variación porcentual -->
            <div class="flex items-center mt-2">
              <p class="text-sm font-bold text-gray-900">
                Variación porcentual: {{ percentageChange.toFixed(2) }}%
              </p>
              <span v-if="percentageChange > 0" class="text-green-600 ml-2"
                >↑</span
              >
              <span v-if="percentageChange < 0" class="text-red-600 ml-2"
                >↓</span
              >
              <span v-if="percentageChange === 0" class="ml-2">→</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300">
            <thead>
              <tr class="font-bold bg-green-200">
                <td colspan="2" class="text-gray-900">Total SO</td>
                <td v-if="showColumnComparePeriod" class="text-gray-900">
                  {{
                    Object.values(groupedData).reduce(
                      (acc, group) => acc + group.totalQuantity,
                      0
                    )
                  }}
                </td>
                <td
                  v-if="showColumnComparePeriodSecondary"
                  class="text-gray-900"
                >
                  {{
                    Object.values(groupedData).reduce(
                      (acc, group) => acc + group.totalSecondaryQuantity,
                      0
                    )
                  }}
                </td>
                <td class="text-gray-900">
                  {{
                    Object.values(groupedData).reduce(
                      (acc, group) => acc + group.totalQuantityWithPercentage,
                      0
                    )
                  }}
                </td>
                <td class="text-gray-900">
                  ${{
                    Object.values(groupedData)
                      .reduce((acc, group) => acc + group.totalPrice, 0)
                      .toLocaleString()
                  }}
                </td>
              </tr>
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
                  v-if="showColumnComparePeriod"
                  class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Unidades {{ formatDate(objetive.compare_period) }}
                  {{
                    objetive.compare_period_sellout_type == "sellout"
                      ? "(ST)"
                      : ""
                  }}
                </th>
                <th
                  v-if="showColumnComparePeriodSecondary"
                  class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                >
                  Unidades {{ formatDate(objetive.compare_period_secondary) }}
                  {{
                    objetive.compare_period_secondary_sellout_type == "sellout"
                      ? "(ST)"
                      : ""
                  }}
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
                <!-- Subtotales -->
                <tr class="font-bold bg-yellow-200">
                  <td colspan="2" class="text-gray-900">
                    Subtotal {{ client }}
                  </td>
                  <td v-if="showColumnComparePeriod" class="text-gray-900">
                    {{ group.totalQuantity }}
                  </td>
                  <td
                    v-if="showColumnComparePeriodSecondary"
                    class="text-gray-900"
                  >
                    {{ group.totalSecondaryQuantity }}
                  </td>
                  <td class="text-gray-900">
                    {{ group.totalQuantityWithPercentage }}
                  </td>
                  <td class="text-gray-900">
                    ${{ group.totalPrice.toLocaleString() }}
                  </td>
                </tr>
                <!-- clientes -->
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
                      v-if="showColumnComparePeriod"
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      {{ selloutdata.quantity }}
                    </td>
                    <td
                      v-if="showColumnComparePeriodSecondary"
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      {{ selloutdata.quantity_secondary }}
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      <a
                        href="#"
                        @click.prevent="
                          openEditQuantityModal(
                            selloutdata.id,
                            selloutdata.quantity_with_percentage,
                            'quantity_secondary'
                          )
                        "
                        class="text-indigo-600 hover:underline"
                      >
                        {{ selloutdata.quantity_with_percentage }}
                      </a>
                    </td>
                    <td
                      class="whitespace-nowrap px-2 py-2 text-sm text-gray-500"
                    >
                      $ {{ Number(selloutdata.price).toLocaleString() }}
                    </td>
                  </tr>
                </template>
                <!-- Espacio antes del subtotal -->
                <tr>
                  <td colspan="6" class="py-8"></td>
                </tr>
              </template>
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

  <EditQuantityModal
    :show="showEditQuantityModal"
    @close="showEditQuantityModal = false"
    :id="selectedSelloutId"
    :quantity="selectedSelloutQuantity"
  />
</template>

  
  <script setup>
import { computed, ref, watch } from "vue";
import PercentageChangeModal from "./PercentageChangeModal.vue";
import EditQuantityModal from "./EditQuantityModal.vue";
import dayjs from "dayjs";
import "dayjs/locale/es";
dayjs.locale("es");

const props = defineProps({
  sellout: Object,
  objetive: Object,
  brand: String,
});

const showPercentajeModal = ref(false);
const percentage = ref(props.objetive.percentages[0]?.percentage);
const activeBrand = ref(props.brand);
const showEditQuantityModal = ref(false);
const selectedSelloutId = ref(null);
const selectedSelloutQuantity = ref(null);

// Agrupar datos y calcular subtotales
const groupedData = computed(() => {
  const groups = {};

  props.sellout.forEach((data) => {
    if (!groups[data.client]) {
      groups[data.client] = {
        totalQuantity: 0,
        totalSecondaryQuantity: 0,
        totalQuantityWithPercentage: 0,
        totalPrice: 0,
        items: [],
      };
    }
    groups[data.client].totalQuantity += Number(data.quantity);
    groups[data.client].totalSecondaryQuantity += Number(
      data.quantity_secondary
    );
    groups[data.client].totalQuantityWithPercentage += Number(
      data.quantity_with_percentage
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
  return dayjs(date).format("MMMM YYYY");
};

// Mostrar columna si los periodos coinciden o si comparison_period es null
const showColumnComparePeriod = computed(() => {
  const comparePeriod = dayjs(props.objetive.compare_period).format("YYYY-MM");
  const comparisonPeriod = props.objetive.comparison_period
    ? dayjs(props.objetive.comparison_period).format("YYYY-MM")
    : null;

  return comparisonPeriod === null || comparePeriod === comparisonPeriod;
});

// Mostrar columna secundaria si los periodos coinciden o si comparison_period es null
const showColumnComparePeriodSecondary = computed(() => {
  const comparePeriodSecondary = dayjs(
    props.objetive.compare_period_secondary
  ).format("YYYY-MM");
  const comparisonPeriod = props.objetive.comparison_period
    ? dayjs(props.objetive.comparison_period).format("YYYY-MM")
    : null;

  return (
    comparisonPeriod === null || comparePeriodSecondary === comparisonPeriod
  );
});

// Cálculo de los totales globales
const totalQuantity = computed(() => {
  return Object.values(groupedData.value).reduce(
    (acc, group) => acc + group.totalQuantity,
    0
  );
});

const totalQuantityPeriod = computed(() => {
  return Object.values(groupedData.value).reduce(
    (acc, group) => acc + group.totalQuantityWithPercentage,
    0
  );
});

const totalPrice = computed(() => {
  return Object.values(groupedData.value).reduce(
    (acc, group) => acc + group.totalPrice,
    0
  );
});

// Inicializa percentageChange
const percentageChange = computed(() => {
  if (totalQuantityPeriod.value === 0) {
    return 0; // Evita dividir por cero
  }
  return (
    ((totalQuantityPeriod.value - totalQuantity.value) / totalQuantity.value) *
    100
  );
});

// Función para abrir el modal de edición de cantidad
const openEditQuantityModal = (id, quantity) => {
  selectedSelloutId.value = id;
  selectedSelloutQuantity.value = quantity;
  showEditQuantityModal.value = true;
};
</script>
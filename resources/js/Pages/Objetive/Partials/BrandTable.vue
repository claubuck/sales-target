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
              Total Unidades mes de comparación
              {{
                showColumnComparePeriod
                  ? formatDate(objetive.compare_period)
                  : formatDate(objetive.compare_period_secondary)
              }}
              : {{ formatNumberToThousands(totalQuantity) }}
            </p>
            <p
              v-if="showColumnComparePeriodSecondary"
              class="text-sm font-bold text-gray-900"
            >
              Total Unidades mes de comparación
              {{
                showColumnComparePeriodSecondary
                  ? formatDate(objetive.compare_period_secondary)
                  : formatDate(objetive.compare_period)
              }}
              : {{ formatNumberToThousands(totalSecondaryQuantity) }}
            </p>
            <p class="text-sm font-bold text-gray-900">
              Total Unidades mes Objetivo {{ formatDate(objetive.period) }} :
              {{ formatNumberToThousands(totalQuantityPeriod) }}
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
                    formatNumberToThousands(
                      Object.values(groupedData).reduce(
                        (acc, group) =>
                          acc + (parseFloat(group.totalQuantity) || 0),
                        0
                      )
                    )
                  }}
                </td>
                <td
                  v-if="showColumnComparePeriodSecondary"
                  class="text-gray-900"
                >
                  {{
                    formatNumberToThousands(
                      Object.values(groupedData).reduce(
                        (acc, group) =>
                          acc + (parseFloat(group.totalSecondaryQuantity) || 0),
                        0
                      )
                    )
                  }}
                </td>
                <td class="text-gray-900">
                  {{
                    formatNumberToThousands(
                      Object.values(groupedData).reduce(
                        (acc, group) =>
                          acc +
                          (parseFloat(group.totalQuantityWithPercentage) || 0),
                        0
                      )
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
                    {{ formatNumberToThousands(group.totalQuantity) }}
                  </td>
                  <td
                    v-if="showColumnComparePeriodSecondary"
                    class="text-gray-900"
                  >
                    {{ formatNumberToThousands(group.totalSecondaryQuantity) }}
                  </td>
                  <td class="text-gray-900">
                    {{
                      formatNumberToThousands(group.totalQuantityWithPercentage)
                    }}
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
                          objetive.comparison_period !== null &&
                            openEditQuantityModal(
                              selloutdata.id,
                              selloutdata.quantity_with_percentage,
                              'quantity_secondary'
                            )
                        "
                        :class="{
                          'text-gray-400 cursor-not-allowed':
                            objetive.comparison_period === null,
                          'text-indigo-600 hover:underline':
                            !isEdited(
                              selloutdata.quantity_with_percentage,
                              selloutdata.quantity,
                              selloutdata.quantity_secondary
                            ) && objetive.comparison_period !== null,
                          'text-red-500':
                            selloutdata.quantity_with_percentage === 0,
                          'text-gray-900 font-bold': isEdited(
                            selloutdata.quantity_with_percentage,
                            selloutdata.quantity,
                            selloutdata.quantity_secondary
                          ),
                        }"
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

  // Primero agrupamos por cliente y PDV
  props.sellout.forEach((data) => {
    const key = `${data.client}|${data.point_of_sale}`;
    if (!groups[data.client]) {
      groups[data.client] = {
        totalQuantity: 0,
        totalSecondaryQuantity: 0,
        totalQuantityWithPercentage: 0,
        totalPrice: 0,
        items: {},
      };
    }

    if (!groups[data.client].items[key]) {
      groups[data.client].items[key] = {
        client: data.client,
        point_of_sale: data.point_of_sale,
        quantity: 0,
        quantity_secondary: 0,
        quantity_with_percentage: 0,
        price: 0,
        id: data.id,
      };
    }

    // Asignamos las cantidades según corresponda
    if (data.quantity) {
      groups[data.client].items[key].quantity = Number(data.quantity);
    }
    if (data.quantity_secondary) {
      groups[data.client].items[key].quantity_secondary = Number(data.quantity_secondary);
    }
    if (data.quantity_with_percentage) {
      groups[data.client].items[key].quantity_with_percentage = Number(data.quantity_with_percentage);
    }
    if (data.price) {
      const priceValue = parseFloat(data.price.toString().replace(/[$,]/g, ""));
      if (!isNaN(priceValue)) {
        groups[data.client].items[key].price = priceValue;
      }
    }
  });

  // Convertimos los items a array y calculamos subtotales
  for (const client in groups) {
    const itemsArr = Object.values(groups[client].items);
    let totalQuantity = 0;
    let totalSecondaryQuantity = 0;
    let totalQuantityWithPercentage = 0;
    let totalPrice = 0;

    itemsArr.forEach((item) => {
      totalQuantity += item.quantity;
      totalSecondaryQuantity += item.quantity_secondary;
      totalQuantityWithPercentage += item.quantity_with_percentage;
      totalPrice += item.price;
    });

    groups[client].totalQuantity = totalQuantity;
    groups[client].totalSecondaryQuantity = totalSecondaryQuantity;
    groups[client].totalQuantityWithPercentage = totalQuantityWithPercentage;
    groups[client].totalPrice = totalPrice;
    groups[client].items = itemsArr;
  }

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

const totalSecondaryQuantity = computed(() => {
  return Object.values(groupedData.value).reduce(
    (acc, group) => acc + group.totalSecondaryQuantity,
    0
  );
});

const totalQuantityPeriod = computed(() => {
  return Object.values(groupedData.value).reduce(
    (acc, group) => acc + (parseFloat(group.totalQuantityWithPercentage) || 0), // Convierte el valor a número
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
  const percentage = props.objetive.percentages[0]?.real_percentage ?? 0;
  return parseFloat(percentage);
});

// Función para abrir el modal de edición de cantidad
const openEditQuantityModal = (id, quantity) => {
  selectedSelloutId.value = id;
  selectedSelloutQuantity.value = quantity;
  showEditQuantityModal.value = true;
};

function formatNumberToThousands(num) {
  // Convierte el número a tipo número, si es una cadena
  const number = typeof num === "string" ? parseFloat(num) : num;

  // Asegúrate de que es un número
  if (isNaN(number)) {
    return num; // Si no es un número, devuelve el valor original
  }

  // Convierte el número a string y usa una expresión regular para añadir el punto
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Función para determinar si la cantidad ha sido editada
function isEdited(quantityWithPercentage, quantity, quantitySecondary) {
  if (showColumnComparePeriod.value) {
    return quantityWithPercentage !== quantity;
  } else if (showColumnComparePeriodSecondary.value) {
    return quantityWithPercentage !== quantitySecondary;
  } else {
    return false;
  }
}
</script>
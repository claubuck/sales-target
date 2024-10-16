<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabsLink from "./Partials/TabsLink.vue";
import BrandTable from "./Partials/BrandTable.vue";
import AllBrandsTable from "./Partials/AllBrandsTable.vue";
import PeriodChangeModal from "./Partials/PeriodChangeModal.vue";
import { PencilIcon } from "@heroicons/vue/24/outline";
import { Head, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/es";
dayjs.locale("es");

const props = defineProps({
  brands: Object,
  filter: String,
  objetive: Object,
  sellout: Object,
});

// Inicializa activeTab con el valor de filter o "CAROLINA HERRERA"
const activeTab = ref(props.filter);
const showPeriodChangeModal = ref(false);

// Función para establecer la tab activa y hacer la solicitud con Inertia
function setActiveTab(tabName) {
  activeTab.value = tabName;

  // Hacer la solicitud a la API usando Inertia
  router.get(
    `/set-objetive/${props.objetive.id}`,
    { filter: tabName },
    {
      preserveState: true, // Esto mantiene el estado de la página
      replace: true, // Esto reemplaza la entrada en el historial
    }
  );
}

// Observa cambios en el prop filter para actualizar activeTab si es necesario
watch(
  () => props.filter,
  (newFilter) => {
    activeTab.value = newFilter || "CAROLINA HERRERA";
  }
);

const formatDate = (date) => {
  return dayjs(date).format("MMMM");
};

const periodFormatDate = (date) => {
  return dayjs(date).format("MMMM YYYY");
};
</script>

<template>
  <Head title="Objetivos" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Objetivos de {{ formatDate(objetive.period) }} 2024
        </h2>
        <button
          @click="showPeriodChangeModal = true"
          :class="[
            'ml-auto flex items-center hover:text-gray-900',
            objetive.comparison_period ? 'text-gray-600' : 'text-red-600',
            !objetive.comparison_period ? 'animate-pulse' : '',
          ]"
        >
          {{
            objetive.comparison_period
              ? "Periodo de comparación " +
                periodFormatDate(objetive.comparison_period)
              : "Debe seleccionar un periodo de comparación"
          }}
        </button>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow-sm sm:rounded-lg">
          <!-- Tabs with sticky position -->
          <div
            class="sticky top-0 z-50 bg-white shadow"
          >
            <TabsLink
              :activeTab="activeTab"
              @update:activeTab="setActiveTab"
              :brands="brands"
            />
          </div>

          <!-- Contenido que hace scroll -->
          <div v-if="filter == 'TODOS'" class="mt-4">
            <AllBrandsTable
              :sellout="sellout"
              :objetive="objetive"
              :brands="brands"
            />
          </div>
          <div v-else>
            <BrandTable
              :sellout="sellout"
              :objetive="objetive"
              :brand="filter"
            />
          </div>
        </div>
      </div>
    </div>

    <PeriodChangeModal
      :show="showPeriodChangeModal"
      :objetive="objetive"
      :brand="filter"
      @close="showPeriodChangeModal = false"
    />
  </AuthenticatedLayout>
</template>

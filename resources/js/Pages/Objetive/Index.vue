<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabsLink from './Partials/TabsLink.vue';
import BrandTable from './Partials/BrandTable.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import dayjs from 'dayjs';
import 'dayjs/locale/es';
dayjs.locale('es');

const props = defineProps({
    brands: Object,
    filter: String,
    objetive: Object,
    sellout: Object,
});

// Inicializa activeTab con el valor de filter o "CAROLINA HERRERA"
const activeTab = ref(props.filter);

// Función para establecer la tab activa y hacer la solicitud con Inertia
function setActiveTab(tabName) {
    activeTab.value = tabName;

    // Hacer la solicitud a la API usando Inertia
    router.get(`/set-objetive/${props.objetive.id}`, { filter: tabName }, {
        preserveState: true, // Esto mantiene el estado de la página
        replace: true, // Esto reemplaza la entrada en el historial
    });
}

// Observa cambios en el prop filter para actualizar activeTab si es necesario
watch(() => props.filter, (newFilter) => {
    activeTab.value = newFilter || 'CAROLINA HERRERA';
});

const formatDate = (date) => {
    return dayjs(date).format('MMMM');
};
</script>

<template>
    <Head title="Objetivos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Objetivos de {{ formatDate(objetive.period) }} 2024</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-sm sm:rounded-lg">
                    <TabsLink :activeTab="activeTab" @update:activeTab="setActiveTab" :brands = brands />
                    <BrandTable :sellout = sellout :objetive = objetive :brand = filter />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

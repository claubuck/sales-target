<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductsTable from './Partials/ProductsTable.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: Object,
});

const activeTab = ref('RABANNE');

function setActiveTab(tabName) {
  activeTab.value = tabName;
}

</script>

<template>
    <Head title="Objetivos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de productos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-sm sm:rounded-lg">
                    <TabsLink :activeTab="activeTab" @update:activeTab="setActiveTab"/>
                    <ProductsTable :products = products />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

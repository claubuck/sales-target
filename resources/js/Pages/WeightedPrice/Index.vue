<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ListPriceComponent from './Partials/ListPriceComponent.vue';
import { ref } from 'vue';

const props = defineProps({
    brands: Object,
});

</script>

<template>
    <Head title="Objetivos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de precios ponderados</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-sm sm:rounded-lg">
                    <ListPriceComponent :brands = brands /> 
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

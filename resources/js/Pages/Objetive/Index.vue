<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TabsLink from './Partials/TabsLink.vue';
import BrandTable from './Partials/BrandTable.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeTab = ref('RABANNE');

function setActiveTab(tabName) {
  activeTab.value = tabName;
}

</script>

<template>
    <Head title="Objetivos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Objetivos Julio 2024</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-4 overflow-hidden shadow-sm sm:rounded-lg">
                    <TabsLink :activeTab="activeTab" @update:activeTab="setActiveTab"/>
                    <BrandTable/>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

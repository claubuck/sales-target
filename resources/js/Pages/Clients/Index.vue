<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ListClientsComponent from './Partials/ListClientsComponent.vue';

const props = defineProps({
  clientEquivalences: Object,
});
</script>

<template>
  <Head title="Clientes" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Clientes</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 overflow-hidden shadow-sm sm:rounded-lg">
          <ListClientsComponent :client-equivalences="clientEquivalences" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

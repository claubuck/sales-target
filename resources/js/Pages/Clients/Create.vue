<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  clientes: Array,
});

const form = useForm({
  cliente_comercial: '',
  cliente_display: '',
});
</script>

<template>
  <Head title="Nuevo cliente" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nuevo cliente</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="form.post(route('clients.store'), { preserveScroll: true })" class="space-y-6">
            <div>
              <InputLabel for="cliente_comercial" value="Nombre en comercial" />
              <select
                id="cliente_comercial"
                v-model="form.cliente_comercial"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
                <option value="">Seleccione un cliente</option>
                <option v-for="c in clientes" :key="c" :value="c">{{ c }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors.cliente_comercial" />
            </div>

            <div>
              <InputLabel for="cliente_display" value="Nombre a mostrar en la plataforma" />
              <TextInput
                id="cliente_display"
                v-model="form.cliente_display"
                type="text"
                class="mt-1 block w-full"
                placeholder="Ej: ROUGE"
                required
              />
              <InputError class="mt-1" :message="form.errors.cliente_display" />
            </div>

            <div class="flex gap-3">
              <PrimaryButton type="submit" :disabled="form.processing">Guardar</PrimaryButton>
              <Link :href="route('clients.index')">
                <SecondaryButton type="button">Cancelar</SecondaryButton>
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

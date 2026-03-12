<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  sucursales: Array,
  clientes: Array,
});

const form = useForm({
  client: '',
  sucursal: '',
  sucursal_objetivo_ba: '',
});

const selectedSucursales = ref([]);
const searchSucursal = ref('');

const filteredSucursales = computed(() => {
  if (!props.sucursales) return [];
  const q = searchSucursal.value.trim().toLowerCase();
  if (!q) return props.sucursales;
  return props.sucursales.filter((s) => String(s).toLowerCase().includes(q));
});

function toggleSucursal(suc) {
  const idx = selectedSucursales.value.indexOf(suc);
  if (idx === -1) {
    selectedSucursales.value = [...selectedSucursales.value, suc];
  } else {
    selectedSucursales.value = selectedSucursales.value.filter((s) => s !== suc);
  }
}

function submit() {
  if (selectedSucursales.value.length === 0) {
    form.setError('sucursal', 'Seleccione al menos una sucursal comercial.');
    return;
  }
  form.sucursal = selectedSucursales.value.join(', ');
  form.post(route('doors.store'), {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head title="Nueva puerta" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva puerta</h2>
    </template>

    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <InputLabel for="client" value="Cliente" />
              <select
                id="client"
                v-model="form.client"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required
              >
                <option value="">Seleccione un cliente</option>
                <option v-for="c in clientes" :key="c" :value="c">{{ c }}</option>
              </select>
              <InputError class="mt-1" :message="form.errors.client" />
            </div>

            <div>
              <InputLabel value="Sucursales que vienen de comercial" />
              <p class="mt-0.5 text-xs text-gray-500">
                Seleccione una o más. Use el buscador para filtrar.
              </p>
              <input
                v-model="searchSucursal"
                type="text"
                placeholder="Buscar sucursal..."
                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
              />
              <div class="mt-2 max-h-48 overflow-y-auto rounded-md border border-gray-300 p-3 space-y-2">
                <label
                  v-for="s in filteredSucursales"
                  :key="s"
                  class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded"
                >
                  <input
                    type="checkbox"
                    :checked="selectedSucursales.includes(s)"
                    @change="toggleSucursal(s)"
                  />
                  <span class="text-sm text-gray-700">{{ s }}</span>
                </label>
              </div>
              <p v-if="selectedSucursales.length" class="mt-1 text-xs text-gray-500">
                Seleccionadas: {{ selectedSucursales.length }}
              </p>
              <InputError class="mt-1" :message="form.errors.sucursal" />
            </div>

            <div>
              <InputLabel for="sucursal_objetivo_ba" value="Equivalencia a mostrar en la plataforma" />
              <TextInput
                id="sucursal_objetivo_ba"
                v-model="form.sucursal_objetivo_ba"
                type="text"
                class="mt-1 block w-full"
                placeholder="Ej: ALTO ROSARIO-ROUGE"
                required
              />
              <InputError class="mt-1" :message="form.errors.sucursal_objetivo_ba" />
            </div>

            <div class="flex gap-3">
              <PrimaryButton type="submit" :disabled="form.processing">Guardar</PrimaryButton>
              <Link :href="route('doors.index')">
                <SecondaryButton type="button">Cancelar</SecondaryButton>
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

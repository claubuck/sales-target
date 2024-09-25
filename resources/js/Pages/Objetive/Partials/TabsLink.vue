<template>
  <div>
    <div class="sm:hidden">
      <label for="tabs" class="sr-only">Select a tab</label>
      <select
        id="tabs"
        name="tabs"
        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
      >
        <option v-for="tab in tabs" :key="tab.name" :selected="tab.current">
          {{ tab.name }}
        </option>
      </select>
    </div>
    <div class="hidden sm:block">
      <nav
        class="isolate flex divide-x divide-gray-200 rounded-lg shadow"
        aria-label="Tabs"
      >
        <a
          v-for="(tab, tabIdx) in tabs"
          :key="tab.name"
          :href="tab.href"
          :class="[
            'group relative min-w-0 flex-1 overflow-hidden px-4 py-4 text-center text-sm font-medium',
            tab.current ? 'text-white' : 'text-gray-600 hover:text-gray-800',
            tabIdx === 0 ? 'rounded-l-lg' : '',
            tabIdx === tabs.length - 1 ? 'rounded-r-lg' : '',
          ]"
          :style="{ backgroundColor: tab.color }"
          @click="handleClick(tab)"
          :aria-current="tab.current ? 'page' : undefined"
        >
          <span>{{ tab.name }}</span>
          <span
            aria-hidden="true"
            :style="{
              backgroundColor: tab.current ? '#1D4ED8' : 'transparent',
            }"
            class="absolute inset-x-0 bottom-0 h-0.5"
          />
        </a>
      </nav>
    </div>
  </div>
</template>
  
  <script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  activeTab: {
    type: String,
    required: true
  },
  brands: {
    type: Array,
    default: () => []
  }
});
console.log('brands',props.brands);
const emit = defineEmits(['update:activeTab']);

// Función para manejar el clic en la tab
function handleClick(tab) {
  emit('update:activeTab', tab.name);
}

// Mapeo de colores según el eje
const colorMap = {
  'EJE 1': '#58d68d',
  'EJE 2': '#85c1e9', 
  'EJE 3': '#a569bd',
};

// Computed para generar las tabs desde props.brands
const tabs = computed(() => {
  return props.brands.map(brand => ({
    name: brand.name,
    href: "#", // Cambia esto según sea necesario
    current: brand.name === props.activeTab,
    color: colorMap[brand.axis] || '#ccc' // Color predeterminado si no está en el mapa
  }));
});

</script>
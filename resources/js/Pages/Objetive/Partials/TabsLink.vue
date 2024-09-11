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
import { ref } from 'vue';

const props = defineProps({
  activeTab: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['update:activeTab']);

function handleClick(tab) {
  emit('update:activeTab', tab.name);
}

const tabs = [
  { name: "CAROLINA HERRERA", href: "#", current: true, color: "#58d68d" }, // Azul
  { name: "RABAN0NE", href: "#", current: false, color: "#58d68d" }, // Naranja
  { name: "JPG", href: "#", current: false, color: "#85c1e9" }, // Verde
  { name: "NINA RICCI", href: "#", current: false, color: "#85c1e9" }, // Azul más claro
  { name: "BANDERAS", href: "#", current: false, color: "#a569bd" }, // Azul púrpura
  { name: "ADOLFO DOMINGUEZ", href: "#", current: false, color: "#a569bd" }, // Rosa
  { name: "BENETON", href: "#", current: false, color: "#a569bd" }, // Morado
  { name: "SHAKIRA", href: "#", current: false, color: "#a569bd" }, // Rojo
  { name: "AGATHA", href: "#", current: false, color: "#a569bd" }, // Azul oscuro
  { name: "PACHA", href: "#", current: false, color: "#a569bd" }, // Amarillo
  { name: "RAPSODIA", href: "#", current: false, color: "#a569bd" }, // Cian
];
</script>
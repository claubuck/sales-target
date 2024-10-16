<!-- Spinner.vue -->
<template>
    <div
      class="absolute inset-0 flex items-center justify-center bg-gray-50 bg-opacity-75"
    >
      <svg
        class="animate-spin h-12 w-12 text-gray-600"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 50 50"
      >
        <circle
          class="opacity-25"
          cx="25"
          cy="25"
          r="20"
          stroke="currentColor"
          stroke-width="5"
          fill="none"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M25 5C12.745 5 5 12.745 5 25h5c0-10.493 8.507-19 19-19V5z"
        ></path>
      </svg>

      <!-- Texto debajo del SVG -->
    <p class="mt-4 text-gray-600">{{ text }}</p>

    </div>
  </template>
  
  <script setup>

  const props = defineProps({
    text: String,
  });
  </script>
  
  <style scoped>
  /* Puedes agregar estilos específicos si es necesario */
  </style>
  
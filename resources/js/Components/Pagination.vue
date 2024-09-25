<template>
    <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
      <div class="-mt-px flex w-0 flex-1">
        <a v-if="showPrev" @click="changePage(currentPage - 1)" class="inline-flex items-center border-t-2 border-transparent pr-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
          <ArrowLongLeftIcon class="mr-3 h-5 w-5 text-gray-400" aria-hidden="true" />
          Previous
        </a>
      </div>
      <div class="hidden md:-mt-px md:flex">
        <a v-for="page in pages" :key="page" @click="changePage(page)" :class="pageClass(page)" class="inline-flex items-center border-t-2 border-transparent px-4 pt-4 text-sm font-medium">
          {{ page }}
        </a>
      </div>
      <div class="-mt-px flex w-0 flex-1 justify-end">
        <a v-if="showNext" @click="changePage(currentPage + 1)" class="inline-flex items-center border-t-2 border-transparent pl-1 pt-4 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
          Next
          <ArrowLongRightIcon class="ml-3 h-5 w-5 text-gray-400" aria-hidden="true" />
        </a>
      </div>
    </nav>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  import { ArrowLongLeftIcon, ArrowLongRightIcon } from '@heroicons/vue/20/solid';
  
  const props = defineProps({
    currentPage: {
      type: Number,
      required: true,
    },
    totalPages: {
      type: Number,
      required: true,
    },
  });
  
  const showPrev = ref(false);
  const showNext = ref(false);
  
  // Calcula las páginas disponibles
  const pages = Array.from({ length: props.totalPages }, (_, index) => index + 1);
  
  // Observa los cambios en currentPage y totalPages
  watch([() => props.currentPage, () => props.totalPages], ([currentPage, totalPages]) => {
    alert(currentPage);
    showPrev.value = currentPage > 1;
    showNext.value = currentPage < totalPages;
  });
  
  // Define la función para cambiar de página
  const emit = defineEmits();
  
  const changePage = (page) => {
    if (page >= 1 && page <= props.totalPages) {
      emit('page-changed', page);
    }
  };
  
  // Función para aplicar clases a los números de página
  const pageClass = (page) => {
    return {
      'text-indigo-600 border-indigo-500': page === props.currentPage,
      'text-gray-500 hover:border-gray-300 hover:text-gray-700': page !== props.currentPage,
    };
  };
  </script>
  
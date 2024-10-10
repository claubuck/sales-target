<template>
    <!-- Global notification live region, render this permanently at the end of the document -->
    <div
      aria-live="assertive"
      class="mt-10 pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50"
    >
      <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
        <!-- Success notification panel -->
        <transition
          enter-active-class="transform ease-out duration-300 transition"
          enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
          enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
          leave-active-class="transition ease-in duration-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="showSuccess"
            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5"
          >
            <div class="p-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <CheckCircleIcon
                    class="h-6 w-6 text-green-400"
                    aria-hidden="true"
                  />
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                  <p class="text-sm font-medium text-gray-900">
                    {{ session.flash.success }}
                  </p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                  <button
                    type="button"
                    @click="showSuccess = false"
                    class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </transition>
  
        <!-- Error notification panel -->
        <transition
          enter-active-class="transform ease-out duration-300 transition"
          enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
          enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
          leave-active-class="transition ease-in duration-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div
            v-if="showError"
            class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-red-50 shadow-lg ring-1 ring-black ring-opacity-5"
          >
            <div class="p-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <XMarkIcon
                    class="h-6 w-6 text-red-400"
                    aria-hidden="true"
                  />
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                  <p class="text-sm font-medium text-gray-900">
                    {{ session.flash.error }}
                  </p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                  <button
                    type="button"
                    @click="showError = false"
                    class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  >
                    <span class="sr-only">Close</span>
                    <XMarkIcon class="h-5 w-5" aria-hidden="true" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </template>
  
  
  <script setup>
import { ref, watch } from "vue";
import { CheckCircleIcon } from "@heroicons/vue/24/outline";
import { XMarkIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
  session: Object,
});

const showSuccess = ref(!!props.session.flash.success);
const showError = ref(!!props.session.flash.error);

// Watch for changes in session.flash.success and session.flash.error to update visibility and set auto-hide
watch(() => props.session.flash.success, (newValue) => {
  if (newValue) {
    showSuccess.value = true;
    autoHideNotification('success'); // Start the auto-hide timer for success messages
  } else {
    showSuccess.value = false;
  }
});

watch(() => props.session.flash.error, (newValue) => {
  if (newValue) {
    showError.value = true;
    autoHideNotification('error'); // Start the auto-hide timer for error messages
  } else {
    showError.value = false;
  }
});


// Function to hide the notification after 
const autoHideNotification = (type) => {
  setTimeout(() => {
    if (type === 'success') {
      showSuccess.value = false;
      // Reset the flash success message
      props.session.flash.success = null;
    } else if (type === 'error') {
      showError.value = false;
      // Reset the flash error message
      props.session.flash.error = null;
    }
  }, 3000); 
};

</script>
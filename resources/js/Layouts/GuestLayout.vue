<script setup>
import ApplicationLogoLogin from '@/Components/ApplicationLogoLogin.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <!-- <Link href="/">
                <ApplicationLogoLogin class="w-40 h-30 fill-current text-gray-500" />
            </Link> -->
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>

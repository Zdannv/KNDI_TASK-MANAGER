<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length >= 3" class="flex flex-wrap justify-center -mb-1 mt-4 space-x-1">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" 
                 class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-gray-400 border rounded bg-white dark:bg-gray-800 dark:border-gray-700" 
                 v-html="link.label" />
            
            <Link v-else 
                  class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded hover:bg-indigo-50 focus:border-indigo-500 focus:text-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition duration-150 ease-in-out" 
                  :class="{ 'bg-indigo-600 text-white dark:bg-indigo-500': link.active, 'bg-white text-gray-700': !link.active }" 
                  :href="link.url" 
                  v-html="link.label" />
        </template>
    </div>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Warning from '@/Components/Icon/Warning.vue'; // Import Icon Warning
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { ref, onMounted } from 'vue';

const isLoaded = ref(false);

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const props = defineProps({
  logs: {},
});

const formatDate = (date) => {
  if (!date) return '-';
  return moment(date).format('DD-MM-YYYY, hh:mm:ss a');
};
</script>

<template>
  <Head title="Logs" />
  <AuthenticatedLayout>
    <template #header>
      <div
        class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
        :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
      >
        <h2 class="lg:col-span-2 font-semibold text-xl leading-tight">
          Logs
        </h2>
      </div>
    </template>

    <div
      class="w-screen md:w-full py-8 px-3 transition-all duration-700 ease-out delay-100"
      :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-start">
            
            <div class="relative z-10 -mb-[1px]">
                <div class="w-40 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center px-4">
                    <Warning class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
                </div>
            </div>

            <div class="w-full overflow-x-auto bg-white/70 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0">
            <table class="w-full text-left dark:text-white table-auto">
                <thead>
                <tr class="bg-indigo-50 dark:bg-gray-700">
                    <th class="p-4 rounded-tl-none">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                        Date
                    </p>
                    </th>
                    <th class="p-4">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                        User
                    </p>
                    </th>
                    <th class="p-4">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                        Target
                    </p>
                    </th>
                    <th class="p-4 rounded-tr-lg">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                        Description
                    </p>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="log in logs" :key="log.id" class="border-t border-blue-gray-50 hover:bg-white/40 dark:hover:bg-slate-800/40 transition-colors">
                    <td class="p-4 align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                        {{ formatDate(log.created_at) }}
                    </p>
                    </td>
                    <td class="p-4 min-w-[250px] align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                        {{ log.user?.name || '-' }}
                    </p>
                    </td>
                    <td class="p-4 min-w-[250px] align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                        {{ log.target || '-' }}
                    </p>
                    </td>
                    <td class="p-4 align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                        {{ log.description || '-' }}
                    </p>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
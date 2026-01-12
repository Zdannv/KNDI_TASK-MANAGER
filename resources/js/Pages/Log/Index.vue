<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
    <!-- Header dengan animasi -->
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

    <!-- Main Content -->
    <div
      class="w-screen md:w-full py-8 px-3 transition-all duration-700 ease-out delay-100"
      :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="overflow-x-auto bg-white/70 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg">
          <table class="w-full text-left dark:text-white table-auto">
            <thead>
              <tr class="bg-indigo-50 dark:bg-gray-700">
                <th class="p-4">
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
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                    Description
                  </p>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id" class="border-t border-blue-gray-50">
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
  </AuthenticatedLayout>
</template>
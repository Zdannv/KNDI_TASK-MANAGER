<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskCreateEditForm from '@/Components/Form/TaskCreateEdit.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import moment from 'moment';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import Search from '@/Components/Icon/Search.vue';
import externalLink from '@/Components/Icon/externalLink.vue';

// State
const openCreateEditForm = ref(false);
const isEditMode = ref(false); // Tetap disimpan untuk logic Form Create
const selectedTask = ref(null);
const isLoaded = ref(false);

// Props
const props = defineProps({
  tasks: {},
  projects: {},
  users: {}, // Kita akan pakai ini untuk mencari nama assign
  communicator: {},
  programmer: {},
  designer: {},
});

// Mounted Animation
onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

// Page & Role
const page = usePage();
const role = computed(() => page.props.auth.user.role);

// Query Params
const queryParams = computed(() => {
  const url = new URL(page.url, window.location.origin);
  return Object.fromEntries(url.searchParams);
});

const search = ref(queryParams.value.search ?? '');

// Handlers
const handleOpenCreateEditForm = () => {
  selectedTask.value = null;
  isEditMode.value = false;
  openCreateEditForm.value = true;
};

const handleCloseForm = () => {
  openCreateEditForm.value = false;
  isEditMode.value = false;
  selectedTask.value = null;
};

// Toggle Is Active (Tetap ada karena switch button masih ada)
const handleUpdateIsActive = (id) => {
  router.post(route('task.changeIsActive', id));
};

const formatDate = (date) => {
  if (!date) return '-';
  return moment(date).format('DD MMMM YYYY');
};

const handleSearch = () => {
  const params = {};
  if (search.value?.trim()) {
    params.search = search.value.trim();
  }
  router.get(route('task.list'), params);
};
</script>

<template>
  <Head title="Tasks" />
  <AuthenticatedLayout>
    
    <template #header>
      <div
        class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
        :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
      >
        <h2 class="lg:col-span-2 font-semibold text-xl leading-tight">
          Tasks
        </h2>
        <div class="lg:col-span-4 flex gap-3 justify-end items-center">
          <TextInput
            id="search"
            type="text"
            class="dark:bg-gray-800 border-gray-400 dark:border-gray-500 w-96"
            v-model="search"
            placeholder="Search for issue or ticket ..."
            @keydown.enter="handleSearch"
          />
          <button
            v-if="['other', 'pm', 'co'].includes(role)" 
            @click="handleOpenCreateEditForm"
            class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
          >
            <Plus />
            <span>New</span>
          </button>
        </div>
      </div>
    </template>

    <button
      v-if="['other', 'pm', 'co'].includes(role)"
      @click="handleOpenCreateEditForm"
      class="fixed sm:hidden right-9 bottom-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm z-40 transition-all duration-800 ease-out delay-500"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <div v-if="openCreateEditForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200"
      >
        <TaskCreateEditForm
          :task="selectedTask"
          :projects="projects"
          :projectId="queryParams.project_id"
          :isEditMode="isEditMode"
          @close="handleCloseForm"
        />
      </div>
    </div>

    <div
      class="sm:hidden w-screen md:w-full pt-3 sm:pt-8 px-3 transition-all duration-1000 ease-out"
      :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="flex flex-col py-4 px-5 text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg">
          <div class="flex flex-row gap-4 pb-4">
            <TextInput
              id="search"
              type="text"
              class="dark:bg-gray-800 border-gray-400 dark:border-gray-500 w-[90%]"
              v-model="search"
              placeholder="Search for issue or ticket..."
              @keydown.enter="handleSearch"
            />
            <button
              @click="handleSearch"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Search />
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="w-screen md:w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="overflow-x-auto bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >
          <table class="w-full text-left dark:text-white table-auto">
            <thead>
              <tr class="bg-indigo-50 dark:bg-gray-700">
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Type</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Assign</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Issue</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Project</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Ticket</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-adb">Start date</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Due date</p>
                </th>
                <th v-if="['other', 'pm', 'co'].includes(role)" class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Is active</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Detail</p>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in tasks" :key="task.id">
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">{{ task.type }}</p>
                </td>

                <td class="p-4 border-t border-blue-gray-50 align-middle min-w-[200px]">
                  <template v-if="task.programmer?.length || task.designer?.length || task.communicator?.length">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                      <span v-for="(id, i) in [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])]" :key="id">
                        {{ users.find(u => u.id === id)?.name ?? id }}
                        <span v-if="i < [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])].length - 1">, </span>
                      </span>
                    </p>
                  </template>
                  <span v-else class="text-gray-500">-</span>
                </td>

                <td class="p-4 min-w-[250px] border-t border-blue-gray-50 align-middle">
                  <a
                    :href="route('task.show', task.id)"
                    class="flex gap-0 font-sans text-sm antialiased font-normal leading-normal text-indigo-600 dark:text-indigo-400 hover:underline"
                  >
                    {{ task.issue }}
                    <externalLink />
                  </a>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                    {{ task.project?.name || '-' }}
                  </p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <a
                    :href="'//' + task.ticket_link"
                    target="_blank"
                    class="block font-sans text-sm antialiased font-normal leading-normal text-indigo-600 dark:text-indigo-400 hover:underline truncate max-w-[200px]"
                  >
                    {{ task.ticket_link }}
                  </a>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                    {{ formatDate(task.start_date) }}
                  </p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900"
                    :class="{ 
                      'text-red-600 font-bold': task.due_date && moment().startOf('day').isAfter(moment(task.due_date).startOf('day'))
                    }">
                    {{ formatDate(task.due_date) }}
                  </p>
                </td>
                
                <td v-if="['other', 'pm', 'co'].includes(role)" class="p-4 border-t border-blue-gray-50 align-middle">
                  <SwitchInput v-model="task.isActive" @update:modelValue="handleUpdateIsActive(task.id)" />
                </td>

                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <a
                    :href="route('task.show', task.id)"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-indigo-600 uppercase transition-all rounded-lg hover:bg-indigo-500/10 active:bg-indigo-500/30 dark:text-indigo-400 dark:hover:bg-indigo-400/10"
                  >
                    View More
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                    </svg>
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
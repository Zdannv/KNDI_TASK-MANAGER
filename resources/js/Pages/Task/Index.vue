<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import Pen from '@/Components/Icon/Pen.vue';
import UserPlus from '@/Components/Icon/UserPlus.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskCreateEditForm from '@/Components/Form/TaskCreateEdit.vue';
import TaskAssignForm from '@/Components/Form/TaskAssign.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import moment from 'moment';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import Search from '@/Components/Icon/Search.vue';
import externalLink from '@/Components/Icon/externalLink.vue';

// --- State ---
const openCreateEditForm = ref(false);
const openAssignForm = ref(false);
const isEditMode = ref(false); 
const selectedTask = ref(null);
const isLoaded = ref(false);

// --- Props ---
const props = defineProps({
  tasks: {},
  projects: {},
  users: {}, 
  communicator: {},
  programmer: {},
  designer: {},
});

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const page = usePage();
const role = computed(() => page.props.auth.user.role);

const queryParams = computed(() => {
  const url = new URL(page.url, window.location.origin);
  return Object.fromEntries(url.searchParams);
});

const search = ref(queryParams.value.search ?? '');

// --- Handlers ---
const handleOpenCreateEditForm = () => {
  selectedTask.value = null;
  isEditMode.value = false;
  openCreateEditForm.value = true;
};

const handleEditTask = (task) => {
  selectedTask.value = task;
  isEditMode.value = true;
  openCreateEditForm.value = true;
};

const handleAssignTask = (task) => {
  selectedTask.value = task;
  openAssignForm.value = true;
};

const handleCloseForm = () => {
  openCreateEditForm.value = false;
  openAssignForm.value = false;
  isEditMode.value = false;
  selectedTask.value = null;
};

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
        <h2 class="font-semibold text-xl leading-tight">Tasks</h2>
        <div class="flex gap-3 justify-end items-center text-sm">
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
            class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors font-medium"
          >
            <Plus />
            <span>New Task</span>
          </button>
        </div>
      </div>
    </template>

    <div v-if="openCreateEditForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskCreateEditForm :task="selectedTask" :projects="projects" :projectId="queryParams.project_id" :isEditMode="isEditMode" @close="handleCloseForm" />
      </div>
    </div>

    <div v-if="openAssignForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskAssignForm :task="selectedTask" :pl="users" :co="communicator" :pg="programmer" :ds="designer" @close="handleCloseForm" />
      </div>
    </div>

    <div class="w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="custom-scrollbar bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >
          <table class="w-full text-left dark:text-white table-auto min-w-[1100px]">
            <thead>
              <tr class="bg-indigo-50 dark:bg-gray-700">
                <th class="p-4"><p class="text-sm font-normal opacity-70">Type</p></th>
                <th class="p-4"><p class="text-sm font-normal opacity-70">Assign</p></th>
                <th class="p-4"><p class="text-sm font-bold italic text-indigo-700 dark:text-indigo-400">Issue</p></th>
                <th class="p-4"><p class="text-sm font-normal opacity-70">Project</p></th>
                <th class="p-4"><p class="text-sm font-normal opacity-70">Ticket</p></th>
                <th class="p-4"><p class="text-sm font-normal opacity-70">Start date</p></th>
                <th class="p-4"><p class="text-sm font-normal opacity-70">Due date</p></th>
                <th v-if="['other', 'pm', 'co'].includes(role)" class="p-4 text-center">
                  <p class="text-sm font-normal opacity-70">Is active</p>
                </th>
                <th class="p-4 text-center"><p class="text-sm font-normal opacity-70 uppercase tracking-widest">Actions</p></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in tasks" :key="task.id" class="border-t border-slate-200 dark:border-slate-800 hover:bg-white/40 transition-colors">
                <td class="p-4 align-middle text-sm">{{ task.type }}</td>
                <td class="p-4 align-middle min-w-[200px] text-sm">
                  {{ [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])].map(id => users.find(u => u.id === id)?.name || id).join(', ') || '-' }}
                </td>

                <td class="p-4 min-w-[250px] align-middle">
                  <a :href="route('task.show', task.id)" 
                    class="flex text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 underline decoration-indigo-500/30 underline-offset-4"
                  >
                    {{ task.issue }}
                    <externalLink />
                  </a>
                </td>

                <td class="p-4 align-middle text-sm">{{ task.project?.name || '-' }}</td>
                <td class="p-4 align-middle">
                  <a :href="'//' + task.ticket_link" target="_blank" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline truncate block max-w-[200px]">
                    {{ task.ticket_link }}
                  </a>
                </td>
                <td class="p-4 align-middle text-sm italic">{{ formatDate(task.start_date) }}</td>
                <td class="p-4 align-middle text-sm italic">
                  <p :class="{ 'text-red-600 font-bold': task.due_date && moment().startOf('day').isAfter(moment(task.due_date).startOf('day')) }">
                    {{ formatDate(task.due_date) }}
                  </p>
                </td>
                
                <td v-if="['other', 'pm', 'co'].includes(role)" class="p-4 align-middle">
                  <div class="flex justify-center">
                    <SwitchInput v-model="task.isActive" @update:modelValue="handleUpdateIsActive(task.id)" />
                  </div>
                </td>

                <td class="p-4 align-middle">
                  <div class="flex items-center justify-center gap-4">
                    <button 
                      v-if="['other', 'pm'].includes(role)"
                      @click="handleAssignTask(task)"
                      class="flex flex-col items-center group transition-transform active:scale-95"
                    >
                      <div class="p-2 text-emerald-600 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-950/30 rounded-lg transition-colors">
                        <UserPlus class="w-5 h-5" />
                      </div>
                      <span class="text-[10px] font-bold uppercase text-emerald-600 mt-1">Assign</span>
                    </button>

                    <button 
                      v-if="['other', 'pm', 'co'].includes(role)"
                      @click="handleEditTask(task)"
                      class="flex flex-col items-center group transition-transform active:scale-95"
                    >
                      <div class="p-2 text-amber-600 group-hover:bg-amber-50 dark:group-hover:bg-amber-950/30 rounded-lg transition-colors">
                        <Pen class="w-5 h-5" />
                      </div>
                      <span class="text-[10px] font-bold uppercase text-amber-600 mt-1">Edit</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Scrollbar permanen agar user tahu area bisa digeser */
.custom-scrollbar {
  overflow-x: auto;
  scrollbar-gutter: stable; /* Menjaga ruang agar tidak melompat */
  scrollbar-width: thin;
  scrollbar-color: #6366f1 #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar {
  height: 10px; /* Lebih tebal agar terlihat sebagai indikator */
  display: block;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9; /* Slate-100 */
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #818cf8; /* Indigo-400 */
  border-radius: 10px;
  border: 2px solid #f1f5f9;
  background-clip: content-box;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #6366f1; /* Indigo-500 */
}
</style>
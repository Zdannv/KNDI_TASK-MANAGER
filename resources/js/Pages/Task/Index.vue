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
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
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
              <span class="hidden sm:inline">New Task</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div v-if="openCreateEditForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-5xl w-full p-8 relative animate-in fade-in zoom-in duration-300 overflow-y-auto max-h-[90vh]">
        <TaskCreateEditForm 
            :task="selectedTask" 
            :projects="projects" 
            :projectId="queryParams.project_id" 
            :isEditMode="isEditMode" 
            @close="handleCloseForm" 
        />
      </div>
    </div>

    <div v-if="openAssignForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskAssignForm :task="selectedTask" :pl="users" :co="communicator" :pg="programmer" :ds="designer" @close="handleCloseForm" />
      </div>
    </div>

    <div class="w-full py-8">
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
                  <div class="flex flex-col gap-1.5 items-start">
                    <a :href="route('task.show', task.id)" 
                      class="flex text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 underline decoration-indigo-500/30 underline-offset-4"
                    >
                      {{ task.issue }}
                      <externalLink />
                    </a>

                    <div v-if="task.is_git_automated" 
                         class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 shadow-sm"
                         title="This task was updated automatically via Git Webhook">
                      <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                      </svg>
                      <span class="opacity-90">{{ task.last_commit_hash }}</span>
                    </div>
                  </div>
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
                    <SwitchInput v-slot:default v-model="task.isActive" @update:modelValue="handleUpdateIsActive(task.id)" />
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
.custom-scrollbar {
  overflow-x: auto;
  scrollbar-gutter: stable;
  scrollbar-width: thin;
  scrollbar-color: #6366f1 #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar {
  height: 10px;
  display: block;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #818cf8;
  border-radius: 10px;
  border: 2px solid #f1f5f9;
  background-clip: content-box;
}
</style>
<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import Folder from '@/Components/Icon/Folder.vue'; 
import Pen from '@/Components/Icon/Pen.vue';     // Import Icon Edit
import Trash from '@/Components/Icon/Trash.vue'; // Import Icon Delete
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProjectForm from '@/Components/Form/Project.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const openForm = ref(false);
const isEditMode = ref(false);
const selectedProject = ref(null);
const isLoaded = ref(false);

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

const handleOpenForm = () => {
  isEditMode.value = false;
  selectedProject.value = null;
  openForm.value = true;
};

const handleCloseForm = () => {
  openForm.value = false;
  isEditMode.value = false;
  selectedProject.value = null;
};

const handleDelete = (id) => {
  if (confirm('Are you sure you want to delete this project?')) {
    router.delete(route('project.destroy', { id, project_id: queryParams.value.project_owner_id }));
  }
};

const handleEdit = (id) => {
  // Cari di projects.data karena pagination
  const project = props.projects.data.find(p => p.id === id);
  if (project) {
    isEditMode.value = true;
    selectedProject.value = project;
    openForm.value = true;
  }
};

const props = defineProps({
  projects: {}, // Object Pagination
  projectOwners: {},
  users: {}
});

const getNameUser = (id) => {
  const user = props.users.find(u => u.id === id);
  return user ? user.name : '-';
};
</script>

<template>
  <Head title="Projects" />
  <AuthenticatedLayout>
    
    <template #header>
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="flex justify-between px-6 py-4 items-center text-gray-800 dark:text-gray-200 
                 bg-white/40 dark:bg-slate-900/60 backdrop-blur-md border border-white/40 dark:border-white/10 
                 shadow-lg rounded-2xl transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <div>
            <h2 class="font-bold text-xl leading-tight text-gray-800 dark:text-white drop-shadow-sm">
              Projects List
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage all projects and assignments.</p>
          </div>
          
          <div v-if="['other', 'pm', 'co'].includes(role)" class="flex justify-end">
            <button
              @click="handleOpenForm"
              class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md hover:shadow-indigo-500/30 transition-all duration-300 transform hover:scale-105"
            >
              <Plus class="w-5 h-5" />
              <span class="hidden sm:inline font-bold text-sm">New Project</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <button
      v-if="['other', 'pm', 'co'].includes(role)"
      @click="handleOpenForm"
      class="fixed sm:hidden right-6 bottom-6 border border-white/20 rounded-full p-4 text-white bg-indigo-600 shadow-xl z-40 transition-all duration-500 ease-out hover:scale-110 active:scale-95"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <div v-if="openForm" class="fixed inset-0 z-50 px-4 flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity">
      <div
        class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl border border-white/50 dark:border-gray-700/50 rounded-2xl shadow-2xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-300"
      >
        <ProjectForm
          :project="selectedProject"
          :projectOwners="projectOwners"
          :projectOwnerId="queryParams.project_owner_id"
          :isEditMode="isEditMode"
          @close="handleCloseForm"
        />
      </div>
    </div>

    <div class="w-full py-8">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div 
          class="flex flex-col items-start transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >

          <div class="relative z-10 -mb-[1px]">
             <div class="w-fit px-6 h-12 bg-white/40 dark:bg-slate-900/60 backdrop-blur-md border-t border-l border-r border-white/40 dark:border-white/10 rounded-t-2xl shadow-sm relative flex items-center gap-3">
                <Folder class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                <span class="font-bold text-gray-800 dark:text-white text-sm tracking-wide shadow-black drop-shadow-sm">Projects Data</span>
                <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-900/60 z-20"></div>
             </div>
          </div>

          <div
            class="w-full overflow-x-auto bg-white/40 dark:bg-slate-900/60 backdrop-blur-md border border-white/40 dark:border-white/10 shadow-xl rounded-b-2xl rounded-tr-2xl relative z-0"
          >
            <table class="w-full text-left dark:text-white table-auto border-collapse">
              <thead>
                <tr class="bg-white/50 dark:bg-gray-800/80 backdrop-blur-md border-b border-white/20 dark:border-white/10">
                  <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">ID</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Name</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Project Owner</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Creator</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Updater</th>
                  <th v-if="['other', 'pm', 'co'].includes(role)" class="p-5 text-center font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/20 dark:divide-white/5">
                <tr v-for="project in projects.data" :key="project.id" class="hover:bg-white/30 dark:hover:bg-white/5 transition duration-200">
                  <td class="p-5 align-middle text-sm text-gray-500 dark:text-gray-400">
                    #{{ project.id }}
                  </td>
                  <td class="p-5 align-middle">
                    <a
                      :href="route('task.list', { project_id: project.id })"
                      class="font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline decoration-indigo-300 underline-offset-2 transition flex items-center gap-2"
                    >
                      <Folder class="w-4 h-4 opacity-50" />
                      {{ project.name }}
                    </a>
                  </td>
                  <td class="p-5 align-middle">
                    <span v-if="project.project_owner" class="px-2.5 py-1 rounded-md bg-slate-100/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300 text-xs font-bold border border-slate-200/50 dark:border-slate-700/50">
                        {{ project.project_owner.name }}
                    </span>
                    <span v-else class="text-gray-400 text-xs">-</span>
                  </td>
                  <td class="p-5 align-middle">
                     <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300">
                             {{ getNameUser(project.creator).charAt(0).toUpperCase() }}
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-200">{{ getNameUser(project.creator) }}</span>
                     </div>
                  </td>
                  <td class="p-5 align-middle">
                     <div class="flex items-center gap-2">
                         <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                             {{ getNameUser(project.updater).charAt(0).toUpperCase() }}
                        </div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ getNameUser(project.updater) }}</span>
                     </div>
                  </td>
                  <td v-if="['other', 'pm', 'co'].includes(role)" class="p-5 align-middle">
                    <div class="flex gap-3 justify-center items-center">
                      <button
                        @click.prevent="handleEdit(project.id)"
                        class="p-1.5 rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition tooltip-trigger" title="Edit"
                      >
                        <Pen class="w-4 h-4" />
                      </button>
                      <button
                        @click.prevent="handleDelete(project.id)"
                        class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition tooltip-trigger" title="Delete"
                      >
                        <Trash class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-6 flex justify-end w-full">
             <Pagination :links="projects.links" />
          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
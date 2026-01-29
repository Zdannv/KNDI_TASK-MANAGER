<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { ref, onMounted } from 'vue';

// Import Icons
import Document from '@/Components/Icon/Document.vue'; // Untuk Tasks
import Folder from '@/Components/Icon/Folder.vue';     // Untuk Projects
import User from '@/Components/Icon/User.vue';         // Untuk Users

defineProps({
  projects: {},
  members: {},
  tasks: {},
});

const isLoaded = ref(false);

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const formatDate = (date) => {
  return date ? moment(date).format('DD MMMM YYYY') : '-';
};
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8 text-gray-800 dark:text-gray-200">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

          <div 
            class="lg:col-span-2 flex flex-col transition-all duration-1000 ease-out"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }"
          >
            
            <div class="relative z-10 -mb-[1px] shrink-0">
               <div class="w-fit px-4 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center gap-3">
                  <Document class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                  <span class="font-bold text-gray-700 dark:text-gray-200">Task Active</span>
                  <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
               </div>
            </div>

            <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0 flex flex-col"> 
              <div class="overflow-x-auto w-full rounded-b-lg"> 
                <div class="max-h-[700px] overflow-y-auto custom-scrollbar">
                  <table class="w-full text-left dark:text-white table-auto border-collapse">
                    <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700 z-10">
                      <tr>
                        <th class="p-4 whitespace-nowrap rounded-tl-none">Type</th>
                        <th class="p-4 whitespace-nowrap min-w-[200px]">Assign</th>
                        <th class="p-4 whitespace-nowrap">Project</th>
                        <th class="p-4 whitespace-nowrap">Issue</th>
                        <th class="p-4 whitespace-nowrap min-w-[300px]">Ticket</th>
                        <th class="p-4 whitespace-nowrap">Start Date</th>
                        <th class="p-4 whitespace-nowrap rounded-tr-lg">Due Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="task in tasks" :key="task.id" class="border-t border-blue-gray-50 hover:bg-white/50 dark:hover:bg-slate-800/50 transition">
                        <td class="p-4 align-middle">{{ task.type }}</td>
                        <td class="p-4 align-middle">
                          <template v-if="task.programmer?.length || task.designer?.length || task.communicator?.length">
                            <span v-for="(id, i) in [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])]" :key="id">
                              {{ members.find(m => m.id === id)?.name ?? id }}{{ i < [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])].length - 1 ? ', ' : '' }}
                            </span>
                          </template>
                          <span v-else>-</span>
                        </td>
                        <td class="p-4 align-middle">{{ task.project.name }}</td>
                        <td class="p-4 align-middle">{{ task.issue }}</td>
                        <td class="p-4 align-middle break-all">
                          <a :href="task.ticket_link" target="_blank" class="text-indigo-600 hover:underline">
                            {{ task.ticket_link || '-' }}
                          </a>
                        </td>
                        <td class="p-4 align-middle">{{ formatDate(task.start_date) }}</td>
                        <td class="p-4 align-middle">
                          <span :class="{ 'text-red-600 font-bold': task.due_date && moment().startOf('day').isAfter(moment(task.due_date).startOf('day')) }">
                            {{ formatDate(task.due_date) }}
                          </span>
                        </td>
                      </tr>
                      <tr v-if="tasks.length === 0">
                        <td colspan="7" class="p-8 text-center text-gray-500">No active tasks</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-8"> 
            
            <div 
              class="flex flex-col transition-all duration-1000 ease-out"
              :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }"
            >
              <div class="relative z-10 -mb-[1px] shrink-0">
                 <div class="w-fit px-4 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center gap-3">
                    <Folder class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <span class="font-bold text-gray-700 dark:text-gray-200">Project Active</span>
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
                 </div>
              </div>

              <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0 flex flex-col">
                <div class="overflow-x-auto w-full rounded-b-lg">
                  <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left dark:text-white table-auto">
                      <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700 z-10">
                        <tr>
                          <th class="p-4 rounded-tl-none">Project</th>
                          <th class="p-4 rounded-tr-lg">Task Count</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="project in projects" :key="project.id" class="border-t border-blue-gray-50 hover:bg-white/50 dark:hover:bg-slate-800/50 transition">
                          <td class="p-4">{{ project.name }}</td>
                          <td class="p-4">{{ project.tasks.length || '-' }}</td>
                        </tr>
                        <tr v-if="projects.length === 0">
                          <td colspan="2" class="p-8 text-center text-gray-500">No active projects</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <div 
              class="flex flex-col transition-all duration-1000 ease-out"
              :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }"
            >
              <div class="relative z-10 -mb-[1px] shrink-0">
                 <div class="w-fit px-4 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center gap-3">
                    <User class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <span class="font-bold text-gray-700 dark:text-gray-200">User Active</span>
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
                 </div>
              </div>

              <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0 flex flex-col">
                <div class="overflow-x-auto w-full rounded-b-lg">
                  <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                    <table class="w-full text-left dark:text-white table-auto">
                      <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700 z-10">
                        <tr>
                          <th class="p-4 rounded-tl-none">Name</th>
                          <th class="p-4 rounded-tr-lg">Task Count</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="member in members" :key="member.id" class="border-t border-blue-gray-50 hover:bg-white/50 dark:hover:bg-slate-800/50 transition">
                          <td class="p-4">{{ member.name }}</td>
                          <td class="p-4">{{ member.total_tasks > 0 ? member.total_tasks : '-' }}</td>
                        </tr>
                        <tr v-if="members.length === 0">
                          <td colspan="2" class="p-8 text-center text-gray-500">No members found</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div> 
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.custom-scrollbar {
  scrollbar-gutter: stable;
  scrollbar-width: thin;
  scrollbar-color: #6366f1 #f1f5f9;
}
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
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
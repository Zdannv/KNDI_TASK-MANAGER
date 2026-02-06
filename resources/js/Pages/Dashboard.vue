<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// 1. Layout Persistent
export default { layout: AuthenticatedLayout };
</script>

<script setup>
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { ref, onMounted } from 'vue';

// Import Icons
import Document from '@/Components/Icon/Document.vue';
import Folder from '@/Components/Icon/Folder.vue';
import User from '@/Components/Icon/User.vue';

defineProps({
  projects: { type: Array, default: () => [] },
  members: { type: Array, default: () => [] },
  tasks: { type: Array, default: () => [] },
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

  <div class="w-full py-8 px-3">
    <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8 text-gray-800 dark:text-gray-200">

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <div 
          class="lg:col-span-2 flex flex-col transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }"
        >
          <div class="relative z-10 -mb-[1px] shrink-0">
             <div class="w-fit px-6 h-12 bg-white/40 dark:bg-gradient-to-b dark:from-slate-700/50 dark:to-slate-800/60 backdrop-blur-xl border-t border-l border-r border-white/40 dark:border-white/20 rounded-t-lg shadow-sm relative flex items-center gap-3">
                <Document class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                <span class="font-bold text-gray-800 dark:text-slate-100 text-sm tracking-wide shadow-black drop-shadow-sm">Task Active</span>
                <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-800/80 z-20"></div>
             </div>
          </div>

          <div class="bg-white/40 dark:bg-gradient-to-b dark:from-slate-800/60 dark:to-slate-950/80 backdrop-blur-xl border border-white/40 dark:border-white/20 shadow-2xl rounded-b-lg rounded-tr-lg relative z-0 flex flex-col overflow-hidden"> 
            <div class="overflow-x-auto w-full"> 
              <div class="max-h-[700px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left dark:text-slate-200 table-auto border-collapse">
                  <thead class="sticky top-0 bg-white/50 dark:bg-slate-800/90 z-10 backdrop-blur-md border-b border-white/20 dark:border-white/10">
                    <tr class="text-xs uppercase tracking-wider text-gray-600 dark:text-slate-400">
                      <th class="p-4 whitespace-nowrap font-semibold">Type</th>
                      <th class="p-4 whitespace-nowrap min-w-[200px] font-semibold">Assign</th>
                      <th class="p-4 whitespace-nowrap font-semibold">Project</th>
                      <th class="p-4 whitespace-nowrap font-semibold">Issue</th>
                      <th class="p-4 whitespace-nowrap min-w-[300px] font-semibold">Ticket</th>
                      <th class="p-4 whitespace-nowrap font-semibold">Start Date</th>
                      <th class="p-4 whitespace-nowrap font-semibold">Due Date</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-white/20 dark:divide-white/5">
                    <tr v-for="task in tasks" :key="task.id" class="hover:bg-white/30 dark:hover:bg-white/5 transition duration-200">
                      <td class="p-4 align-middle text-sm font-medium">{{ task.type }}</td>
                      <td class="p-4 align-middle text-sm">
                        <template v-if="task.programmer?.length || task.designer?.length || task.communicator?.length">
                          <div class="flex flex-wrap gap-1">
                              <span v-for="(id, i) in [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])]" :key="id" 
                                    class="inline-block px-2 py-0.5 rounded-md bg-indigo-50/50 dark:bg-indigo-500/10 border border-indigo-100/50 dark:border-indigo-500/20 text-xs text-indigo-700 dark:text-indigo-300">
                                {{ members.find(m => m.id === id)?.name ?? id }}
                              </span>
                          </div>
                        </template>
                        <span v-else class="text-gray-400 text-xs italic">-</span>
                      </td>
                      <td class="p-4 align-middle text-sm font-medium text-gray-700 dark:text-slate-200">{{ task.project.name }}</td>
                      <td class="p-4 align-middle text-sm text-gray-600 dark:text-slate-400">{{ task.issue }}</td>
                      <td class="p-4 align-middle break-all text-sm">
                        <a :href="task.ticket_link" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 hover:underline decoration-indigo-300 underline-offset-2 transition">
                          {{ task.ticket_link || '-' }}
                        </a>
                      </td>
                      <td class="p-4 align-middle text-sm text-gray-500 dark:text-slate-400">{{ formatDate(task.start_date) }}</td>
                      <td class="p-4 align-middle text-sm">
                        <span :class="[
                          'px-2 py-1 rounded-md text-xs font-bold border',
                          task.due_date && moment().startOf('day').isAfter(moment(task.due_date).startOf('day'))
                              ? 'bg-red-50/50 text-red-600 border-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20'
                              : 'bg-emerald-50/50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20'
                        ]">
                          {{ formatDate(task.due_date) }}
                        </span>
                      </td>
                    </tr>
                    <tr v-if="tasks.length === 0">
                      <td colspan="7" class="p-12 text-center text-gray-400 dark:text-gray-500 italic">
                          <div class="flex flex-col items-center gap-2">
                              <Document class="w-8 h-8 opacity-20" />
                              <span>No active tasks found</span>
                          </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-8"> 
          
          <div 
            class="flex flex-col transition-all duration-1000 ease-out delay-100"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }"
          >
            <div class="relative z-10 -mb-[1px] shrink-0">
               <div class="w-fit px-6 h-12 bg-white/40 dark:bg-gradient-to-b dark:from-slate-700/50 dark:to-slate-800/60 backdrop-blur-xl border-t border-l border-r border-white/40 dark:border-white/20 rounded-t-lg shadow-sm relative flex items-center gap-3">
                  <Folder class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                  <span class="font-bold text-gray-800 dark:text-slate-100 text-sm tracking-wide">Project Active</span>
                  <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-800/80 z-20"></div>
               </div>
            </div>

            <div class="bg-white/40 dark:bg-gradient-to-b dark:from-slate-800/60 dark:to-slate-950/80 backdrop-blur-xl border border-white/40 dark:border-white/20 shadow-2xl rounded-b-lg rounded-tr-lg relative z-0 flex flex-col overflow-hidden">
              <div class="overflow-x-auto w-full">
                <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                  <table class="w-full text-left dark:text-slate-200 table-auto">
                    <thead class="sticky top-0 bg-white/50 dark:bg-slate-800/90 z-10 backdrop-blur-md border-b border-white/20 dark:border-white/10">
                      <tr class="text-xs uppercase tracking-wider text-gray-600 dark:text-slate-400">
                        <th class="p-4 font-semibold">Project Name</th>
                        <th class="p-4 font-semibold text-center">Tasks</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white/20 dark:divide-white/5">
                      <tr v-for="project in projects" :key="project.id" class="hover:bg-white/30 dark:hover:bg-white/5 transition duration-200">
                        <td class="p-4 font-medium text-sm text-gray-700 dark:text-slate-200">{{ project.name }}</td>
                        <td class="p-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 text-xs font-bold rounded-full bg-indigo-100/50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-300 border border-indigo-200/50 dark:border-indigo-500/30">
                              {{ project.tasks.length }}
                            </span>
                        </td>
                      </tr>
                      <tr v-if="projects.length === 0">
                        <td colspan="2" class="p-8 text-center text-gray-400 dark:text-gray-500 italic">No active projects</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div 
            class="flex flex-col transition-all duration-1000 ease-out delay-200"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }"
          >
            <div class="relative z-10 -mb-[1px] shrink-0">
               <div class="w-fit px-6 h-12 bg-white/40 dark:bg-gradient-to-b dark:from-slate-700/50 dark:to-slate-800/60 backdrop-blur-xl border-t border-l border-r border-white/40 dark:border-white/20 rounded-t-lg shadow-sm relative flex items-center gap-3">
                  <User class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                  <span class="font-bold text-gray-800 dark:text-slate-100 text-sm tracking-wide">User Active</span>
                  <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-800/80 z-20"></div>
               </div>
            </div>

            <div class="bg-white/40 dark:bg-gradient-to-b dark:from-slate-800/60 dark:to-slate-950/80 backdrop-blur-xl border border-white/40 dark:border-white/20 shadow-2xl rounded-b-lg rounded-tr-lg relative z-0 flex flex-col overflow-hidden">
              <div class="overflow-x-auto w-full">
                <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                  <table class="w-full text-left dark:text-slate-200 table-auto">
                    <thead class="sticky top-0 bg-white/50 dark:bg-slate-800/90 z-10 backdrop-blur-md border-b border-white/20 dark:border-white/10">
                      <tr class="text-xs uppercase tracking-wider text-gray-600 dark:text-slate-400">
                        <th class="p-4 font-semibold">Team Member</th>
                        <th class="p-4 font-semibold text-center">Tasks</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-white/20 dark:divide-white/5">
                      <tr v-for="member in members" :key="member.id" class="hover:bg-white/30 dark:hover:bg-white/5 transition duration-200">
                        <td class="p-4 text-sm flex items-center gap-3">
                          <div class="w-8 h-8 rounded-full bg-indigo-50/50 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-indigo-600 border border-indigo-100/50 dark:border-slate-600">
                              {{ member.name.charAt(0).toUpperCase() }}
                          </div>
                          <span class="font-medium text-gray-700 dark:text-slate-200">{{ member.name }}</span>
                        </td>
                        <td class="p-4 text-center">
                          <span v-if="member.total_tasks > 0" class="inline-flex items-center justify-center min-w-[2rem] px-2 py-1 text-xs font-bold rounded-full bg-emerald-100/50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-500/30">
                              {{ member.total_tasks }}
                          </span>
                          <span v-else class="text-gray-400 text-xs">-</span>
                        </td>
                      </tr>
                      <tr v-if="members.length === 0">
                        <td colspan="2" class="p-8 text-center text-gray-400 dark:text-gray-500 italic">No members found</td>
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
</template>

<style scoped>
/* Scrollbar Default (Light Mode) */
.custom-scrollbar::-webkit-scrollbar {
  height: 6px;
  width: 6px;
  display: block;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(156, 163, 175, 0.5); /* Abu-abu untuk Light Mode */
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(156, 163, 175, 0.8);
}

/* Scrollbar Dark Mode */
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1); /* Putih transparan untuk Dark Mode */
}
:global(.dark) .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255, 255, 255, 0.2);
}
</style>
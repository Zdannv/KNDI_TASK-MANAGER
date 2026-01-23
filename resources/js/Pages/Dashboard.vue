<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import moment from 'moment';
import { ref, onMounted } from 'vue';

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

          <div class="lg:col-span-2 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out flex flex-col h-[650px]"
              :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }">
            
            <p class="text-center text-lg font-bold py-3 bg-indigo-100 dark:bg-slate-900 rounded-t-lg shrink-0">
                Tasks Active
            </p>
            
            <div class="overflow-auto h-full w-full rounded-b-lg"> 
              <table class="w-full text-left dark:text-white table-auto border-collapse">
                <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700 z-10 shadow-sm">
                  <tr>
                    <th class="p-4 whitespace-nowrap">Type</th>
                    <th class="p-4 whitespace-nowrap min-w-[200px]">Assign</th>
                    <th class="p-4 whitespace-nowrap">Project</th>
                    <th class="p-4 whitespace-nowrap">Issue</th>
                    <th class="p-4 whitespace-nowrap min-w-[300px]">Ticket</th>
                    <th class="p-4 whitespace-nowrap">Start Date</th>
                    <th class="p-4 whitespace-nowrap">Due Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="task in tasks" :key="task.id" class="border-t border-blue-gray-50 hover:bg-white/50 dark:hover:bg-slate-800/50 transition">
                    <td class="p-4">{{ task.type }}</td>
                    <td class="p-4">
                      <template v-if="task.programmer?.length || task.designer?.length || task.communicator?.length">
                        <span v-for="(id, i) in [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])]" :key="id">
                          {{ members.find(m => m.id === id)?.name ?? id }}{{ i < [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])].length - 1 ? ', ' : '' }}
                        </span>
                      </template>
                      <span v-else>-</span>
                    </td>
                    <td class="p-4">{{ task.project.name }}</td>
                    <td class="p-4">{{ task.issue }}</td>
                    <td class="p-4 break-all">
                      <a :href="task.ticket_link" target="_blank" class="text-indigo-600 hover:underline">
                        {{ task.ticket_link || '-' }}
                      </a>
                    </td>
                    <td class="p-4">{{ formatDate(task.start_date) }}</td>
                    <td class="p-4">
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

          <div class="flex flex-col gap-8 h-[650px]"> <div class="flex-1 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out flex flex-col overflow-hidden"
                :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }">
              <p class="text-center text-lg font-bold py-3 bg-indigo-100 dark:bg-slate-900 shrink-0">Projects Active</p>
              
              <div class="overflow-auto flex-1">
                <table class="w-full text-left dark:text-white table-auto">
                  <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700">
                    <tr>
                      <th class="p-4">Project</th>
                      <th class="p-4">Task Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="project in projects" :key="project.id" class="border-t border-blue-gray-50">
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

            <div class="flex-1 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out flex flex-col overflow-hidden"
                :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }">
              <p class="text-center text-lg font-bold py-3 bg-indigo-100 dark:bg-slate-900 shrink-0">Users Active</p>
              
              <div class="overflow-auto flex-1">
                <table class="w-full text-left dark:text-white table-auto">
                  <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700">
                    <tr>
                      <th class="p-4">Name</th>
                      <th class="p-4">Task Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="member in members" :key="member.id" class="border-t border-blue-gray-50">
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

          </div> </div> </div>
    </div>
  </AuthenticatedLayout>
</template>
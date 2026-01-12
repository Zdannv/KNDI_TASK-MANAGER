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
    <div class="w-screen md:w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8 text-gray-800 dark:text-gray-200">

        <!-- Row: Projects & Users -->
        <div class="flex flex-col md:flex-row gap-8 mb-8">

          <!-- Projects Active -->
          <div class="flex-1 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
              :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }">
            <p class="text-center text-lg font-bold py-2 bg-indigo-100 dark:bg-slate-900">Projects Active</p>
            <div class="max-h-72 overflow-y-auto"> <!-- Tambahkan wrapper scroll -->
              <table class="w-full text-left dark:text-white table-auto">
                <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700"> <!-- Sticky header -->
                  <tr>
                    <th class="p-4">Project</th>
                    <th class="p-4">Task Count</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="project in projects" :key="project.id">
                    <td class="p-4 border-t border-blue-gray-50">{{ project.name }}</td>
                    <td class="p-4 border-t border-blue-gray-50">{{ project.tasks.length || '-' }}</td>
                  </tr>
                  <tr v-if="projects.length === 0">
                    <td colspan="2" class="p-8 text-center text-gray-500">No active projects</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Users Active -->
          <div class="flex-1 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
              :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }">
            <p class="text-center text-lg font-bold py-2 bg-indigo-100 dark:bg-slate-900">Users Active</p>
            <div class="max-h-72 overflow-y-auto"> <!-- Sama seperti atas -->
              <table class="w-full text-left dark:text-white table-auto">
                <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700">
                  <tr>
                    <th class="p-4">Name</th>
                    <th class="p-4">Task Count</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="member in members" :key="member.id">
                    <td class="p-4 border-t border-blue-gray-50">{{ member.name }}</td>
                    <td class="p-4 border-t border-blue-gray-50">{{ member.total_tasks > 0 ? member.total_tasks : '-' }}</td>
                  </tr>
                  <tr v-if="members.length === 0">
                    <td colspan="2" class="p-8 text-center text-gray-500">No members found</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Tasks Active -->
        <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out delay-100"
            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-10 opacity-0': !isLoaded }">
          <p class="text-center text-lg font-bold py-2 bg-indigo-100 dark:bg-slate-900">Tasks Active</p>
          <div class=" max-h-[30rem] overflow-y-auto"> <!-- Tinggi lebih besar untuk table utama -->
            <table class="w-full text-left dark:text-white table-auto">
              <thead class="sticky top-0 bg-indigo-50 dark:bg-gray-700 z-10"> <!-- z-10 agar tidak tertutup -->
                <tr>
                  <th class="p-4">Type</th>
                  <th class="p-4 min-w-[200px]">Assign</th>
                  <th class="p-4">Project</th>
                  <th class="p-4">Issue</th>
                  <th class="p-4 min-w-[300px]">Ticket</th>
                  <th class="p-4">Start Date</th>
                  <th class="p-4">Due Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="task in tasks" :key="task.id">
                  <td class="p-4 border-t border-blue-gray-50">{{ task.type }}</td>
                  <td class="p-4 border-t border-blue-gray-50">
                    <template v-if="task.programmer?.length || task.designer?.length || task.communicator?.length">
                      <span v-for="(id, i) in [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])]" :key="id">
                        {{ members.find(m => m.id === id)?.name ?? id }}{{ i < [...(task.programmer || []), ...(task.designer || []), ...(task.communicator || [])].length - 1 ? ', ' : '' }}
                      </span>
                    </template>
                    <span v-else>-</span>
                  </td>
                  <td class="p-4 border-t border-blue-gray-50">{{ task.project.name }}</td>
                  <td class="p-4 border-t border-blue-gray-50">{{ task.issue }}</td>
                  <td class="p-4 border-t border-blue-gray-50 break-all">
                    <a :href="task.ticket_link" target="_blank" class="text-indigo-600 hover:underline">
                      {{ task.ticket_link || '-' }}
                    </a>
                  </td>
                  <td class="p-4 border-t border-blue-gray-50">{{ formatDate(task.start_date) }}</td>
                  <td class="p-4 border-t border-blue-gray-50">
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
  </AuthenticatedLayout>
</template>
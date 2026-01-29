<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import Close from '@/Components/Icon/Close.vue';
import Gear from '@/Components/Icon/Gear.vue';
import Download from '@/Components/Icon/Download.vue';
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Clock from '@/Components/Icon/Clock.vue'; // Import Icon Logtime
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LogtimeForm from '@/Components/Form/Logtime.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import moment from 'moment';
import Datepicker from '@vuepic/vue-datepicker';
import Download2 from '@/Components/Icon/Download2.vue';

const showButtons = ref(false);
const openForm = ref(false);
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

const options = ref(['other', 'co'].includes(role.value) ? true : false);
const id = ref(Number(queryParams.value.user_id) || null);

const dates = ref(
  queryParams.value.from && queryParams.value.to
    ? [
        moment(queryParams.value.from, 'DD-MM-YYYY').toDate(),
        moment(queryParams.value.to, 'DD-MM-YYYY').toDate(),
      ]
    : []
);

const handleOpenOptions = () => {
  options.value = !options.value;
};

const handleOpenForm = () => {
  openForm.value = true;
};

const handleCloseForm = () => {
  openForm.value = false;
};

const handleDelete = (id) => {
  if (confirm('Are you sure you want to delete this logtime?')) {
    router.delete(route('logtime.destroy', id));
  }
};

const props = defineProps({
  logtimes: {},
  tasks: {},
  users: {}
});

const groupedLogtimes = computed(() => {
  const grouped = {};
  props.logtimes.forEach(item => {
    const date = formatDate(item.date);
    if (!grouped[date]) {
      grouped[date] = { items: [], totalTime: 0 };
    }
    grouped[date].items.push(item);
    grouped[date].totalTime += parseFloat(item.time_used || 0);
  });
  return grouped;
});

const formatDate = (date) => {
  return moment(date).format('DD MMMM YYYY');
};

watch(id, (newValue) => {
  const params = { user_id: newValue };
  if (dates.value.length === 2) {
    params.from = moment(dates.value[0]).format('DD-MM-YYYY');
    params.to = moment(dates.value[1]).format('DD-MM-YYYY');
  }
  router.get(route('logtime.list', params));
});

const handleDateChange = (newDate) => {
  const params = {};
  if (id.value) params.user_id = id.value;
  if (newDate && newDate.length === 2) {
    params.from = moment(newDate[0]).format('DD-MM-YYYY');
    params.to = moment(newDate[1]).format('DD-MM-YYYY');
  }
  router.get(route('logtime.list', params));
};

const exportLogtime = (summary) => {
  const params = {};
  if (id.value) params.user_id = id.value;
  if (dates.value.length === 2) {
    params.from = moment(dates.value[0]).format('DD-MM-YYYY');
    params.to = moment(dates.value[1]).format('DD-MM-YYYY');
  }
  params.summary = summary;
  window.open(route('logtime.export', params), '_blank');
};

const visibleButtons = computed(() => {
  return [
    { action: 'add', icon: Plus, handler: handleOpenForm, text: 'New' },
    { action: 'export', icon: Download, handler: () => exportLogtime(false), text: 'Export' },
    { action: 'exportSummary', icon: Download2, handler: () => exportLogtime(true), text: 'ExportSummary' },
    { action: 'reset', icon: Close, handler: () => router.get(route('logtime.list')), text: 'Reset' }
  ];
});
</script>

<template>
  <Head title="Logtimes" />
  <AuthenticatedLayout>
    <template #header>
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <h2 class="font-semibold text-xl leading-tight">Logtimes</h2>
          <div class="flex gap-4 justify-end">
            <button
              v-if="['other', 'co'].includes(role)"
              @click="handleOpenOptions"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Gear />
              <span class="hidden sm:inline">Options</span>
            </button>
            <button
              @click="handleOpenForm"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Plus />
              <span class="hidden sm:inline">New</span>
              <span class="sm:hidden">New</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div v-if="['other', 'co'].includes(role)" class="fixed sm:hidden right-9 bottom-9 z-50">
      <div
        class="shrink-0 inline-flex items-center justify-center p-2 rounded-full dark:text-gray-300 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out border bg-white dark:bg-gray-700 shadow-sm transition-all duration-800 ease-out delay-500"
        :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
      >
        <Hamburger v-model="showButtons" />
      </div>
      <TransitionGroup tag="div" name="button-list">
        <button
          v-for="(button, index) in visibleButtons"
          v-show="showButtons"
          :key="button.action"
          @click="button.handler"
          class="fixed right-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm"
          :style="{ bottom: `${36 + (index + 1) * 76}px` }"
        >
          <component :is="button.icon" />
        </button>
      </TransitionGroup>
    </div>
    <button v-else
      @click="handleOpenForm"
      class="fixed sm:hidden right-9 bottom-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm z-40 transition-all duration-800 ease-out delay-500"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <div v-if="openForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <LogtimeForm :tasks="tasks" @close="handleCloseForm" />
      </div>
    </div>

    <div
      v-if="options"
      class="w-full pt-3 sm:pt-8 transition-all duration-1000 ease-out"
      :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="flex flex-col py-4 px-5 text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg font-medium">
          <div class="flex flex-col sm:flex-row gap-4 pb-4">
            <Datepicker
              v-model="dates"
              range
              placeholder="Select Range Dates"
              :enable-time-picker="false"
              @update:model-value="handleDateChange"
            />
            <SelectInput
              id="user"
              v-model="id"
              :options="users"
              label="name"
              valueKey="id"
              class="block w-full text-gray-700"
              placeholder="Filter user..."
              :dark="true"
            />
          </div>
          <div class="hidden sm:flex justify-end gap-4">
            <button @click="exportLogtime(false)" class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
              <Download />
              <span>{{ (id || dates.length > 0) ? 'Export' : 'ExportAll' }}</span>
            </button>
            <button @click="exportLogtime(true)" class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
              <Download2 />
              <span>{{ (id || dates.length > 0) ? 'ExportSummary' : 'ExportSummaryAll' }}</span>
            </button>
            <button @click="() => router.get(route('logtime.list'))" class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
              <Close />
              <span>Reset</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="w-full py-4 sm:py-8 transition-all duration-700 ease-out delay-100"
      :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-start">
            
            <div class="relative z-10 -mb-[1px]">
                <div class="w-40 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center px-4">
                    <Clock class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
                </div>
            </div>

            <div
            :class="{ 'sm:max-h-[39rem]': options, 'max-h-[49rem]': !options }"
            class="w-full overflow-x-auto bg-white/70 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0"
            >
            <table class="w-full min-w-[40rem] text-left dark:text-white table-auto">
                <thead class="bg-gray-200 dark:bg-gray-700 border-b-2 border-gray-300">
                <tr class="bg-indigo-100 dark:bg-gray-700">
                    <th class="p-4 rounded-tl-none"><p class="text-sm opacity-70">Date</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Issue</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Ticket</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Time used</p></th>
                    <th v-if="['other', 'co'].includes(role)" class="p-4 text-center rounded-tr-lg"><p class="text-sm opacity-70 uppercase tracking-widest">Action</p></th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(group, date) in groupedLogtimes" :key="date">
                    <tr class="bg-indigo-50 dark:bg-slate-800 border-t border-gray-300">
                    <td class="py-2 px-4 italic font-bold text-indigo-700 dark:text-indigo-400" colspan="3">{{ date }}</td>
                    <td class="py-2 px-4">
                        <p class="font-bold text-blue-gray-900">{{ group.totalTime }} h</p>
                    </td>
                    <td v-if="['other', 'co'].includes(role)" class="py-2 px-4"></td>
                    </tr>
                    <tr v-for="value in group.items" :key="value.id" class="border-t border-gray-200 hover:bg-white/50 transition-colors">
                    <td class="p-4 text-sm">{{ formatDate(value.date) }}</td>
                    <td class="p-4">
                        <a :href="route('task.show', value.task.id)" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ value.task.issue }}
                        </a>
                    </td>
                    <td class="p-4">
                        <a :href="'//' + value.task.ticket_link" target="_blank" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ value.task.ticket_link }}
                        </a>
                    </td>
                    <td class="p-4 text-sm">{{ value.time_used }} h</td>
                    <td v-if="['other', 'co'].includes(role)" class="p-4">
                        <div class="flex justify-center">
                        <button @click.prevent="handleDelete(value.id)" class="text-red-600 dark:text-red-400 font-medium hover:underline text-sm">
                            Delete
                        </button>
                        </div>
                    </td>
                    </tr>
                </template>
                </tbody>
            </table>
            </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
:deep(.dp__input) {
  height: 42px !important;
  border-radius: 7px !important;
  border-color: #6B7280;
}
@media (prefers-color-scheme: dark) {
  :deep(.dp__input) {
    background-color: #1F2937;
    color: #E5E7EB;
  }
  :deep(.dp__menu) {
    background-color: #1F2937;
  }
}
</style>
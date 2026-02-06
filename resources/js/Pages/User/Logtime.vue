<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// 1. Layout didaftarkan di sini agar persistent (tidak reload saat navigasi)
export default { layout: AuthenticatedLayout };
</script>

<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import Close from '@/Components/Icon/Close.vue';
import Gear from '@/Components/Icon/Gear.vue';
import Download from '@/Components/Icon/Download.vue';
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Clock from '@/Components/Icon/Clock.vue';
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LogtimeForm from '@/Components/Form/Logtime.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import moment from 'moment';
import Datepicker from '@vuepic/vue-datepicker';
import Download2 from '@/Components/Icon/Download2.vue';
import Modal from '@/Components/Modal.vue';
import Trash from '@/Components/Icon/Trash.vue';
import Detail from '@/Components/Icon/Detail.vue';

const showButtons = ref(false);
const openForm = ref(false);
const isLoaded = ref(false);
const selectedLogtime = ref(null);
const showDetailLogtime = ref(false);

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
  logtimes: Object,
  tasks: {},
  users: {}
});

const groupedLogtimes = computed(() => {
  const grouped = {};
  const list = props.logtimes && props.logtimes.data ? props.logtimes.data : [];

  list.forEach(item => {
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

const openDetailLogtime = (item) => {
  selectedLogtime.value = item;
  showDetailLogtime.value = true;
}

const closeDetailLogtime = () => {
  showDetailLogtime.value = false;
  selectedLogtime.value = null;
}
</script>

<template>
  <Head title="Logtimes" />
  
  <div class="w-full"> <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8 mt-8">
        <div
          class="flex justify-between px-6 py-4 items-center text-gray-800 dark:text-gray-200 
                 bg-white/40 dark:bg-gradient-to-b dark:from-slate-700/30 dark:to-slate-900/60 backdrop-blur-xl border border-white/40 dark:border-white/20 
                 shadow-lg rounded-lg transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <div>
            <h2 class="font-bold text-xl leading-tight text-gray-800 dark:text-slate-100 drop-shadow-sm">Logtimes</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Track and manage work hours.</p>
          </div>
          
          <div class="flex gap-4 justify-end">
            <button
              v-if="['other', 'co'].includes(role)"
              @click="handleOpenOptions"
              class="flex items-center gap-2 px-4 py-2 bg-white/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-700/50 text-gray-700 dark:text-gray-200 rounded-lg shadow-sm border border-white/40 dark:border-white/10 backdrop-blur-sm transition-all"
            >
              <Gear class="w-4 h-4" />
              <span class="hidden sm:inline font-medium text-sm">Options</span>
            </button>
            <button
              @click="handleOpenForm"
              class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md hover:shadow-indigo-500/30 transition-all duration-300 transform hover:scale-105"
            >
              <Plus class="w-5 h-5" />
              <span class="hidden sm:inline font-bold text-sm">Log Time</span>
              <span class="sm:hidden">New</span>
            </button>
          </div>
        </div>
    </div>

    <div v-if="['other', 'co'].includes(role)" class="fixed sm:hidden right-6 bottom-6 z-50">
      <div
        class="shrink-0 inline-flex items-center justify-center p-3 rounded-full text-white bg-indigo-600 shadow-xl z-40 transition-all duration-500 hover:scale-110 active:scale-95"
        @click="showButtons = !showButtons"
      >
        <Hamburger v-model="showButtons" class="w-6 h-6" />
      </div>
      <TransitionGroup tag="div" name="button-list">
        <button
          v-for="(button, index) in visibleButtons"
          v-show="showButtons"
          :key="button.action"
          @click="button.handler"
          class="fixed right-6 border border-white/20 rounded-full p-3 text-gray-700 dark:text-white bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-lg"
          :style="{ bottom: `${80 + (index) * 60}px` }"
        >
          <component :is="button.icon" class="w-5 h-5" />
        </button>
      </TransitionGroup>
    </div>

    <div v-if="openForm" class="fixed inset-0 z-50 px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity">
      <div class="bg-white/90 dark:bg-gradient-to-b dark:from-slate-800/90 dark:to-slate-950 backdrop-blur-2xl border border-white/50 dark:border-white/10 rounded-lg shadow-2xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-300">
        <LogtimeForm :tasks="tasks" @close="handleCloseForm" />
      </div>
    </div>

    <div
      v-if="options"
      class="w-full pt-4 sm:pt-6 transition-all duration-500 ease-out"
      :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="flex flex-col py-6 px-6 text-gray-800 dark:text-gray-200 
                    bg-white/40 dark:bg-gradient-to-b dark:from-slate-700/30 dark:to-slate-900/60 backdrop-blur-xl border border-white/40 dark:border-white/10 
                    shadow-lg rounded-lg font-medium">
          <div class="flex flex-col sm:flex-row gap-4 pb-4 border-b border-gray-200/50 dark:border-white/5 mb-4">
            <div class="w-full sm:w-1/3">
                 <label class="text-xs font-bold text-gray-500 dark:text-slate-400 mb-1 block uppercase">Date Range</label>
                 <Datepicker
                  v-model="dates"
                  range
                  placeholder="Select Range Dates"
                  :enable-time-picker="false"
                  @update:model-value="handleDateChange"
                  input-class-name="glass-datepicker"
                />
            </div>
            <div class="w-full sm:w-1/3">
                <label class="text-xs font-bold text-gray-500 dark:text-slate-400 mb-1 block uppercase">Filter User</label>
                <SelectInput
                  id="user"
                  v-model="id"
                  :options="users"
                  label="name"
                  valueKey="id"
                  class="block w-full"
                  placeholder="Select user..."
                  :dark="true"
                />
            </div>
          </div>
          <div class="hidden sm:flex justify-end gap-3">
            <button @click="exportLogtime(false)" class="flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg bg-indigo-50/50 hover:bg-indigo-100/50 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-300 transition-colors border border-indigo-200/50 dark:border-indigo-800/50">
              <Download class="w-4 h-4" />
              <span>{{ (id || dates.length > 0) ? 'Export Data' : 'Export All' }}</span>
            </button>
            <button @click="exportLogtime(true)" class="flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg bg-emerald-50/50 hover:bg-emerald-100/50 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 dark:text-emerald-300 transition-colors border border-emerald-200/50 dark:border-emerald-800/50">
              <Download2 class="w-4 h-4" />
              <span>Summary</span>
            </button>
            <button @click="() => router.get(route('logtime.list'))" class="flex items-center gap-2 px-3 py-2 text-sm font-bold rounded-lg bg-gray-100/50 hover:bg-gray-200/50 text-gray-600 dark:bg-slate-700/50 dark:hover:bg-slate-600/50 dark:text-slate-300 transition-colors border border-gray-200/50 dark:border-slate-600/50">
              <Close class="w-4 h-4" />
              <span>Reset</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="w-full py-6 sm:py-8 transition-all duration-700 ease-out delay-100"
      :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-start">
            
            <div class="relative z-10 -mb-[1px]">
                <div class="w-fit px-6 h-12 bg-white/40 dark:bg-slate-700/50 dark:to-slate-800/60 backdrop-blur-xl border-t border-l border-r border-white/40 dark:border-white/20 rounded-t-lg shadow-sm relative flex items-center gap-3">
                    <Clock class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                    <span class="font-bold text-gray-800 dark:text-slate-100 text-sm tracking-wide shadow-black drop-shadow-sm">Time Logs</span>
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-800/80 z-20"></div>
                </div>
            </div>

            <div
            :class="{ 'sm:max-h-[39rem]': options, 'max-h-[49rem]': !options }"
            class="w-full overflow-x-auto bg-white/40 dark:bg-gradient-to-b dark:from-slate-800/60 dark:to-slate-950/80 backdrop-blur-xl border border-white/40 dark:border-white/20 shadow-xl rounded-b-lg rounded-tr-lg relative z-0 flex flex-col"
            >
            <table class="w-full min-w-[40rem] text-left dark:text-white table-auto">
                <thead class="bg-gray-200 dark:bg-gray-700 border-b-2 border-gray-300">
                <tr class="bg-indigo-100 dark:bg-gray-700">
                    <th class="p-4 rounded-tl-none"><p class="text-sm opacity-70">Date</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Issue</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Ticket</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Description</p></th>
                    <th class="p-4"><p class="text-sm opacity-70">Time used</p></th>
                    <th v-if="['other', 'co'].includes(role)" class="p-4 text-center rounded-tr-lg"><p class="text-sm opacity-70 uppercase tracking-widest">Action</p></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-white/20 dark:divide-white/5">
                <template v-for="(group, date) in groupedLogtimes" :key="date">
                    <tr class="bg-indigo-50 dark:bg-slate-800 border-t border-gray-300">
                    <td class="py-2 px-4 italic font-bold text-indigo-700 dark:text-indigo-400" colspan="3">{{ date }}</td>
                    <td></td>
                    <td class="py-2 px-4">
                        <p class="font-bold text-blue-gray-900">{{ group.totalTime }} h</p>
                    </td>
                    <td v-if="['other', 'co'].includes(role)" class="py-2 px-4"></td>
                    </tr>
                    <tr v-for="value in group.items" :key="value.id" class="border-t border-gray-200 hover:bg-white/50 dark:hover:bg-slate-800/50 transition-colors">
                    <td class="p-4 text-sm">{{ formatDate(value.date) }}</td>
                    <td class="p-4">
                        <a :href="route('task.show', value.task.id)" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ value.task.issue }}
                        </a>
                    </td>
                    <td class="p-5">
                        <a :href="'//' + value.task.ticket_link" target="_blank" class="text-sm font-mono text-indigo-600 dark:text-indigo-400 hover:underline">
                        {{ value.task.ticket_link }}
                        </a>
                    </td>
                    <td class="p-4 max-w-[200px]">
                      <div class="truncate" :title="value.description">
                        {{ value.description || '-' }}
                      </div>
                    </td>
                    <td class="p-4 text-sm">{{ value.time_used }} h</td>
                    <td v-if="['other', 'co'].includes(role)" class="p-4">
                      <div class="flex justify-center gap-8">
                        <button
                          @click="openDetailLogtime(value)"
                          class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline text-sm"
                        >
                          <Detail class="w-6 h-6" />
                        </button>
                        <button @click.prevent="handleDelete(value.id)" class="text-red-600 dark:text-red-400 font-medium hover:underline text-sm">
                          <Trash class="w-6 h-6" />
                        </button>
                      </div>
                    </td>
                    </tr>
                </template>
                <tr v-if="logtimes.data.length === 0">
                    <td colspan="5" class="p-12 text-center text-gray-400 dark:text-gray-500 italic">No logtimes found.</td>
                </tr>
                </tbody>
            </table>
            </div>
            
            <div class="mt-6 flex justify-end w-full">
                <Pagination :links="logtimes.links" />
            </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>

  <Modal :show="showDetailLogtime" @close="closeDetailLogtime" max-width="2xl">
    <div class="p-6 bg-white dark:bg-slate-800"> 
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                Logtime Details
            </h2>
        </div>

        <div class="space-y-4">
            <div class="border-b pb-2 dark:border-gray-700">
                <label class="block text-sm font-bold uppercase text-gray-500 dark:text-white">Date</label>
                <p class="text-gray-800 dark:text-white">{{ formatDate(selectedLogtime?.date) }}</p>
            </div>
            
            <div class="border-b pb-2 dark:border-gray-700">
                <label class="block text-sm font-bold uppercase text-gray-500 dark:text-white">Ticket Link</label>
                <a :href="'//' + selectedLogtime?.task.ticket_link" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline break-all">
                    {{ selectedLogtime?.task.ticket_link }}
                </a>
            </div>
            
            <div class="border-b pb-2 dark:border-gray-700">
                <label class="block text-sm font-bold uppercase text-gray-500 dark:text-white">Time Used</label>
                <p class="text-gray-800 dark:text-gray-200">{{ selectedLogtime?.time_used }} h</p>
            </div>

            <div>
                <label class="block text-sm font-bold uppercase text-gray-500 dark:text-white">Description</label>
                <div class="max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                  <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                    {{ selectedLogtime?.description || '-' }}
                  </p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button 
                @click="closeDetailLogtime"
                class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition shadow-sm"
            >
                Close
            </button>
        </div>
    </div>
  </Modal>
</template>

<style scoped>
:deep(.dp__input) {
  background-color: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(8px);
  border-color: rgba(255, 255, 255, 0.3);
  border-radius: 0.5rem;
  height: 42px;
  font-size: 0.875rem;
  font-weight: 500;
}

:deep(.dp__input):hover {
    border-color: #6366f1;
}

:deep(.dark .dp__input) {
  background-color: rgba(15, 23, 42, 0.5); 
  border-color: rgba(255, 255, 255, 0.1);
  color: #f1f5f9;
}

:deep(.dp__menu) {
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(12px);
  border-radius: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

:deep(.dark .dp__menu) {
  background-color: rgba(30, 41, 59, 0.95);
  border-color: rgba(255, 255, 255, 0.1);
}

:deep(.dark .dp__cell_inner), :deep(.dark .dp__month_year_select), :deep(.dark .dp__calendar_header_item) {
    color: #e2e8f0;
}

.custom-scrollbar {
  scrollbar-gutter: stable;
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
}
.custom-scrollbar::-webkit-scrollbar {
  height: 6px;
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
</style>
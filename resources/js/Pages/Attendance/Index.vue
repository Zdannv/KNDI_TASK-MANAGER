<script setup>
import Close from '@/Components/Icon/Close.vue';
import Gear from '@/Components/Icon/Gear.vue';
import Download from '@/Components/Icon/Download.vue';
import Download2 from '@/Components/Icon/Download2.vue';
import UserPlus from '@/Components/Icon/UserPlus.vue';
import Pagination from '@/Components/Pagination.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import moment from 'moment';
import Datepicker from '@vuepic/vue-datepicker';

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

const options = ref(true); 
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

const props = defineProps({
  attendances: Object, 
  users: Array
});

// Grouping by Date
const groupedAttendances = computed(() => {
  const grouped = {};
  if (props.attendances && props.attendances.data) {
      props.attendances.data.forEach(item => {
        const date = moment(item.check_in_time).format('DD MMMM YYYY');
        if (!grouped[date]) {
          grouped[date] = { items: [], count: 0 };
        }
        grouped[date].items.push(item);
        grouped[date].count += 1;
      });
  }
  return grouped;
});

const formatTime = (datetime) => {
  return moment(datetime).format('HH:mm:ss');
};

const formatDate = (date) => {
  return moment(date).format('DD MMMM YYYY');
};

// Logic Filter
watch(id, (newValue) => {
  const params = { user_id: newValue };
  if (dates.value.length === 2) {
    params.from = moment(dates.value[0]).format('DD-MM-YYYY');
    params.to = moment(dates.value[1]).format('DD-MM-YYYY');
  }
  router.get(route('attendance.list', params), {}, { preserveState: true });
});

const handleDateChange = (newDate) => {
  const params = {};
  if (id.value) params.user_id = id.value;
  if (newDate && newDate.length === 2) {
    params.from = moment(newDate[0]).format('DD-MM-YYYY');
    params.to = moment(newDate[1]).format('DD-MM-YYYY');
  }
  router.get(route('attendance.list', params), {}, { preserveState: true });
};

const exportAttendance = (summary) => {
  const params = {};
  if (id.value) params.user_id = id.value;
  if (dates.value.length === 2) {
    params.from = moment(dates.value[0]).format('DD-MM-YYYY');
    params.to = moment(dates.value[1]).format('DD-MM-YYYY');
  }
  params.summary = summary;
  window.open(route('attendance.export', params), '_blank');
};

const resetFilter = () => {
    router.get(route('attendance.list'));
}
</script>

<template>
  <Head title="Attendance List" />
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
                Employee Attendance
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor daily check-in and check-out.</p>
          </div>
          
          <div class="flex gap-4 justify-end">
            <button
              @click="handleOpenOptions"
              class="flex items-center gap-2 px-4 py-2 bg-white/50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl shadow-sm border border-white/40 dark:border-gray-600/50 backdrop-blur-sm transition-all"
            >
              <Gear class="w-4 h-4" />
              <span class="hidden sm:inline font-medium text-sm">Options</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div
      v-if="options"
      class="w-full pt-4 sm:pt-6 transition-all duration-500 ease-out"
      :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="flex flex-col py-6 px-6 text-gray-800 dark:text-gray-200 
                    bg-white/40 dark:bg-slate-900/60 backdrop-blur-md border border-white/40 dark:border-white/10 
                    shadow-lg rounded-2xl font-medium">
          <div class="flex flex-col sm:flex-row gap-4 pb-4 border-b border-gray-200/50 dark:border-gray-700/50 mb-4">
            <div class="w-full sm:w-1/3">
                 <label class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 block uppercase">Date Range</label>
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
                <label class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 block uppercase">Filter User</label>
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
            <button @click="exportAttendance(false)" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-300 transition-colors border border-indigo-200 dark:border-indigo-800">
              <Download class="w-4 h-4" />
              <span>Export All</span>
            </button>
            <button @click="exportAttendance(true)" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 dark:text-emerald-300 transition-colors border border-emerald-200 dark:border-emerald-800">
              <Download2 class="w-4 h-4" />
              <span>Summary</span>
            </button>
            <button @click="resetFilter" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors">
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
                <div class="w-fit px-6 h-12 bg-white/40 dark:bg-slate-900/60 backdrop-blur-md border-t border-l border-r border-white/40 dark:border-white/10 rounded-t-2xl shadow-sm relative flex items-center gap-3">
                    <UserPlus class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                    <span class="font-bold text-gray-800 dark:text-white text-sm tracking-wide shadow-black drop-shadow-sm">Attendance Data</span>
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-900/60 z-20"></div>
                </div>
            </div>

            <div
                class="w-full overflow-x-auto bg-white/40 dark:bg-slate-900/60 backdrop-blur-md border border-white/40 dark:border-white/10 shadow-xl rounded-b-2xl rounded-tr-2xl relative z-0"
            >
            <table class="w-full min-w-[40rem] text-left dark:text-white table-auto border-collapse">
                <thead class="sticky top-0 z-20 bg-white/50 dark:bg-gray-800/80 backdrop-blur-md border-b border-white/20 dark:border-white/10">
                <tr>
                    <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Date</th>
                    <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Employee Name</th>
                    <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider">Check-in Time</th>
                    <th class="p-5 font-semibold text-gray-600 dark:text-gray-300 text-sm uppercase tracking-wider text-right">Check-out Time</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-white/20 dark:divide-white/5">
                <template v-for="(group, date) in groupedAttendances" :key="date">
                    <tr class="bg-indigo-50/50 dark:bg-slate-800/50 backdrop-blur-sm border-t border-white/30 dark:border-gray-700/50">
                        <td class="py-3 px-5" colspan="3">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100/50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 text-xs font-bold border border-indigo-200 dark:border-indigo-800">
                                {{ date }}
                            </span>
                        </td>
                        <td class="py-3 px-5 text-right">
                            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total: {{ group.count }}</span>
                        </td>
                    </tr>

                    <tr v-for="item in group.items" :key="item.id" class="hover:bg-white/30 dark:hover:bg-white/5 transition duration-200">
                        <td class="p-5 text-sm text-gray-500 dark:text-gray-400">{{ formatDate(item.check_in_time) }}</td>
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-indigo-600 border border-indigo-100/50">
                                    {{ item.user.name.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 dark:text-gray-200 text-sm">{{ item.user.name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ item.user.email }}</div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="p-5">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-md bg-emerald-100/50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200/50 dark:border-emerald-800/30 backdrop-blur-sm">
                                    {{ formatTime(item.check_in_time) }}
                                </span>
                            </div>
                        </td>

                        <td class="p-5 text-right">
                            <div v-if="item.check_out_time" class="flex items-center justify-end gap-2">
                                <span class="px-2.5 py-1 rounded-md bg-rose-100/50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300 text-xs font-bold border border-rose-200/50 dark:border-rose-800/30 backdrop-blur-sm">
                                    {{ formatTime(item.check_out_time) }}
                                </span>
                            </div>
                            <span v-else class="text-xs text-gray-400 italic">-- : --</span>
                        </td>
                    </tr>
                </template>
                <tr v-if="props.attendances.data.length === 0">
                    <td colspan="4" class="p-12 text-center text-gray-400 dark:text-gray-500 italic">No attendance data found.</td>
                </tr>
                </tbody>
            </table>
            </div>
            
            <div class="mt-6 flex justify-end w-full">
                <Pagination :links="attendances.links" />
            </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Custom style for Datepicker input to match glass theme */
:deep(.dp__input) {
  background-color: rgba(255, 255, 255, 0.5);
  backdrop-filter: blur(4px);
  border-color: rgba(209, 213, 219, 0.5); /* gray-300 */
  border-radius: 0.5rem; /* rounded-lg */
  height: 42px;
  font-size: 0.875rem;
}

:deep(.dp__input):hover {
    border-color: #6366f1; /* indigo-500 */
}

@media (prefers-color-scheme: dark) {
  :deep(.dp__input) {
    background-color: rgba(31, 41, 55, 0.5); /* gray-800 */
    border-color: rgba(75, 85, 99, 0.5); /* gray-600 */
    color: #e5e7eb;
  }
  :deep(.dp__menu) {
    background-color: #1f2937;
    border-color: #374151;
  }
  :deep(.dp__cell_inner), :deep(.dp__month_year_select), :deep(.dp__calendar_header_item) {
      color: #e5e7eb;
  }
}
</style>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// 1. Layout Persistent
export default { layout: AuthenticatedLayout };
</script>

<script setup>
import Close from '@/Components/Icon/Close.vue';
import Download from '@/Components/Icon/Download.vue';
import Download2 from '@/Components/Icon/Download2.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import moment from 'moment';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

// State Loading Animation
const isLoaded = ref(false);
onMounted(() => {
  requestAnimationFrame(() => {
    setTimeout(() => { isLoaded.value = true; }, 50);
  });
});

// Page & Props
const page = usePage();
const props = defineProps({
  attendances: Object, 
  users: Array
});

// Query Params Handling
const queryParams = computed(() => {
  const url = new URL(page.url, window.location.origin);
  return Object.fromEntries(url.searchParams);
});

// Filter States
const id = ref(Number(queryParams.value.user_id) || null);
const dates = ref(
  queryParams.value.from && queryParams.value.to
    ? [
        moment(queryParams.value.from, 'DD-MM-YYYY').toDate(),
        moment(queryParams.value.to, 'DD-MM-YYYY').toDate(),
      ]
    : []
);

// Grouping Logic
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

// Format Helpers
const formatTime = (datetime) => moment(datetime).format('HH:mm:ss');
const formatDate = (date) => moment(date).format('DD MMMM YYYY');

// Watchers for Filters
const updateParams = () => {
  const params = {};
  if (id.value) params.user_id = id.value;
  if (dates.value && dates.value.length === 2) {
    params.from = moment(dates.value[0]).format('DD-MM-YYYY');
    params.to = moment(dates.value[1]).format('DD-MM-YYYY');
  }
  router.get(route('attendance.list', params), {}, { preserveState: true, replace: true });
};

watch(id, updateParams);

const handleDateChange = (newDate) => {
    dates.value = newDate; 
    updateParams();
};

// Actions
const exportAttendance = (summary) => {
  const params = {};
  if (id.value) params.user_id = id.value;
  if (dates.value && dates.value.length === 2) {
    params.from = moment(dates.value[0]).format('DD-MM-YYYY');
    params.to = moment(dates.value[1]).format('DD-MM-YYYY');
  }
  params.summary = summary;
  window.open(route('attendance.export', params), '_blank');
};

const resetFilter = () => {
    id.value = null;
    dates.value = [];
    router.get(route('attendance.list'));
}
</script>

<template>
  <Head title="Attendance List" />
  
  <div class="py-12 w-full transition-opacity duration-500 ease-out" :class="{ 'opacity-100': isLoaded, 'opacity-0': !isLoaded }">
    <div class="max-w-[100rem] mx-auto sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-slate-100 leading-tight">
                    Employee Attendance
                </h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Monitor daily check-in and check-out records.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button 
                    @click="exportAttendance(false)" 
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm"
                >
                    <Download class="w-4 h-4 mr-2" />
                    Export All
                </button>

                <button 
                    @click="exportAttendance(true)" 
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 shadow-sm"
                >
                    <Download2 class="w-4 h-4 mr-2" />
                    Summary
                </button>
            </div>
        </div>

        <div class="relative z-20 bg-white/50 dark:bg-slate-900/40 backdrop-blur-xl p-4 rounded-xl shadow-sm border border-gray-200/50 dark:border-white/5 mb-6">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                
                <div class="flex flex-1 flex-col md:flex-row gap-4 w-full">
                    <div class="w-full md:w-72">
                         <Datepicker
                            v-model="dates"
                            range
                            dark
                            placeholder="Select Date Range"
                            :enable-time-picker="false"
                            @update:model-value="handleDateChange"
                            input-class-name="glass-datepicker"
                            :clearable="false"
                            teleport-center
                        />
                    </div>
                    
                    <div class="w-full md:w-64">
                        <SelectInput
                            id="user"
                            v-model="id"
                            :options="users"
                            label="name"
                            valueKey="id"
                            class="block w-full h-[42px]"
                            placeholder="Filter by User..."
                            :dark="true"
                        />
                    </div>
                </div>

                <button 
                    @click="resetFilter" 
                    class="w-full md:w-auto px-4 py-2.5 bg-gray-100 text-gray-700 dark:bg-slate-700/50 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600/50 transition text-sm font-bold flex items-center justify-center gap-2 whitespace-nowrap border border-gray-200 dark:border-slate-600/50"
                >
                    <Close class="w-4 h-4" />
                    Reset Filter
                </button>
            </div>
        </div>

        <div class="bg-white/40 dark:bg-slate-900/40 backdrop-blur-xl border border-white/40 dark:border-white/20 shadow-xl rounded-xl relative z-0 overflow-hidden">
            
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[50rem] text-left dark:text-slate-200 table-auto border-collapse">
                    <thead class="bg-white/90 dark:bg-slate-900/80 backdrop-blur-sm border-b border-white/20 dark:border-white/10">
                        <tr>
                            <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-xs uppercase tracking-wider">Date / Time</th>
                            <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-xs uppercase tracking-wider">Employee</th>
                            <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-xs uppercase tracking-wider">Check-in</th>
                            <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-xs uppercase tracking-wider text-right">Check-out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/20 dark:divide-white/5">
                        
                        <template v-for="(group, date) in groupedAttendances" :key="date">
                            <tr class="bg-primary-50/50 dark:bg-primary-500/10 backdrop-blur-sm">
                                <td class="py-3 px-5" colspan="3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary-100/50 dark:bg-primary-500/20 text-primary-700 dark:text-primary-300 text-xs font-bold border border-primary-200/50 dark:border-primary-500/30">
                                        {{ date }}
                                    </span>
                                </td>
                                <td class="py-3 px-5 text-right">
                                    <span class="text-[10px] font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide bg-white/50 dark:bg-slate-900/60 px-2 py-1 rounded">
                                        Total: {{ group.count }}
                                    </span>
                                </td>
                            </tr>

                            <tr v-for="item in group.items" :key="item.id" class="hover:bg-white/50 dark:hover:bg-white/5 transition duration-200 group">
                                <td class="p-5 text-sm text-gray-500 dark:text-slate-400 font-medium">
                                    {{ formatDate(item.check_in_time) }}
                                </td>
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-100 to-primary-50 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400 border border-primary-100/50 dark:border-slate-600 shadow-sm">
                                            {{ item.user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-800 dark:text-slate-200 text-sm group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                                {{ item.user.name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-500">{{ item.user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold border border-emerald-100 dark:border-emerald-500/20">
                                        {{ formatTime(item.check_in_time) }}
                                    </span>
                                </td>

                                <td class="p-5 text-right">
                                    <div v-if="item.check_out_time">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 text-xs font-bold border border-rose-100 dark:border-rose-500/20">
                                            {{ formatTime(item.check_out_time) }}
                                        </span>
                                    </div>
                                    <span v-else class="text-xs text-gray-400 italic opacity-50">-- : --</span>
                                </td>
                            </tr>
                        </template>

                        <tr v-if="Object.keys(groupedAttendances).length === 0">
                            <td colspan="4" class="p-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400 dark:text-slate-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-3 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span class="text-sm font-medium">No attendance records found for this period.</span>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <Pagination :links="attendances.links" />
        </div>

    </div>
  </div>
</template>

<style scoped>
:deep(.dp__main) { --dp-background-color: transparent; }

:deep(.dp__input) {
  background-color: rgba(255, 255, 255, 0.5);
  border-color: rgba(229, 231, 235, 1);
  border-radius: 0.5rem;
  height: 42px;
  font-size: 0.875rem;
}

:deep(.dark) .dp__input {
  background-color: rgba(15, 23, 42, 0.4) !important; 
  border-color: rgba(255, 255, 255, 0.1) !important;
  color: #f1f5f9 !important;
}

:deep(.dp__menu) {
  border-radius: 0.75rem;
  overflow: hidden;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

:deep(.dark) .dp__menu {
  background-color: rgba(15, 23, 42, 0.9) !important;
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255,255,255,0.1);
}

:deep(.dark) .dp__cell_inner, 
:deep(.dark) .dp__month_year_select, 
:deep(.dark) .dp__calendar_header_item {
    color: #e2e8f0 !important;
}

::-webkit-scrollbar { height: 6px; width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background-color: rgba(156, 163, 175, 0.3); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background-color: rgba(156, 163, 175, 0.5); }
:global(.dark) ::-webkit-scrollbar-thumb { background-color: rgba(255, 255, 255, 0.1); }
</style>
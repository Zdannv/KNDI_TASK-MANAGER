<script setup>
import Close from '@/Components/Icon/Close.vue';
import Gear from '@/Components/Icon/Gear.vue';
import Download from '@/Components/Icon/Download.vue';
import Download2 from '@/Components/Icon/Download2.vue';
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
  attendances: Array,
  users: Array
});

// Grouping by Date
const groupedAttendances = computed(() => {
  const grouped = {};
  props.attendances.forEach(item => {
    const date = moment(item.check_in_time).format('DD MMMM YYYY');
    if (!grouped[date]) {
      grouped[date] = { items: [], count: 0 };
    }
    grouped[date].items.push(item);
    grouped[date].count += 1;
  });
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
          class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <h2 class="font-semibold text-xl leading-tight">Employee Attendance</h2>
          <div class="flex gap-4 justify-end">
            <button
              @click="handleOpenOptions"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Gear />
              <span class="hidden sm:inline">Options</span>
            </button>
          </div>
        </div>
      </div>
    </template>

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
            <button @click="exportAttendance(false)" class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
              <Download />
              <span>Export All</span>
            </button>
            <button @click="exportAttendance(true)" class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
              <Download2 />
              <span>Export Summary</span>
            </button>
            <button @click="resetFilter" class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors">
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
        <div
          class="overflow-x-auto bg-white/70 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg max-h-[49rem]"
        >
          <table class="w-full min-w-[40rem] text-left dark:text-white table-auto">
            <thead class="bg-gray-200 dark:bg-gray-700 border-b-2 border-gray-300">
              <tr class="bg-indigo-100 dark:bg-gray-700">
                <th class="p-4"><p class="text-sm opacity-70">Date</p></th>
                <th class="p-4"><p class="text-sm opacity-70">Employee Name</p></th>
                <th class="p-4"><p class="text-sm opacity-70">Check-in Time</p></th>
                <th class="p-4"><p class="text-sm opacity-70">Check-out Time</p></th>
              </tr>
            </thead>
            <tbody>
              <template v-for="(group, date) in groupedAttendances" :key="date">
                <tr class="bg-indigo-50 dark:bg-slate-800 border-t border-gray-300">
                  <td class="py-2 px-4 italic font-bold text-indigo-700 dark:text-indigo-400" colspan="3">{{ date }}</td>
                  <td class="py-2 px-4 text-right">
                    <p class="font-bold text-blue-gray-900 text-sm">Total: {{ group.count }}</p>
                  </td>
                </tr>
                <tr v-for="item in group.items" :key="item.id" class="border-t border-gray-200 hover:bg-white/50 transition-colors">
                  <td class="p-4 text-sm text-gray-500">{{ formatDate(item.check_in_time) }}</td>
                  <td class="p-4">
                     <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ item.user.name }}</span>
                     <div class="text-xs text-gray-500">{{ item.user.email }}</div>
                  </td>
                  
                  <td class="p-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-bold">
                            {{ formatTime(item.check_in_time) }}
                        </span>
                    </div>
                  </td>

                  <td class="p-4">
                    <div v-if="item.check_out_time" class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-bold">
                            {{ formatTime(item.check_out_time) }}
                        </span>
                    </div>
                    <span v-else class="text-xs text-gray-400 italic">Not checked out</span>
                  </td>
                </tr>
              </template>
              <tr v-if="props.attendances.length === 0">
                  <td colspan="4" class="p-8 text-center text-gray-500">No attendance data found.</td>
              </tr>
            </tbody>
          </table>
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
<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import Gear from '@/Components/Icon/Gear.vue';
import Download from '@/Components/Icon/Download.vue';
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Book from '@/Components/Icon/Book.vue'; // Import Icon Skill
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SkillForm from '@/Components/Form/Skill.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import moment from 'moment';
import Close from '@/Components/Icon/Close.vue';

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
  if (confirm('Are you sure you want to delete this skill?')) {
    router.delete(route('skill.destroy', id));
  }
};

const props = defineProps({
  skills: {},
  users: {}
});

const formatDate = (date) => {
  return moment(date).format('DD MMMM YYYY');
};

const exportSkill = () => {
  const params = {};
  if (id.value) params.user_id = id.value;
  window.open(route('skill.export', params), '_blank');
};

const visibleButtons = computed(() => {
  const buttons = [
    { action: 'add', icon: Plus, handler: handleOpenForm, text: 'New' },
    { action: 'export', icon: Download, handler: () => exportSkill(), text: 'Export' },
    { action: 'reset', icon: Close, handler: () => router.get(route('skill.list')), text: 'Reset' }
  ];

  return buttons;
});

watch(id, (newValue) => {
  const params = { user_id: newValue };
  router.get(route('skill.list', params));
});
</script>

<template>
  <Head title="Skills" />
  <AuthenticatedLayout>
    <template #header>
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <h2 class="font-semibold text-xl leading-tight">
            Skills
          </h2>
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
        <SkillForm @close="handleCloseForm" />
      </div>
    </div>

    <div
      v-if="options"
      class="w-full pt-3 sm:pt-8 transition-all duration-1000 ease-out"
      :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="flex flex-col py-4 px-5 text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg">
          <div class="flex flex-col sm:flex-row gap-4 pb-4">
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
            <button
              @click="exportSkill"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Download />
              <span>{{ id ? 'Export' : 'ExportAll' }}</span>
            </button>
            <button
              @click="() => router.get(route('skill.list'))"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Close />
              <span>Reset</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="w-full py-8 transition-all duration-700 ease-out delay-100"
      :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div class="flex flex-col items-start">
            
            <div class="relative z-10 -mb-[1px]">
                <div class="w-40 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center px-4">
                    <Book class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
                </div>
            </div>

            <div class="w-full overflow-x-auto bg-white/70 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0">
            <table class="w-full text-left dark:text-white table-auto">
                <thead>
                <tr class="bg-indigo-50 dark:bg-gray-700">
                    <th class="p-4 rounded-tl-none">
                    <p class="text-sm opacity-70">Skill</p>
                    </th>
                    <th class="p-4">
                    <p class="text-sm opacity-70">Created At</p>
                    </th>
                    <th class="p-4 text-center rounded-tr-lg">
                    <p class="text-sm opacity-70 uppercase tracking-widest">Action</p>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="value in skills" :key="value.id" class="border-t border-slate-200 dark:border-slate-800 hover:bg-white/40 transition-colors">
                    <td class="p-4 align-middle">
                    <p class="text-sm font-bold">{{ value.skill }}</p>
                    </td>
                    <td class="p-4 align-middle">
                    <p class="text-sm italic">
                        {{ formatDate(value.created_at) }}
                    </p>
                    </td>
                    <td class="p-4 align-middle">
                    <div class="flex justify-center items-center">
                        <button
                        @click.prevent="handleDelete(value.id)"
                        class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium"
                        >
                        Delete
                        </button>
                    </div>
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
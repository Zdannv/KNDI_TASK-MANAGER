<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import Build from '@/Components/Icon/Build.vue'; // Import Icon Build
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ClientForm from '@/Components/Form/Client.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const openForm = ref(false);
const isEditMode = ref(false);
const selectedClient = ref(null);
const isLoaded = ref(false);

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const page = usePage();
const role = computed(() => page.props.auth.user.role);

const handleOpenForm = () => {
  isEditMode.value = false;
  selectedClient.value = null;
  openForm.value = true;
};

const handleCloseForm = () => {
  openForm.value = false;
  isEditMode.value = false;
  selectedClient.value = null;
};

const handleDelete = (id) => {
  if (confirm('Are you sure you want to delete this client?')) {
    router.delete(route('client.destroy', id));
  }
};

const handleEdit = (id) => {
  const projectOwner = props.projectOwners.find(c => c.id === id);
  if (projectOwner) {
    isEditMode.value = true;
    selectedClient.value = projectOwner;
    openForm.value = true;
  }
};

const props = defineProps({
  projectOwners: {},
  users: {}
});

const getNameUser = (id) => {
  const user = props.users.find(u => u.id === id);
  return user ? user.name : '-';
};
</script>

<template>
  <Head title="Project Owner" />
  <AuthenticatedLayout>
    <template #header>
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <h2 class="lg:col-span-2 font-semibold text-xl leading-tight">
            Project Owner
          </h2>
          <div v-if="['other', 'pm', 'co'].includes(role)" class="lg:col-span-4 flex justify-end">
            <button
              @click="handleOpenForm"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <Plus />
              <span>New</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <button
      v-if="['other', 'pm', 'co'].includes(role)"
      @click="handleOpenForm"
      class="fixed sm:hidden right-9 bottom-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm z-40 transition-all duration-800 ease-out delay-500"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <div v-if="openForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200"
      >
        <ClientForm :projectOwners="selectedClient" :isEditMode="isEditMode" @close="handleCloseForm" />
      </div>
    </div>

    <div class="w-full py-8">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div 
          class="flex flex-col items-start transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >

          <div class="relative z-10 -mb-[1px]">
             <div class="w-40 h-10 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border-t border-l border-r border-slate-200 dark:border-slate-800 rounded-t-xl shadow-[0_-2px_5px_rgba(0,0,0,0.02)] relative flex items-center px-4">
                
                <Build class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />

                <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/70 dark:bg-slate-900/70 z-20"></div>
             </div>
          </div>
          <div
            class="w-full overflow-x-auto bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-b-lg rounded-tr-lg rounded-tl-none relative z-0"
          >
            <table class="w-full text-left dark:text-white table-auto">
              <thead>
                <tr class="bg-indigo-50 dark:bg-gray-700">
                  <th class="p-4 rounded-tl-none">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">ID</p>
                  </th>
                  <th class="p-4">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Name</p>
                  </th>
                  <th class="p-4">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Creator</p>
                  </th>
                  <th class="p-4">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Updater</p>
                  </th>
                  <th v-if="['other', 'pm', 'co'].includes(role)" class="p-4 text-center rounded-tr-lg">
                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Action</p>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="owner in projectOwners" :key="owner.id" class="hover:bg-white/40 dark:hover:bg-slate-800/40 transition-colors">
                  <td class="p-4 border-t border-slate-200 dark:border-slate-800 align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal">{{ owner.id }}</p>
                  </td>
                  <td class="p-4 border-t border-slate-200 dark:border-slate-800 align-middle">
                    <a
                      :href="route('project.list', { project_owner_id: owner.id })"
                      class="block font-sans text-sm antialiased font-bold leading-normal text-indigo-600 dark:text-indigo-400 hover:underline"
                    >
                      {{ owner.name }}
                    </a>
                  </td>
                  <td class="p-4 border-t border-slate-200 dark:border-slate-800 align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal">
                      {{ getNameUser(owner.creator) }}
                    </p>
                  </td>
                  <td class="p-4 border-t border-slate-200 dark:border-slate-800 align-middle">
                    <p class="block font-sans text-sm antialiased font-normal leading-normal">
                      {{ getNameUser(owner.updater) }}
                    </p>
                  </td>
                  <td v-if="['other', 'pm', 'co'].includes(role)" class="p-4 border-t border-slate-200 dark:border-slate-800 align-middle">
                    <div class="flex gap-4 justify-center items-center text-sm">
                      <a
                        @click.prevent="handleEdit(owner.id)"
                        class="cursor-pointer text-indigo-600 hover:text-indigo-800 dark:text-gray-200 dark:hover:text-indigo-400 font-medium transition-colors"
                      >
                        Edit
                      </a>
                      <a
                        @click.prevent="handleDelete(owner.id)"
                        class="cursor-pointer text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium transition-colors"
                      >
                        Delete
                      </a>
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
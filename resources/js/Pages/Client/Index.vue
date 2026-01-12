<script setup>
import Plus from '@/Components/Icon/Plus.vue';
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
  const client = props.clients.find(c => c.id === id);
  if (client) {
    isEditMode.value = true;
    selectedClient.value = client;
    openForm.value = true;
  }
};

const props = defineProps({
  clients: {},
  users: {}
});

const getNameUser = (id) => {
  const user = props.users.find(u => u.id === id);
  return user ? user.name : '-';
};
</script>

<template>
  <Head title="Clients" />
  <AuthenticatedLayout>
    <!-- Header dengan animasi -->
    <template #header>
      <div
        class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
        :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
      >
        <h2 class="lg:col-span-2 font-semibold text-xl leading-tight">
          Project owner
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
    </template>

    <!-- Floating Button (Mobile) -->
    <button
      v-if="['other', 'pm', 'co'].includes(role)"
      @click="handleOpenForm"
      class="fixed sm:hidden right-9 bottom-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm z-40 transition-all duration-800 ease-out delay-500"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <!-- Modal dengan animasi masuk -->
    <div v-if="openForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div
        class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200"
      >
        <ClientForm :client="selectedClient" :isEditMode="isEditMode" @close="handleCloseForm" />
      </div>
    </div>

    <!-- Main Content -->
    <div class="w-screen md:w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <!-- Table dengan animasi masuk -->
        <div
          class="overflow-x-auto bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >
          <table class="w-full text-left dark:text-white table-auto">
            <thead>
              <tr class="bg-indigo-50 dark:bg-gray-700">
                <th class="p-4">
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
                <th v-if="['other', 'pm', 'co'].includes(role)" class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Action</p>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in clients" :key="client.id">
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">{{ client.id }}</p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <a
                    :href="route('project.list', { client_id: client.id })"
                    class="block font-sans text-sm antialiased font-normal leading-normal text-indigo-600 dark:text-indigo-400 hover:underline"
                  >
                    {{ client.name }}
                  </a>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                    {{ getNameUser(client.creator) }}
                  </p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                    {{ getNameUser(client.updater) }}
                  </p>
                </td>
                <td v-if="['other', 'pm', 'co'].includes(role)" class="p-4 border-t border-blue-gray-50 align-middle">
                  <div class="flex gap-3 items-center text-sm">
                    <a
                      @click.prevent="handleEdit(client.id)"
                      class="cursor-pointer text-indigo-600 hover:text-indigo-800 dark:text-gray-200 dark:hover:text-gray-400 font-medium"
                    >
                      Edit
                    </a>
                    <a
                      @click.prevent="handleDelete(client.id)"
                      class="cursor-pointer text-red-600 hover:text-red-800 dark:text-gray-200 dark:hover:text-gray-400 font-medium"
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
  </AuthenticatedLayout>
</template>
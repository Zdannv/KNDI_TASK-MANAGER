<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UserForm from '@/Components/Form/User.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const openForm = ref(false);
const isEditMode = ref(false);
const selectedUser = ref(null);
const isLoaded = ref(false);

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const handleOpenForm = () => {
  isEditMode.value = false;
  selectedUser.value = null;
  openForm.value = true;
};

const handleCloseForm = () => {
  openForm.value = false;
  isEditMode.value = false;
  selectedUser.value = null;
};

const handleDelete = (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(route('user.destroy', id));
  }
};

const handleEdit = (id) => {
  const user = props.users.find(u => u.id === id);
  if (user) {
    isEditMode.value = true;
    selectedUser.value = user;
    openForm.value = true;
  }
};

const props = defineProps({
  users: {}
});

const formatRole = (role) => {
  const roles = {
    'pm': 'Project Manager',
    'pg': 'Programmer',
    'co': 'Communicator',
    'ds': 'Designer',
    'other': 'Other'
  };
  return roles[role] || role;
};
</script>

<template>
  <Head title="Users" />
  <AuthenticatedLayout>
    <!-- Header dengan animasi -->
    <template #header>
      <div
        class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
        :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
      >
        <h2 class="lg:col-span-2 font-semibold text-xl leading-tight">
          Users
        </h2>
        <div class="lg:col-span-4 flex justify-end">
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
      @click="handleOpenForm"
      class="fixed sm:hidden right-9 bottom-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm z-40 transition-all duration-800 ease-out delay-500"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <!-- Modal (tanpa animasi masuk, karena muncul setelah klik) -->
    <div v-if="openForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <UserForm :user="selectedUser" :isEditMode="isEditMode" @close="handleCloseForm" />
      </div>
    </div>

    <!-- Main Content -->
    <div class="w-screen md:w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <!-- Table with staggered animation -->
        <div
          class="overflow-x-auto bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >
          <table class="w-full text-left dark:text-white table-auto">
            <thead>
              <tr class="bg-indigo-50 dark:bg-gray-700">
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">No</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Name</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Email</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Role</p>
                </th>
                <th class="p-4">
                  <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">Action</p>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(user, idx) in users" :key="user.id">
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">{{ idx + 1 }}</p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">{{ user.name }}</p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">{{ user.email }}</p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">{{ formatRole(user.role) }}</p>
                </td>
                <td class="p-4 border-t border-blue-gray-50 align-middle">
                  <div class="flex gap-3 items-center text-sm">
                    <a @click.prevent="handleEdit(user.id)" class="cursor-pointer text-indigo-600 hover:text-indigo-800 dark:text-gray-200 dark:hover:text-gray-400 font-medium">
                      Edit
                    </a>
                    <a @click.prevent="handleDelete(user.id)" class="cursor-pointer text-red-600 hover:text-red-800 dark:text-gray-200 dark:hover:text-gray-400 font-medium">
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
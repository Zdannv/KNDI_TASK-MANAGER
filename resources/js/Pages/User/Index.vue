<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

// --- PROPS ---
const props = defineProps({
  users: {}
});

// --- STATE UI ---
const openForm = ref(false);
const isEditMode = ref(false);
const isLoaded = ref(false);
const showPassword = ref(false);        // State untuk mata password
const showConfirmPassword = ref(false); // State untuk mata confirm password

// --- FORM STATE ---
const form = useForm({
  id: null,
  name: '',
  email: '',
  role: 'pg',
  password: '',
  password_confirmation: '',
});

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

// --- COMPUTED VALIDATION ---
// Cek real-time apakah password match
const isPasswordMismatch = computed(() => {
  return form.password && form.password_confirmation && form.password !== form.password_confirmation;
});

// --- HANDLERS ---

const handleOpenForm = () => {
  isEditMode.value = false;
  form.reset(); // Kosongkan form
  form.clearErrors();
  showPassword.value = false;
  showConfirmPassword.value = false;
  openForm.value = true;
};

const handleCloseForm = () => {
  openForm.value = false;
  isEditMode.value = false;
  form.reset();
  form.clearErrors();
};

const handleEdit = (id) => {
  const user = props.users.find(u => u.id === id);
  if (user) {
    isEditMode.value = true;
    // Isi form dengan data user
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = ''; // Password dikosongkan saat edit
    form.password_confirmation = '';
    
    // Reset tampilan password
    showPassword.value = false;
    showConfirmPassword.value = false;
    openForm.value = true;
  }
};

const handleDelete = (id) => {
  if (confirm('Are you sure you want to delete this user?')) {
    router.delete(route('user.destroy', id));
  }
};

const handleSubmit = () => {
  // Cegah submit jika password tidak match
  if (isPasswordMismatch.value) return;

  if (isEditMode.value) {
    form.put(route('user.update', form.id), {
      onSuccess: () => handleCloseForm(),
    });
  } else {
    form.post(route('user.store'), {
      onSuccess: () => handleCloseForm(),
    });
  }
};

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
    
    <template #header>
      <div
        class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-1000 ease-out"
        :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
      >
        <h2 class="lg:col-span-2 font-semibold text-xl leading-tight">Users</h2>
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

    <button
      @click="handleOpenForm"
      class="fixed sm:hidden right-9 bottom-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm z-40 transition-all duration-800 ease-out delay-500"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <div v-if="openForm" class="fixed inset-0 z-50 px-2 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ isEditMode ? 'Edit User' : 'Create New User' }}
          </h2>
          <button @click="handleCloseForm" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div>
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div>
            <InputLabel for="role" value="Role" />
            <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
              <option value="pm">Project Manager</option>
              <option value="pg">Programmer</option>
              <option value="ds">Designer</option>
              <option value="co">Communicator</option>
              <option value="other">Other</option>
            </select>
            <InputError class="mt-2" :message="form.errors.role" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <div class="relative">
              <InputLabel for="password" :value="isEditMode ? 'Password (Optional)' : 'Password'" />
              <div class="relative mt-1">
                <TextInput
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="block w-full pr-10"
                  v-model="form.password"
                  :required="!isEditMode"
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="relative">
              <InputLabel for="password_confirmation" value="Confirm Password" />
              <div class="relative mt-1">
                <TextInput
                  id="password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  class="block w-full pr-10"
                  v-model="form.password_confirmation"
                  :required="!isEditMode || form.password.length > 0"
                  :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': isPasswordMismatch }"
                />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                  <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
              </div>
              <p v-if="isPasswordMismatch" class="text-sm text-red-600 mt-1 animate-pulse font-medium">
                Passwords do not match!
              </p>
            </div>
          </div>

          <div class="flex items-center justify-end mt-4 gap-3">
            <SecondaryButton @click="handleCloseForm">Cancel</SecondaryButton>
            <PrimaryButton 
              class="ms-4" 
              :class="{ 'opacity-25 cursor-not-allowed': form.processing || isPasswordMismatch }" 
              :disabled="form.processing || isPasswordMismatch"
            >
              {{ isEditMode ? 'Update User' : 'Create User' }}
            </PrimaryButton>
          </div>
        </form>

      </div>
    </div>
    <div class="w-screen md:w-full py-8 px-3">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
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
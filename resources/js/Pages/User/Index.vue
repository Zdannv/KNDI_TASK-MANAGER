<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
export default { layout: AuthenticatedLayout };
</script>

<script setup>
import Plus from '@/Components/Icon/Plus.vue';
import User from '@/Components/Icon/User.vue';
import Pagination from '@/Components/Pagination.vue';
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
const showPassword = ref(false);        
const showConfirmPassword = ref(false); 

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
const isPasswordMismatch = computed(() => {
  return form.password && form.password_confirmation && form.password !== form.password_confirmation;
});

// --- HANDLERS ---
const handleOpenForm = () => {
  isEditMode.value = false;
  form.reset(); 
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
  const user = props.users.data.find(u => u.id === id);
  if (user) {
    isEditMode.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = ''; 
    form.password_confirmation = '';
    
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
  
  <div v-if="$slots.header || true" class="mx-auto max-w-[100rem] sm:px-6 lg:px-8 mt-8">
        <div
          class="flex justify-between px-6 py-4 items-center text-gray-800 dark:text-gray-200 
                 bg-white/40 dark:bg-gradient-to-b dark:from-slate-700/50 dark:to-slate-800/60 backdrop-blur-xl border border-white/40 dark:border-white/20 
                 shadow-lg rounded-lg transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <div>
            <h2 class="font-bold text-xl leading-tight text-gray-800 dark:text-slate-100 drop-shadow-sm">Users Management</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage system access and roles.</p>
          </div>
          <div class="flex justify-end">
            <button
              @click="handleOpenForm"
              class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md hover:shadow-indigo-500/30 transition-all duration-300 transform hover:scale-105"
            >
              <Plus class="w-5 h-5" />
              <span class="hidden sm:inline font-bold text-sm">Add New User</span>
            </button>
          </div>
        </div>
    </div>

    <button
      @click="handleOpenForm"
      class="fixed sm:hidden right-6 bottom-6 border border-white/20 rounded-full p-4 text-white bg-indigo-600 shadow-xl z-40 transition-all duration-500 ease-out hover:scale-110 active:scale-95"
      :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
    >
      <Plus />
    </button>

    <div v-if="openForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity">
      <div class="bg-white/90 dark:bg-gradient-to-b dark:from-slate-800/90 dark:to-slate-950 backdrop-blur-2xl border border-white/50 dark:border-white/10 rounded-lg shadow-2xl max-w-lg w-full p-8 relative animate-in fade-in zoom-in duration-300">
        
        <div class="flex justify-between items-center mb-6">
          <div>
              <h2 class="text-xl font-bold text-gray-900 dark:text-slate-100">
                {{ isEditMode ? 'Edit User' : 'Create New User' }}
              </h2>
              <p class="text-sm text-gray-500 dark:text-slate-400">Fill in the details below.</p>
          </div>
          <button @click="handleCloseForm" class="p-2 bg-gray-100 dark:bg-slate-800 rounded-full text-gray-400 hover:text-red-500 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-5">
          <div>
            <InputLabel for="name" value="Full Name" />
            <TextInput id="name" type="text" class="mt-1 block w-full bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm dark:border-white/10 dark:text-white" v-model="form.name" required autofocus placeholder="John Doe" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>

          <div>
            <InputLabel for="email" value="Email Address" />
            <TextInput id="email" type="email" class="mt-1 block w-full bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm dark:border-white/10 dark:text-white" v-model="form.email" required placeholder="name@company.com" />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div>
            <InputLabel for="role" value="Role" />
            <div class="relative">
                <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 dark:border-white/10 bg-white/50 dark:bg-slate-900/50 dark:text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm backdrop-blur-sm transition cursor-pointer py-2.5">
                  <option value="pm">Project Manager</option>
                  <option value="pg">Programmer</option>
                  <option value="ds">Designer</option>
                  <option value="co">Communicator</option>
                  <option value="other">Other</option>
                </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="relative">
              <InputLabel for="password" :value="isEditMode ? 'Password (Optional)' : 'Password'" />
              <div class="relative mt-1">
                <TextInput id="password" :type="showPassword ? 'text' : 'password'" class="block w-full pr-10 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm dark:border-white/10 dark:text-white" v-model="form.password" :required="!isEditMode" placeholder="••••••••" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-indigo-500 transition">
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="relative">
              <InputLabel for="password_confirmation" value="Confirm Password" />
              <div class="relative mt-1">
                <TextInput id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" class="block w-full pr-10 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm dark:border-white/10 dark:text-white" v-model="form.password_confirmation" :required="!isEditMode && form.password" placeholder="••••••••" />
                <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-indigo-500 transition">
                  <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
          </div>
          <p v-if="isPasswordMismatch" class="text-sm text-red-500 font-bold dark:bg-red-500/10 p-2 rounded-lg animate-pulse">Passwords do not match!</p>

          <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-gray-100 dark:border-white/10">
            <SecondaryButton @click="handleCloseForm" :disabled="form.processing">Cancel</SecondaryButton>
            <PrimaryButton :disabled="form.processing || isPasswordMismatch" class="shadow-lg shadow-indigo-500/30">
               {{ form.processing ? 'Saving...' : (isEditMode ? 'Update User' : 'Create User') }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </div>

    <div class="w-full py-8">
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        
        <div 
          class="flex flex-col items-start transition-all duration-700 ease-out delay-100"
          :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
        >

          <div class="relative z-10 -mb-[1px]">
             <div class="w-fit px-6 h-12 bg-white/40 dark:bg-slate-700/50 dark:to-slate-800/60 backdrop-blur-xl border-t border-l border-r border-white/40 dark:border-white/20 rounded-t-lg shadow-sm relative flex items-center gap-3">
                <User class="w-5 h-5 text-indigo-600 dark:text-indigo-400 drop-shadow-sm" />
                <span class="font-bold text-gray-800 dark:text-slate-100 text-sm tracking-wide shadow-black drop-shadow-sm">All Users Data</span>
                <div class="absolute -bottom-[1px] left-0 right-0 h-[2px] bg-white/40 dark:bg-slate-800/80 z-20"></div>
             </div>
          </div>
          
          <div
            class="w-full overflow-x-auto bg-white/40 dark:bg-gradient-to-b dark:from-slate-800/60 dark:to-slate-950/80 backdrop-blur-xl border border-white/40 dark:border-white/20 shadow-xl rounded-b-lg rounded-tr-lg relative z-0"
          >
            <table class="w-full text-left dark:text-slate-200 table-auto border-collapse">
              <thead>
                <tr class="bg-white/50 dark:bg-slate-800/90 backdrop-blur-md border-b border-white/20 dark:border-white/10">
                  <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-sm uppercase tracking-wider">No</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-sm uppercase tracking-wider">Name</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-sm uppercase tracking-wider">Email</th>
                  <th class="p-5 font-semibold text-gray-600 dark:text-slate-400 text-sm uppercase tracking-wider">Role</th>
                  <th class="p-5 text-center font-semibold text-gray-600 dark:text-slate-400 text-sm uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-white/20 dark:divide-white/5">
                <tr v-for="(user, idx) in users.data" :key="user.id" class="hover:bg-white/30 dark:hover:bg-white/5 transition duration-200">
                  <td class="p-5 align-middle text-sm text-gray-500 dark:text-slate-400">{{ (users.current_page - 1) * users.per_page + idx + 1 }}</td>
                  
                  <td class="p-5 align-middle">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100/50 dark:bg-slate-800 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-xs border border-indigo-100/50 dark:border-slate-700">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="font-bold text-gray-800 dark:text-slate-200">{{ user.name }}</span>
                      </div>
                  </td>
                  <td class="p-5 align-middle text-sm text-gray-600 dark:text-slate-400 font-mono">{{ user.email }}</td>
                  <td class="p-5 align-middle">
                     <span :class="{
                        'bg-purple-100/50 text-purple-700 border-purple-200/50 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800/30': user.role === 'pm',
                        'bg-blue-100/50 text-blue-700 border-blue-200/50 dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800/30': user.role === 'pg',
                        'bg-pink-100/50 text-pink-700 border-pink-200/50 dark:bg-pink-900/30 dark:text-pink-400 dark:border-pink-800/30': user.role === 'ds',
                        'bg-orange-100/50 text-orange-700 border-orange-200/50 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800/30': user.role === 'co',
                        'bg-gray-100/50 text-gray-700 border-gray-200/50 dark:bg-slate-800/30 dark:text-slate-300 dark:border-slate-700/30': user.role === 'other',
                     }" class="px-3 py-1 rounded-full text-xs font-bold border backdrop-blur-sm shadow-sm">
                        {{ formatRole(user.role) }}
                     </span>
                  </td>
                  <td class="p-5 align-middle">
                    <div class="flex gap-3 justify-center items-center text-sm">
                      <button @click="handleEdit(user.id)" class="p-1.5 rounded-lg text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition tooltip-trigger" title="Edit">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                          </svg>
                      </button>
                      <button @click="handleDelete(user.id)" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition tooltip-trigger" title="Delete">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                          </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="mt-6 flex justify-end w-full">
             <Pagination :links="users.links" />
          </div>

        </div>

      </div>
    </div>

  </template>

<style scoped>
.custom-scrollbar {
  scrollbar-gutter: stable;
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.1) transparent;
}
.custom-scrollbar::-webkit-scrollbar {
  height: 6px;
  width: 6px;
  display: block;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; 
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: rgba(255, 255, 255, 0.2);
}
</style>
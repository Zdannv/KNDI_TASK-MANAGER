<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Attendance from '@/Components/Icon/attendance.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({
  canLogin: Boolean,
  laravelVersion: String,
  phpVersion: String,
  status: String,
  canResetPassword: Boolean,
});

const isLoaded = ref(false);
const showPassword = ref(false);

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password')
  })
}

onMounted(() => {
  setTimeout(() => isLoaded.value = true, 100);
});
</script>

<template>
  <Head title="Project & HR Management" />
  <div class="base-screen min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-5 min-h-screen">
      <div class="lg:col-span-3 flex flex-col justify-center px-8 py-12 lg:px-16 overflow-y-auto">
        <div 
          class="max-w-4xl transition-all duration-1000 ease-out flex flex-col justify-center items-center text-center"
          :class="{ 'translate-x-0 opacity-100': isLoaded, '-translate-x-8 opacity-0': !isLoaded }"
        >
          <div class="relative group w-fit mb-8">
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full blur-lg opacity-70 group-hover:opacity-100 transition duration-500"></div>
            <div class="relative p-0 bg-white dark:bg-slate-900 rounded-full ring-1 ring-slate-200 dark:ring-slate-800">
              <ApplicationLogo class="w-16 h-16 scale-125" />
            </div>
          </div>

          <h1 class="text-4xl md:text-6xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-[#2876BC] to-[#50B2D8] mb-4">
            Project & Task Management System
          </h1>
          <p class="text-xl font-medium text-blue-600 dark:text-blue-400 mb-6">
            Kyodo News Digital Indonesia
          </p>
          <p class="max-w-2xl text-base text-slate-600 dark:text-slate-400 leading-relaxed mb-12">
            Kelola project, tugas, waktu kerja, skill karyawan, dan impor data secara efisien dalam satu platform terintegrasi.
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-3xl">
            <div class="feature-container group flex flex-col items-center">
              <div class="feature-container-icon from-indigo-500 to-blue-500">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
              </div>
              <h3 class="text-center">Project & Task</h3>
              <p class="text-center">Buat, assign, dan pantau tugas dengan deadline, PR, dan tim terintegrasi.</p>
            </div>

            <div class="feature-container group flex flex-col items-center">
              <div class="feature-container-icon from-purple-500 to-pink-500">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-center">Logtime & Export</h3>
              <p class="text-center">Catat waktu kerja per tugas dan ekspor data ke Excel dengan mudah.</p>
            </div>
          </div>

          <footer class="mt-12 text-sm text-slate-500 dark:text-slate-400 text-center">
            <p>© {{ new Date().getFullYear() }} Project & Task Management System Indonesia.</p>
            <p class="mt-1 text-xs opacity-75">Laravel v{{ laravelVersion }} • PHP v{{ phpVersion }}</p>
          </footer>
        </div>
      </div>

      <div class="lg:col-span-2 bg-white/50 dark:bg-slate-900/50 backdrop-blur-xl border-l border-slate-200 dark:border-slate-800 flex flex-col justify-center px-8 py-12">
        <div 
          class="w-full max-w-md mx-auto space-y-8 transition-all duration-700 delay-300"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <div class="text-center lg:text-left">
            <!-- <h2 class="text-4xl md:text-6xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 mb-4 text-center">LOGIN</h2> -->
          </div>

          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <InputLabel for="email" value="Email" />
              <TextInput
                id="email"
                type="email"
                class="mt-1 block w-full"
                v-model="form.email"
                required
                autofocus
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
              <InputLabel for="password" value="Password" />
              <div class="relative mt-1">
                <TextInput
                  id="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="block w-full pr-10"
                  v-model="form.password"
                  required
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                </button>
              </div>
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
              <label class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Ingat saya</span>
              </label>
            </div>

            <div class="flex flex-col gap-4">
              <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                class="w-full justify-center py-4 text-lg bg-[#2876BC]"
              >
                Masuk
              </PrimaryButton>

              <div class="relative py-2">
                <div class="absolute inset-0 flex items-center"><span class="w-full border-t border-slate-300"></span></div>
                <div class="relative flex justify-center text-xs uppercase"><span class="bg-white dark:bg-slate-900 px-2 text-slate-500">Atau</span></div>
              </div>

              <Link
                href="/recognize"
                class="flex items-center justify-center gap-3 px-8 py-4 text-lg font-semibold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-800 border-2 border-indigo-100 dark:border-slate-700 rounded-2xl hover:border-indigo-600 transition-all duration-300"
              >
                <Attendance class="w-6 h-6" />
                <span>Presensi Kehadiran</span>
              </Link>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</template>

<style>
.base-screen {
  @apply bg-gradient-to-br from-slate-50 via-white to-indigo-50 dark:from-slate-950 dark:via-black dark:to-indigo-950 text-slate-900 dark:text-slate-100;
}
.feature-container {
  @apply p-6 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-indigo-500 transition-all duration-300;
}
.feature-container-icon {
  @apply w-12 h-12 bg-gradient-to-br rounded-xl flex items-center justify-center mb-4 shadow-sm;
}
.feature-container h3 {
  @apply text-lg font-semibold text-slate-900 dark:text-white;
}
.feature-container p {
  @apply mt-2 text-sm text-slate-600 dark:text-slate-400;
}
</style>
<style scoped>
.group:hover svg {
  transform: scale(1.1);
}
</style>
<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Attendance from '@/Components/Icon/attendance.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
  canLogin: Boolean,
  laravelVersion: String,
  phpVersion: String,
});

// Animasi masuk halus
const isLoaded = ref(false);
onMounted(() => {
  setTimeout(() => isLoaded.value = true, 100);
});
</script>

<template>
  <Head title="Project & HR Management" />
  <div class="base-screen">
    <div class="relative flex flex-col items-center justify-center min-h-screen px-6 py-12 lg:px-8">
      <div class="w-full max-w-5xl mx-auto text-center space-y-12">

        <!-- Logo & Company Name -->
        <div
          class="flex flex-col items-center space-y-6 transition-all duration-1000 ease-out"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <!-- Logo SVG -->
          <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full blur-lg opacity-70 group-hover:opacity-100 transition duration-500"></div>
            <div class="relative p-0 bg-white dark:bg-slate-900 rounded-full ring-1 ring-slate-200 dark:ring-slate-800">
              <ApplicationLogo class="w-24 scale-150" />
            </div>
          </div>

          <div>
            <h1 class="text-5xl md:text-6xl !leading-tight font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
              Project & Task Management System
            </h1>
            <p class="text-lg font-medium text-indigo-600 dark:text-indigo-400">
              Kyodo News Digital Indonesia
            </p>
          </div>

          <p class="mt-4 max-w-2xl mx-auto text-base text-slate-600 dark:text-slate-400 leading-relaxed">
            Kelola project, tugas, waktu kerja, skill karyawan, dan impor data secara efisien dalam satu platform terintegrasi.
          </p>
        </div>

        <!-- CTA Buttons -->
        <div
          class="flex flex-col sm:flex-row gap-4 justify-center items-center transition-all duration-700 delay-300"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <Link
            v-if="canLogin && $page.props.auth.user"
            :href="route('dashboard')"
            class="group inline-flex items-center gap-3 px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/50"
          >
            <span>Dashboard</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </Link>

          <template v-else-if="canLogin">
            <Link
              :href="route('login')"
              class="group inline-flex items-center gap-3 px-8 py-4 text-lg font-semibold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-800 border-2 border-indigo-600 dark:border-indigo-500 rounded-full shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/50"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              <span>Masuk</span>
            </Link>
          </template>

          <Link
            href="/recognize"
            class="group inline-flex items-center gap-3 px-4 py-4 text-lg font-semibold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-800 border-2 border-indigo-600 dark:border-indigo-500 rounded-full shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300 focus:outline-none focus-visible:ring-4 focus-visible:ring-indigo-500/50"
          >
           <Attendance /> 
          </Link>
        </div>

        <!-- Features Grid (Sesuai Aplikasi Nyata) -->
        <div
          class="mt-20 grid grid-cols-1 md:grid-cols-2 gap-6 text-left transition-all duration-700 delay-500"
          :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <!-- Project & Task Management -->
          <div class="feature-container group">
            <div class="feature-container-icon from-indigo-500 to-blue-500 ">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
              </svg>
            </div>
            <h3>Project & Task</h3>
            <p>
              Buat, assign, dan pantau tugas dengan deadline, PR, komentar, dan tim terintegrasi.
            </p>
          </div>

          <!-- Time Tracking & Logtime -->
          <div class="feature-container group">
            <div class="feature-container-icon from-purple-500 to-pink-500">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3>Logtime & Export</h3>
            <p>
              Catat waktu kerja per tugas, filter per user/tanggal, dan ekspor ke Excel.
            </p>
          </div>

          <!-- HR & Skills Management -->
          <div class="hidden feature-container group">
            <div class="feature-container-icon from-pink-500 to-rose-500">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <h3>HR & Skills</h3>
            <p>
              Kelola karyawan, skill, role, dan impor data massal via Excel.
            </p>
          </div>
        </div>

        <!-- Footer -->
        <footer class="mt-20 text-center text-sm text-slate-500 dark:text-slate-400">
          <p>
            © {{ new Date().getFullYear() }} Project & Task Management System Indonesia.
            Laravel v{{ laravelVersion }} • PHP v{{ phpVersion }}
          </p>
        </footer>

      </div>
    </div>
  </div>
</template>

<style>
.base-screen {
  @apply min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-200 dark:from-slate-950 dark:via-black
  dark:to-indigo-900 text-slate-900 dark:text-slate-100 overflow-hidden;
}
.feature-container {
  @apply p-6 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm rounded-2xl border border-slate-200
  dark:border-slate-800 hover:border-indigo-500 dark:hover:border-indigo-500 transition-all duration-300;
}
.feature-container-icon {
  @apply w-12 h-12 bg-gradient-to-br rounded-xl flex items-center justify-center mb-4;
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
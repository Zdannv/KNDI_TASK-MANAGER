<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
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
            <svg width="32px" height="32px" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000">
              <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
              <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
              <g id="SVGRepo_iconCarrier">
                <path d="M241.371429 548.571429a358.4 285.257143 90 1 0 570.514285 0 358.4 285.257143 90 1 0-570.514285 0Z" fill="#f66151"></path>
                <path d="M58.514286 526.628571c-14.628571 0-21.942857-14.628571-21.942857-29.257142 0-36.571429 14.628571-117.028571 65.828571-204.8 7.314286-14.628571 21.942857-14.628571 36.571429-7.314286s21.942857 29.257143 7.314285 36.571428c-43.885714 87.771429-58.514286 153.6-58.514285 182.857143 0 14.628571-14.628571 21.942857-29.257143 21.942857zM160.914286 285.257143c-7.314286 0-14.628571 0-14.628572-7.314286-14.628571-7.314286-14.628571-29.257143-7.314285-36.571428 51.2-65.828571 117.028571-117.028571 182.857142-153.6 14.628571-7.314286 29.257143 0 36.571429 14.628571 7.314286 14.628571 0 29.257143-14.628571 36.571429-58.514286 29.257143-109.714286 73.142857-160.914286 131.657142 0 7.314286-7.314286 14.628571-21.942857 14.628572zM424.228571 109.714286c-14.628571 0-21.942857-7.314286-29.257142-21.942857 0-14.628571 7.314286-29.257143 21.942857-36.571429 43.885714-14.628571 95.085714-14.628571 146.285714-14.628571 21.942857 7.314286 29.257143 21.942857 29.257143 36.571428s-14.628571 29.257143-29.257143 29.257143c-43.885714-7.314286-87.771429-7.314286-138.971429 7.314286 7.314286 0 7.314286 0 0 0zM775.314286 160.914286c-7.314286 0-7.314286 0-14.628572-7.314286-43.885714-29.257143-95.085714-43.885714-146.285714-58.514286-7.314286 7.314286-21.942857-7.314286-14.628571-21.942857 0-14.628571 14.628571-21.942857 29.257142-21.942857 58.514286 14.628571 109.714286 29.257143 160.914286 65.828571 14.628571 7.314286 14.628571 21.942857 7.314286 36.571429 0 7.314286-14.628571 7.314286-21.942857 7.314286zM994.742857 599.771429c-14.628571 0-29.257143-14.628571-29.257143-29.257143-7.314286-160.914286-58.514286-285.257143-146.285714-365.714286-14.628571-7.314286-14.628571-29.257143 0-36.571429 7.314286-14.628571 29.257143-14.628571 36.571429 0 95.085714 87.771429 153.6 226.742857 160.914285 402.285715 7.314286 14.628571-7.314286 29.257143-21 .942857 29.257143z" fill="#e01b24"></path><path d="M826.514286 914.285714h-7.314286c-14.628571-7.314286-21.942857-21.942857-14.628571-36.571428 0 0 14.628571-43.885714 36.571428-131.657143 21.942857-87.771429 7.314286-204.8 7.314286-204.8 0-124.342857-73.142857-241.371429-190.171429-292.571429-14.628571-7.314286-21.942857-21.942857-14.628571-36.571428 7.314286-14.628571 21.942857-21.942857 36.571428-14.628572 131.657143 58.514286 219.428571 190.171429 219.428572 336.457143 0 0 14.628571 124.342857-7.314286 219.428572-21.942857 95.085714-43.885714 138.971429-43.885714 138.971428-7.314286 14.628571-14.628571 21.942857-21.942857 21.942857zM336.457143 277.942857c-7.314286 0-14.628571-7.314286-21.942857-14.628571-7.314286-7.314286 0-21.942857 7.314285-29.257143 80.457143-58.514286 175.542857-80.457143 270.628572-58.514286 14.628571 0 21.942857 14.628571 21.942857 29.257143s-14.628571 21.942857-29.257143 21.942857c-80.457143-14.628571-160.914286 0-226.742857 51.2h-21.942857zM65.828571 694.857143c-14.628571 0-21.942857-7.314286-29.257142-21.942857 0-14.628571 7.314286-29.257143 21.942857-29.257143 0 0 29.257143-7.314286 58.514285-36.571429 21.942857-21.942857 43.885714-58.514286 51.2-73.142857 0-80.457143 29.257143-160.914286 80.457143-226.742857 7.314286-14.628571 29.257143-21.942857 36.571429-7.314286 14.628571 7.314286 14.628571 21.942857 7.314286 36.571429-43.885714 58.514286-73.142857 124.342857-73.142858 197.485714v7.314286c0 7.314286-21.942857 73.142857-65.828571 102.4-43.885714 36.571429-80.457143 51.2-80.457143 51.2H65.828571z" fill="#e01b24"></path><path d="M124.342857 789.942857c-7.314286 0-14.628571-7.314286-21.942857-14.628571-7.314286-14.628571-7.314286-29.257143 7.314286-36.571429 0 0 95.085714-65.828571 124.342857-87.771428 7.314286-7.314286 29.257143-43.885714 36.571428-80.457143 0-58.514286 36.571429-160.914286 73.142858-204.8 0 0 14.628571-21.942857 51.2-43.885715 29.257143-14.628571 65.828571-29.257143 102.4-29.257142 14.628571-7.314286 29.257143 0 29.257142 14.628571s-7.314286 29.257143-21.942857 29.257143c-29.257143 0-51.2 7.314286-73.142857 21.942857-29.257143 21.942857-43.885714 36.571429-43.885714 36.571429-29.257143 36.571429-65.828571 131.657143-65.828572 182.857142V585.142857c-7.314286 21.942857-29.257143 87.771429-58.514285 117.028572-21.942857 14.628571-117.028571 80.457143-124.342857 87.771428h-14.628572zM702.171429 438.857143c-7.314286 0-14.628571 0-21.942858-7.314286-21.942857-36.571429-58.514286-65.828571-102.4-80.457143-14.628571-7.314286-21.942857-21.942857-14.628571-36.571428 0-14.628571 21.942857-21.942857 36.571429-14.628572 51.2 21.942857 102.4 58.514286 131.657142 95.085715 7.314286 14.628571 7.314286 29.257143-7.314285 36.571428-7.314286 7.314286-14.628571 7.314286-21.942857 7.314286zM724.114286 716.8s-7.314286 0 0 0c-21.942857-7.314286-29.257143-21.942857-29.257143-36.571429 7.314286-21.942857 7.314286-36.571429 14.628571-43.885714 7.314286-29.257143 7.314286-95.085714 7.314286-95.085714 0-14.628571 0-36.571429-7.314286-51.2 0-14.628571 7.314286-29.257143 21.942857-36.571429 14.628571-7.314286 29.257143 7.314286 36.571429 21.942857 7.314286 21.942857 7.314286 43.885714 7.314286 65.828572 0 0-7.314286 73.142857-7.314286 102.4 0 7.314286-7.314286 21.942857-7.314286 43.885714-14.628571 14.628571-21.942857 29.257143-36.571428 29.257143z" fill="#e01b24"></path><path d="M665.6 928.914286H658.285714c-14.628571-7.314286-21.942857-21.942857-14.628571-36.571429 0 0 21.942857-80.457143 43.885714-153.6 0-14.628571 21.942857-21.942857 36.571429-21.942857s21.942857 14.628571 21.942857 36.571429c-14.628571 73.142857-43.885714 153.6-43.885714 153.6-14.628571 14.628571-21.942857 21.942857-36.571429 21.942857z" fill="#e01b24"></path><path d="M548.571429 936.228571c-21.942857-7.314286-29.257143-21.942857-29.257143-36.571428l80.457143-329.142857c14.628571-43.885714-14.628571-87.771429-58.514286-102.4-43.885714-7.314286-95.085714 21.942857-102.4 65.828571l-73.142857 212.114286-146.285715 117.028571c-14.628571 14.628571-36.571429 14.628571-43.885714 0-7.314286-14.628571-7.314286-29.257143 7.314286-36.571428l138.971428-109.714286 65.828572-190.171429c0-36.571429 29.257143-73.142857 58.514286-87.771428 29.257143-21.942857 73.142857-29.257143 109.714285-14.628572C629.028571 438.857143 680.228571 512 658.285714 592.457143l-80.457143 329.142857c0 7.314286-14.628571 14.628571-29.257142 14.628571z" fill="#e01b24"></path><path d="M387.657143 943.542857h-14.628572c-14.628571-7.314286-14.628571-21.942857-7.314285-36.571428l80.457143-146.285715 43.885714-204.8c0-14.628571 14.628571-21.942857 29.257143-21.942857s21.942857 14.628571 21.942857 29.257143l-43.885714 219.428571-87.771429 153.6s-14.628571 7.314286-21.942857 7.314286z" fill="#e01b24"></path>
              </g>
            </svg>
            
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
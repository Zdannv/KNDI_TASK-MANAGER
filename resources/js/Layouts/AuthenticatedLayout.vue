<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Menus from "@/Components/Menus.vue";
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Logout from '@/Components/Icon/Logout.vue';
import Toast from '@/Components/Toast.vue';

defineProps({
    title: String,
});

// --- PERBAIKAN LOGIKA SIDEBAR ---
// Ambil state dari localStorage
const sidebarState = localStorage.getItem('sidebarOpen');

// Logika: Jika sidebarState null (user baru/belum ada cache), set TRUE (Terbuka).
// Jika ada isinya ('true'/'false'), ikuti isinya.
const openMenus = ref(sidebarState === null ? true : sidebarState === 'true');

watch(openMenus, (newValue) => {
  localStorage.setItem('sidebarOpen', newValue);
});
// -------------------------------

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-200 dark:from-slate-900 dark:via-black dark:to-indigo-950">
        <Head :title="title" />

        <nav class="md:hidden fixed top-0 left-0 w-full bg-indigo-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 z-50 shadow-sm">
            <div class="flex justify-between items-center px-4 h-16">
                <div class="p-2">
                    <Hamburger v-model="openMenus" />
                </div>
                <form @submit.prevent="logout" class="flex">
                    <button class="flex items-center text-gray-700 dark:text-gray-300 hover:text-gray-500 transition-colors">
                        <div class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-900">
                            <Logout />
                        </div>
                    </button>
                </form>
            </div>

            <div v-if="openMenus" class="bg-indigo-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-xl">
                <div class="flex flex-col p-4 space-y-4">
                    <Menus v-model="openMenus" />
                </div>
            </div>
        </nav>

        <nav 
            class="hidden md:block fixed top-0 left-0 h-screen bg-indigo-50 dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 z-50 transition-all duration-300 ease-in-out shadow-lg" 
            :class="{'w-52': openMenus, 'w-16': !openMenus}"
        >
            <div class="flex flex-col justify-between h-full">
                <div class="flex flex-col items-center">
                    <div 
                        :class="{'justify-between': openMenus, 'justify-center': !openMenus}" 
                        class="w-full py-4 px-3 flex items-center transition-all"
                    >
                        <span 
                            v-if="openMenus" 
                            class="text-gray-700 dark:text-gray-200 font-bold ml-2 text-lg tracking-wide opacity-100 transition-opacity duration-300"
                        >
                            Menu
                        </span>

                        <Hamburger v-model="openMenus" />
                    </div>
                    <div class="space-y-6 pt-4 w-full flex flex-col items-center">
                        <Menus v-model="openMenus" />
                    </div>
                </div>

                <form @submit.prevent="logout" class="flex justify-center pb-6">
                    <button class="w-full px-2 flex items-center justify-center dark:text-gray-300 hover:text-red-500 transition-colors group">
                        <div :class="{'me-2': openMenus}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 group-hover:border-red-500">
                            <Logout />
                        </div>
                        <span v-if="openMenus" class="font-medium whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </div>
        </nav>

        <div 
            class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out pt-16 md:pt-0" 
            :class="{'md:pl-52': openMenus, 'md:pl-16': !openMenus}"
        >
            <header v-if="$slots.header" class="pt-6 md:pt-12 px-3">
                <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main class="flex-1 py-6 px-3">
                <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>

        <Toast />
    </div>
</template>
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

const openMenus = ref(localStorage.getItem('sidebarOpen') === 'true');

watch(openMenus, (newValue) => {
  localStorage.setItem('sidebarOpen', newValue);
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-200 dark:from-slate-900 dark:via-black dark:to-indigo-950">
            <!-- Navbar for mobile (hidden on larger screens) -->
            <nav class="md:hidden fixed top-0 left-0 w-full bg-indigo-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 z-10">
                <div class="flex justify-between items-center px-4">
                    <!-- Hamburger Menu -->
                    <div class="p-2">
                        <Hamburger v-model="openMenus" />
                    </div>
                    <!-- Logout Button -->
                    <form @submit.prevent="logout" class="flex">
                        <button class="flex items-center text-gray-700 dark:text-gray-300 hover:text-gray-500">
                            <div class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-900">
                                <Logout />
                            </div>
                        </button>
                    </form>
                </div>

                <!-- Mobile Menu (shown when hamburger is clicked) -->
                <div v-if="openMenus" class="bg-indigo-50 dark:bg-gray-800 relative">
                    <div class="flex flex-col p-4 space-y-4">
                        <!-- Navigation Links -->
                        <Menus v-model="openMenus" />
                    </div>
                </div>
            </nav>

            <!-- Sidebar for desktop (hidden on mobile) -->
            <nav class="hidden md:block fixed top-0 left-0 h-screen bg-indigo-50 dark:bg-gray-800 shadow-lg shadow-indigo-500 dark:shadow-indigo-800 border-r border-gray-100 dark:border-gray-700 transition-all duration-300 ease-in-out" :class="{'w-52': openMenus, 'w-16': !openMenus}">
                <div class="flex flex-col justify-between h-full">
                    <div :class="{'w-52': openMenus, 'w-16': !openMenus}" class="flex flex-col items-center transition-all duration-300 ease-in-out">
                        <!-- Hamburger -->
                        <div :class="{'justify-end': openMenus, 'justify-center': !openMenus}" class="w-full py-4 px-2 flex">
                            <Hamburger v-model="openMenus" />
                        </div>
                        <!-- Navigation Links -->
                        <div class="space-y-8 pt-4 w-fit flex flex-col">
                            <Menus v-model="openMenus" />
                        </div>
                    </div>
                    <form @submit.prevent="logout" class="flex justify-center">
                        <button class="w-fit p-4 flex items-center dark:text-gray-300 hover:text-gray-500">
                            <div :class="{'me-2': openMenus}" class="p-[5px] w-fit border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
                                <Logout />
                            </div>
                            <span :class="{'hidden': !openMenus, 'inline-flex': openMenus}">Logout</span>
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Main Content Wrapper -->
            <div class="flex-1 flex flex-col pt-16 md:pt-0 md:ml-16" :class="{'md:ml-52': openMenus}">
                <!-- Page Heading -->
                <header v-if="$slots.header" class="hidden sm:block pt-12 px-3">
                    <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Main Content (Scrollable) -->
                <main class="flex-1 overflow-y-auto">
                    <slot />
                </main>
            </div>
        </div>
        <Toast />
    </div>
</template>
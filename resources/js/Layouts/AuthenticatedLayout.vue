<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import Menus from "@/Components/Menus.vue";
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Logout from '@/Components/Icon/Logout.vue';
import Toast from '@/Components/Toast.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    title: String,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const getInitialSidebarState = () => {
    if (typeof window !== 'undefined') {
        const savedState = localStorage.getItem('sidebarOpen');
        if (savedState !== null) {
            return savedState === 'true';
        }
        // 2. Jika user baru/pertama kali login:
        // Desktop (>= 768px) -> Default BUKA (True)
        // Mobile (< 768px)  -> Default TUTUP (False)
        return window.innerWidth >= 768;
    }
    return true; // Fallback
};

const openMenus = ref(getInitialSidebarState());

watch(openMenus, (newValue) => {
  localStorage.setItem('sidebarOpen', newValue);
});

const logout = () => {
    router.post(route('logout'));
};

const getInitials = (name) => {
    if (!name) return 'U';
    const names = name.split(' ');
    let initials = names[0].substring(0, 1).toUpperCase();
    if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
    }
    return initials;
};

const getRoleLabel = (role) => {
    const roles = {
        'pm': 'Project Manager',
        'pg': 'Programmer',
        'ds': 'Designer',
        'co': 'Communicator',
        'other': 'Admin'
    };
    return roles[role] || role;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-200 dark:from-slate-900 dark:via-black dark:to-indigo-950 font-sans">
        <Head :title="title" />

        <nav class="md:hidden fixed top-0 left-0 w-full bg-indigo-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 z-50 shadow-sm">
            <div class="flex justify-between items-center px-4 h-16">
                <div class="flex items-center gap-3">
                    <button @click="openMenus = !openMenus" class="p-2 text-gray-500 rounded-md hover:bg-gray-100 focus:outline-none">
                        <Hamburger :show="openMenus" />
                    </button>
                    <img src="/icon_kndi.svg" alt="KNDI Logo" class="w-8 h-8" />
                    <span class="font-bold text-lg text-gray-800 dark:text-white tracking-tight">KNDI Task</span>
                </div>
            </div>

            <div v-if="openMenus" class="bg-indigo-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-xl overflow-y-auto max-h-[80vh]">
                <div class="flex flex-col p-4 space-y-4">
                    <Menus v-model="openMenus" />
                    
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-white/60 dark:bg-gray-700/50">
                            <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-sm shrink-0">
                                {{ getInitials(user.name) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ getRoleLabel(user.role) }}</p>
                            </div>
                            <button @click="logout" class="p-2 text-red-500 hover:bg-red-50 rounded-md transition">
                                <Logout class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <nav 
            class="hidden md:flex fixed top-0 left-0 h-screen bg-indigo-50 dark:bg-gray-800 border-r border-gray-100 dark:border-gray-700 z-50 transition-all duration-300 ease-in-out shadow-lg flex-col" 
            :class="{'w-52': openMenus, 'w-16': !openMenus}"
        >
            <div 
                :class="{'justify-between pl-4 pr-2': openMenus, 'justify-center px-0': !openMenus}" 
                class="w-full h-16 flex items-center transition-all border-b border-indigo-100 dark:border-gray-700 shrink-0"
            >
                <div v-if="openMenus" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <img src="/icon_kndi.svg" alt="KNDI Logo" class="w-8 h-8 shrink-0" />
                    <span class="text-gray-800 dark:text-gray-200 font-bold text-lg tracking-tight">KNDI</span>
                </div>
                <div v-else class="absolute left-4 opacity-0 pointer-events-none"></div>

                <Hamburger v-model="openMenus" />
            </div>

            <div class="flex-1 overflow-y-auto py-4 px-3 flex flex-col items-center gap-2 no-scrollbar">
                <Menus v-model="openMenus" />
            </div>
        </nav>

        <div 
            class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out pt-16 md:pt-0" 
            :class="{'md:pl-52': openMenus, 'md:pl-16': !openMenus}"
        >
            <header class="hidden md:flex bg-white/80 dark:bg-gray-800/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-700 h-16 items-center justify-end px-6 sticky top-0 z-40">
                
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div class="text-right mr-3">
                                <div class="text-sm font-bold text-gray-800 dark:text-white">{{ user.name }}</div>
                                <div class="text-[10px] uppercase tracking-wide text-gray-500">{{ getRoleLabel(user.role) }}</div>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-700 shadow-sm">
                                {{ getInitials(user.name) }}
                            </div>
                            <svg class="ms-2 -me-0.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <div class="px-4 py-2 text-xs text-gray-400 border-b dark:border-gray-600 mb-1">
                            {{ user.email }}
                        </div>
                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20"> 
                            Log Out 
                        </DropdownLink>
                    </template>
                </Dropdown>
            </header>

            <div v-if="$slots.header" class="pt-6 px-3 md:px-8">
                <slot name="header" />
            </div>

            <main class="flex-1 py-6 px-3 md:px-8">
                <slot />
            </main>
        </div>

        <Toast />
    </div>
</template>
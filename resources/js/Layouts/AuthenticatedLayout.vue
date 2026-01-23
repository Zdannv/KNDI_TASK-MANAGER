<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import Menus from "@/Components/Menus.vue";
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Logout from '@/Components/Icon/Logout.vue';
import Pen from '@/Components/Icon/Pen.vue';
import Toast from '@/Components/Toast.vue';
import Modal from '@/Components/Modal.vue'; // Pastikan komponen Modal ada
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Props
defineProps({
    title: String,
});

// State & Hooks
const page = usePage();
const user = computed(() => page.props.auth.user);

// --- Logic Sidebar Kanan (Profile) ---
const showProfilePanel = ref(false);

// Logic Edit Nama
const isEditingName = ref(false);
const nameForm = useForm({
    name: '',
    email: '',
    avatar: ''
});

const startEditingName = () => {
    nameForm.name = user.value.name;
    nameForm.email = user.value.email; // Email harus dikirim untuk validasi unique
    nameForm.avatar = user.value.avatar;
    isEditingName.value = true;
};

const saveName = () => {
    nameForm.patch(route('profile.update'), {
        preserveScroll: true,
        preserveState: true, 
        onSuccess: () => {
            isEditingName.value = false;
        }
    });
};

const cancelEditingName = () => {
    isEditingName.value = false;
    nameForm.reset();
};

// Logic Edit Avatar
const showAvatarModal = ref(false);
const avatarForm = useForm({
    name: '',
    email: '',
    avatar: ''
});

// Daftar Asset Profile (Pastikan file ini ada di public/avatars/)
const avatarAssets = [
    '/avatars/avatar-1.jpeg',
    '/avatars/avatar-2.jpg',
    '/avatars/avatar-3.jpeg',
    '/avatars/avatar-4.jpg',
    '/avatars/avatar-5.jpeg',
    '/avatars/avatar-6.jpeg',
];

const selectedAvatarTemp = ref('');

const openAvatarModal = () => {
    selectedAvatarTemp.value = user.value.avatar || avatarAssets[0];
    showAvatarModal.value = true;
};

const selectAvatar = (assetPath) => {
    selectedAvatarTemp.value = assetPath;
};

const saveAvatar = () => {
    avatarForm.name = user.value.name;
    avatarForm.email = user.value.email;
    avatarForm.avatar = selectedAvatarTemp.value;

    avatarForm.patch(route('profile.update'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showAvatarModal.value = false;
        }
    });
};

// --- Logic Sidebar Kiri (Navigation) ---
const getInitialSidebarState = () => {
    if (typeof window !== 'undefined') {
        const savedState = localStorage.getItem('sidebarOpen');
        if (savedState !== null) {
            return savedState === 'true';
        }
        return window.innerWidth >= 768;
    }
    return true;
};

const openMenus = ref(getInitialSidebarState());

watch(openMenus, (newValue) => {
  localStorage.setItem('sidebarOpen', newValue);
});

// --- Actions & Utils ---
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
    <div class="min-h-screen bg-[#F2F5FA] dark:bg-slate-900 font-sans text-slate-600 dark:text-slate-300">
        <Head :title="title" />

        <div 
            v-if="showProfilePanel" 
            @click="showProfilePanel = false"
            class="fixed inset-0 bg-black/20 z-[60] backdrop-blur-sm transition-opacity"
        ></div>

        <nav class="md:hidden fixed top-0 left-0 w-full bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-50 shadow-sm">
            <div class="flex justify-between items-center px-4 h-16">
                <div class="flex items-center gap-3">
                    <button @click="openMenus = !openMenus" class="p-2 text-gray-500 rounded-md hover:bg-gray-100 focus:outline-none">
                        <Hamburger :show="openMenus" />
                    </button>
                    <img src="/icon_kndi.svg" alt="Logo" class="w-8 h-8" />
                    <span class="font-bold text-lg text-gray-800 dark:text-white tracking-tight">KNDI Task</span>
                </div>
                <button @click="showProfilePanel = true" class="relative">
                     <div v-if="user.avatar" class="w-8 h-8 rounded-full overflow-hidden border border-indigo-200">
                        <img :src="user.avatar" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div v-else class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                         {{ getInitials(user.name) }}
                    </div>
                </button>
            </div>

            <div v-if="openMenus" class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 shadow-xl overflow-y-auto max-h-[80vh]">
                <div class="flex flex-col p-4 space-y-4">
                    <Menus v-model="openMenus" />
                </div>
            </div>
        </nav>

        <nav 
            class="hidden md:flex fixed top-0 left-0 h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 z-40 transition-all duration-300 ease-in-out shadow-sm flex-col" 
            :class="{'w-64': openMenus, 'w-20': !openMenus}"
        >
            <div 
                :class="{'justify-between pl-6 pr-4': openMenus, 'justify-center px-0': !openMenus}" 
                class="w-full h-20 flex items-center transition-all shrink-0"
            >
                <div v-if="openMenus" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <img src="/icon_kndi.svg" alt="Logo" class="w-8 h-8 shrink-0" />
                    <span class="text-gray-800 dark:text-white font-bold text-xl tracking-tight">KNDI</span>
                </div>
                <Hamburger v-model="openMenus" class="cursor-pointer text-gray-400 hover:text-indigo-600 transition" />
            </div>

            <div class="flex-1 overflow-y-auto py-6 px-4 flex flex-col gap-2 no-scrollbar">
                <Menus v-model="openMenus" />
            </div>
        </nav>

        <div 
            class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out pt-16 md:pt-0" 
            :class="{'md:pl-64': openMenus, 'md:pl-20': !openMenus}"
        >
            <header class="hidden md:flex h-24 items-center justify-between px-8 sticky top-0 z-30 bg-[#F2F5FA] dark:bg-slate-900">
                
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Hello, {{ user.name.split(' ')[0] }}!</h1>
                    <p class="text-sm text-slate-400 font-medium">Have a productive day.</p>
                </div>

                <div class="flex items-center gap-4">
                    <button 
                        @click="showProfilePanel = true"
                        class="flex items-center gap-3 p-1.5 pr-4 rounded-full bg-white dark:bg-gray-800 shadow-sm hover:shadow-md transition group border border-transparent hover:border-indigo-100"
                    >
                        <div v-if="user.avatar" class="w-10 h-10 rounded-full overflow-hidden border border-indigo-200">
                             <img :src="user.avatar" alt="User Avatar" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold border-2 border-white dark:border-gray-700">
                            {{ getInitials(user.name) }}
                        </div>

                        <div class="text-left hidden lg:block">
                            <p class="text-sm font-bold text-slate-700 dark:text-gray-200 group-hover:text-indigo-600 transition">{{ user.name }}</p>
                        </div>
                    </button>
                </div>
            </header>

            <main class="flex-1 py-6 px-4 md:px-8">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>
                
                <slot />
            </main>
        </div>

        <aside 
            class="fixed top-0 right-0 h-screen w-80 bg-white dark:bg-gray-800 shadow-2xl z-[70] transform transition-transform duration-300 ease-out overflow-y-auto"
            :class="showProfilePanel ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="p-6 flex flex-col h-full">
                
                <div class="flex justify-end mb-2">
                    <button @click="showProfilePanel = false" class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col items-center mb-8 w-full">
                    
                    <div class="relative group cursor-pointer mb-4" @click="openAvatarModal">
                        <div class="w-24 h-24 rounded-full bg-indigo-50 border-4 border-white dark:border-gray-700 shadow-lg flex items-center justify-center text-2xl font-bold text-indigo-600 relative overflow-hidden">
                            <img v-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" alt="Profile">
                            <span v-else>{{ getInitials(user.name) }}</span>
                            
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="text-white text-xs font-medium">Change</span>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-0 right-0 p-1.5 bg-white dark:bg-gray-700 rounded-full shadow-md border border-gray-100 dark:border-gray-600 text-indigo-500 hover:text-indigo-700 transition">
                            <Pen class="w-4 h-4" />
                        </div>
                    </div>

                    <div class="mt-2 w-full text-center min-h-[40px]">
                        <div v-if="!isEditingName" class="flex items-center justify-center gap-2 group">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white truncate max-w-[200px]">{{ user.name }}</h2>
                            <button @click="startEditingName" class="p-1 text-slate-300 group-hover:text-indigo-500 transition" title="Edit Name">
                                <Pen class="w-4 h-4" />
                            </button>
                        </div>

                        <div v-else class="flex items-center justify-center gap-2 animate-fade-in">
                            <TextInput 
                                v-model="nameForm.name" 
                                class="py-1 px-2 text-center text-sm w-40 h-8"
                                placeholder="Your Name"
                                @keyup.enter="saveName"
                            />
                            <button 
                                @click="saveName"
                                :disabled="nameForm.processing" 
                                class="p-1.5 bg-green-100 text-green-600 rounded-md hover:bg-green-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button 
                                @click="cancelEditingName" 
                                class="p-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                  <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mt-2">
                         <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs font-bold rounded-full uppercase tracking-wider border border-indigo-100 dark:border-indigo-800">
                            {{ getRoleLabel(user.role) }}
                        </span>
                    </div>
                </div>

                <hr class="border-slate-100 dark:border-gray-700 mb-6" />

                <div class="flex-1">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-700 dark:text-gray-200">Skills</h3>
                        <span class="text-xs text-slate-400 bg-slate-50 dark:bg-gray-700 px-2 py-0.5 rounded-md">Top 5</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <template v-if="user.skills && user.skills.length">
                            <span 
                                v-for="(skillItem, index) in user.skills.slice(0, 5)" 
                                :key="index"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-white dark:bg-gray-700 text-slate-600 dark:text-gray-200 border border-slate-200 dark:border-gray-600 shadow-sm"
                            >
                                {{ skillItem.skill }}
                            </span>
                        </template>
                        <div v-else class="w-full py-8 text-center border-2 border-dashed border-slate-200 dark:border-gray-700 rounded-xl">
                            <p class="text-sm text-slate-400">No skills added yet.</p>
                            <button @click="router.visit(route('profile.edit'))" class="mt-2 text-xs text-indigo-500 font-bold hover:underline">
                                + Add Skills via Full Profile
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-gray-700">
                    <button @click="logout" class="w-full py-3 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 font-bold text-sm hover:bg-red-100 transition flex items-center justify-center gap-2">
                        <Logout class="w-4 h-4" />
                        Log Out
                    </button>
                </div>
            </div>
        </aside>

        <Modal :show="showAvatarModal" @close="showAvatarModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                    Select Profile Avatar
                </h2>
                
                <p class="text-sm text-gray-500 mb-6">
                    Choose one of the available assets below for your profile picture.
                </p>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div 
                        v-for="(asset, index) in avatarAssets" 
                        :key="index"
                        @click="selectAvatar(asset)"
                        class="cursor-pointer rounded-full overflow-hidden border-4 transition-all duration-200 hover:scale-105"
                        :class="selectedAvatarTemp === asset ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-transparent hover:border-gray-200'"
                    >
                        <img :src="asset" alt="Avatar Option" class="w-full h-auto">
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="showAvatarModal = false"> Cancel </SecondaryButton>
                    <PrimaryButton @click="saveAvatar" :disabled="avatarForm.processing"> 
                        {{ avatarForm.processing ? 'Saving...' : 'Save Avatar' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <Toast />
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
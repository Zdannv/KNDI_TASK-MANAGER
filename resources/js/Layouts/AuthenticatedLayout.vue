<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router, usePage, useForm } from '@inertiajs/vue3';
import Menus from "@/Components/Menus.vue";
import Hamburger from '@/Components/Icon/Hamburger.vue';
import Logout from '@/Components/Icon/Logout.vue';
import Pen from '@/Components/Icon/Pen.vue';
import Toast from '@/Components/Toast.vue';
import Modal from '@/Components/Modal.vue'; 
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';

// Props
defineProps({
    title: String,
});

// State & Hooks
const page = usePage();
const user = computed(() => page.props.auth.user);

// --- Logic Sidebar Kanan (Profile) ---
const showProfilePanel = ref(false);

// Logic Edit Nama & Avatar
const isEditingName = ref(false);
const nameForm = useForm({ name: '', email: '', avatar: '' });

const startEditingName = () => {
    nameForm.name = user.value.name;
    nameForm.email = user.value.email; 
    nameForm.avatar = user.value.avatar;
    isEditingName.value = true;
};

const saveName = () => {
    nameForm.patch(route('profile.update'), {
        preserveScroll: true, preserveState: true, 
        onSuccess: () => { isEditingName.value = false; }
    });
};

const cancelEditingName = () => {
    isEditingName.value = false;
    nameForm.reset();
};

const showAvatarModal = ref(false);
const avatarForm = useForm({ name: '', email: '', avatar: '' });

const avatarAssets = [
    '/avatars/avatar-1.jpeg', '/avatars/avatar-2.jpg', '/avatars/avatar-3.jpeg', '/avatars/avatar-4.jpg',
    '/avatars/avatar-5.jpeg', '/avatars/avatar-6.jpeg', '/avatars/avatar-1.jpeg', '/avatars/avatar-2.jpg',
    '/avatars/avatar-3.jpeg', '/avatars/avatar-4.jpg', '/avatars/avatar-5.jpeg', '/avatars/avatar-6.jpeg',
];

const selectedAvatarTemp = ref('');

const openAvatarModal = () => {
    selectedAvatarTemp.value = user.value.avatar || avatarAssets[0];
    showAvatarModal.value = true;
};
const selectAvatar = (assetPath) => { selectedAvatarTemp.value = assetPath; };
const saveAvatar = () => {
    avatarForm.name = user.value.name;
    avatarForm.email = user.value.email;
    avatarForm.avatar = selectedAvatarTemp.value;
    avatarForm.patch(route('profile.update'), {
        preserveScroll: true, preserveState: true,
        onSuccess: () => { showAvatarModal.value = false; }
    });
};

// --- Logic Ganti Password ---
const showPasswordModal = ref(false);
const openPasswordModal = () => { showPasswordModal.value = true; };
const closePasswordModal = () => { showPasswordModal.value = false; };

// --- Logic Sidebar Kiri (Navigation) ---
const getInitialSidebarState = () => {
    if (typeof window !== 'undefined') {
        const savedState = localStorage.getItem('sidebarOpen');
        if (savedState !== null) return savedState === 'true';
        return window.innerWidth >= 768;
    }
    return true;
};

const openMenus = ref(getInitialSidebarState());
watch(openMenus, (newValue) => { localStorage.setItem('sidebarOpen', newValue); });

// Mutual Exclusivity
watch(openMenus, (isOpen) => { if (isOpen) showProfilePanel.value = false; });
watch(showProfilePanel, (isOpen) => {
    if (isOpen) openMenus.value = false;
    else { if (window.innerWidth >= 1024) openMenus.value = true; }
});

const logout = () => { router.post(route('logout')); };
const getInitials = (name) => {
    if (!name) return 'U';
    const names = name.split(' ');
    let initials = names[0].substring(0, 1).toUpperCase();
    if (names.length > 1) initials += names[names.length - 1].substring(0, 1).toUpperCase();
    return initials;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-[#F2F5FA] via-[#eef2ff] to-[#e0e7ff] dark:from-slate-950 dark:via-[#0d1b3e] dark:to-indigo-950 font-sans text-slate-600 dark:text-slate-300 relative selection:bg-indigo-500 selection:text-white overflow-x-hidden">
        <Head :title="title" />

        <nav class="md:hidden fixed top-0 left-0 w-full bg-white/10 dark:bg-gray-900/20 backdrop-blur-md border-b border-white/20 dark:border-gray-700/20 z-50 shadow-sm transition-colors duration-300">
            <div class="flex justify-between items-center px-4 h-16">
                <div class="flex items-center gap-3">
                    <button @click="openMenus = !openMenus" class="p-2 text-gray-600 dark:text-gray-300 rounded-md hover:bg-white/20 focus:outline-none transition">
                        <Hamburger :show="openMenus" />
                    </button>
                    <img src="/icon_kndi.svg" alt="Logo" class="w-8 h-8 drop-shadow-md" />
                    <span class="font-bold text-lg text-gray-800 dark:text-white tracking-tight drop-shadow-sm">KNDI Task</span>
                </div>
                <button @click="showProfilePanel = true" class="relative">
                     <div v-if="user.avatar" class="w-8 h-8 rounded-full overflow-hidden border border-white/50 shadow-sm">
                        <img :src="user.avatar" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <div v-else class="w-8 h-8 rounded-full bg-indigo-100/50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-inner backdrop-blur-sm">
                         {{ getInitials(user.name) }}
                    </div>
                </button>
            </div>

            <div v-if="openMenus" class="bg-white/60 dark:bg-gray-900/80 backdrop-blur-xl border-t border-white/20 dark:border-gray-700/30 shadow-xl overflow-y-auto max-h-[80vh]">
                <div class="flex flex-col p-4 space-y-4">
                    <Menus :sidebarOpen="true" />
                </div>
            </div>
        </nav>

        <nav 
            class="hidden md:flex fixed top-4 left-4 bottom-4 z-50 transition-all duration-500 cubic-bezier(0.4, 0, 0.2, 1) shadow-2xl flex-col rounded-[35px] overflow-hidden border border-white/20 dark:border-white/5" 
            :class="[
                openMenus ? 'w-72' : 'w-24',
                showProfilePanel ? '-translate-x-[200%]' : 'translate-x-0',
                'bg-gradient-to-b from-[#0d1b3e]/80 to-[#1e1b4b]/80 dark:from-slate-900/70 dark:to-black/70 backdrop-blur-xl'
            ]"
        >
            <div 
                class="h-24 flex items-center shrink-0 text-white transition-all duration-300 relative z-20"
                :class="openMenus ? 'justify-between pl-8 pr-6' : 'justify-center px-0'" 
            >
                <div v-if="openMenus" class="flex items-center gap-3 overflow-hidden whitespace-nowrap animate-fade-in">
                    <div class="bg-white/20 p-1.5 rounded-lg shadow-lg backdrop-blur-sm border border-white/10">
                        <img src="/icon_kndi.svg" alt="Logo" class="w-6 h-6 shrink-0" />
                    </div>
                    <span class="text-white font-bold text-2xl tracking-wide drop-shadow-md">KNDI.</span>
                </div>
                
                <button 
                    @click="openMenus = !openMenus" 
                    class="text-gray-300 hover:text-white transition focus:outline-none p-2 rounded-full hover:bg-white/10"
                >
                    <Hamburger :show="openMenus" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-6 no-scrollbar relative z-10">
                <Menus :sidebarOpen="openMenus" />
            </div>
        </nav>


        <div 
            class="flex-1 flex flex-col min-h-screen transition-all duration-500 ease-out pt-16 md:pt-0" 
            :class="{
                'md:pl-[20rem] md:pr-0': openMenus && !showProfilePanel, 
                'md:pl-[8rem] md:pr-0': !openMenus && !showProfilePanel,
                'md:pl-6 md:pr-[22rem]': showProfilePanel
            }"
        >
            <header class="hidden md:flex h-24 items-center justify-between px-8 sticky top-0 z-40 transition-colors duration-300
                bg-transparent backdrop-blur-md border-b border-white/5 dark:border-white/5">
                <div>
                    <h1 class="text-3xl font-bold text-[#0d1b3e] dark:text-white tracking-tight drop-shadow-sm">
                        Hello, {{ user.name.split(' ')[0] }}!
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">Have a productive day.</p>
                </div>

                <div class="flex items-center gap-4">
                    <button 
                        @click="showProfilePanel = !showProfilePanel"
                        class="flex items-center gap-3 p-1.5 pr-5 rounded-full bg-white/30 dark:bg-gray-800/30 shadow-sm hover:shadow-lg hover:bg-white/50 dark:hover:bg-gray-700/50 transition-all duration-300 group border border-white/30 hover:border-indigo-200 backdrop-blur-sm"
                    >
                        <div v-if="user.avatar" class="w-10 h-10 rounded-full overflow-hidden border-2 border-indigo-100 group-hover:border-indigo-300 transition">
                             <img :src="user.avatar" alt="User Avatar" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-10 h-10 rounded-full bg-indigo-100/50 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-sm border-2 border-white/30 dark:border-gray-700/30">
                            {{ getInitials(user.name) }}
                        </div>

                        <div class="text-left hidden lg:block">
                            <p class="text-sm font-bold text-slate-700 dark:text-gray-200 group-hover:text-indigo-600 transition">{{ user.name }}</p>
                        </div>
                    </button>
                </div>
            </header>

            <main class="flex-1 px-4 md:px-8 pb-8">
                <div v-if="$slots.header" class="mb-6">
                    <slot name="header" />
                </div>
                <slot />
            </main>
        </div>


        <aside 
            class="fixed top-0 right-0 h-screen w-80 shadow-[-10px_0_30px_-5px_rgba(0,0,0,0.1)] z-[70] transform transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1) overflow-y-auto border-l border-white/20 dark:border-white/10
            bg-gradient-to-b from-white/70 to-white/50 dark:from-gray-900/70 dark:to-gray-800/70 backdrop-blur-2xl"
            :class="showProfilePanel ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="p-6 flex flex-col h-full relative">
                
                <button @click="showProfilePanel = false" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50/50 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg font-bold text-[#0d1b3e] dark:text-white mb-8 mt-2">User Profile</h2>

                <div class="flex flex-col items-center mb-8 w-full">
                    <div class="relative group cursor-pointer mb-6" @click="openAvatarModal">
                        <div class="w-24 h-24 rounded-full bg-indigo-50/30 border-[5px] border-white/40 dark:border-gray-600/30 shadow-xl flex items-center justify-center text-3xl font-bold text-indigo-600 relative overflow-hidden backdrop-blur-sm">
                            <img v-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" alt="Profile">
                            <span v-else>{{ getInitials(user.name) }}</span>
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                <Pen class="text-white w-6 h-6" />
                            </div>
                        </div>
                    </div>

                    <div class="w-full text-center">
                        <div v-if="!isEditingName" class="flex items-center justify-center gap-2 group">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white shadow-sm">{{ user.name }}</h2>
                            <button @click="startEditingName" class="p-1 text-slate-400 group-hover:text-indigo-500 transition">
                                <Pen class="w-3.5 h-3.5" />
                            </button>
                        </div>
                        <div v-else class="flex items-center justify-center gap-2 animate-fade-in">
                            <TextInput v-model="nameForm.name" class="py-1 px-2 text-center text-sm font-bold w-40 bg-white/40 backdrop-blur-sm border-white/50" @keyup.enter="saveName" />
                            <button @click="saveName" class="p-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 shadow-md">✓</button>
                            <button @click="cancelEditingName" class="p-1.5 bg-gray-200/80 text-gray-600 rounded hover:bg-gray-300">✕</button>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">{{ user.email }}</p>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="bg-white/30 dark:bg-gray-700/30 rounded-xl p-5 mb-6 backdrop-blur-md border border-white/30 dark:border-gray-600/20 shadow-sm">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-sm text-slate-700 dark:text-gray-200">Skills</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                             <template v-if="user.skills && user.skills.length">
                                <span v-for="(skillItem, index) in user.skills.slice(0, 5)" :key="index" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/60 dark:bg-gray-800/60 text-indigo-600 dark:text-indigo-300 border border-indigo-100/50 dark:border-indigo-900 shadow-sm backdrop-blur-sm">
                                    {{ skillItem.skill }}
                                </span>
                            </template>
                            <span v-else class="text-xs text-slate-400 italic">No skills defined.</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-200/20 dark:border-gray-700/20 space-y-2">
                        <button 
                            @click="openPasswordModal"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 bg-white/40 dark:bg-slate-800/40 hover:bg-indigo-50/50 dark:hover:bg-gray-700/50 hover:text-indigo-600 rounded-lg transition-colors border border-white/40 dark:border-gray-600/30 backdrop-blur-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span>Change Password</span>
                        </button>

                        <button 
                            @click="logout" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-red-500 bg-red-50/30 hover:bg-red-100/50 hover:text-red-600 rounded-lg transition-colors backdrop-blur-sm border border-transparent hover:border-red-100"
                        >
                            <Logout class="w-4 h-4" />
                            <span>Sign Out</span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <Modal :show="showAvatarModal" @close="showAvatarModal = false">
            <div class="p-6 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">Choose Your Avatar</h2>
                
                <div class="flex flex-col items-center gap-6 mb-8">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-blue-500 rounded-full blur opacity-40 animate-pulse"></div>
                        <img 
                            :src="selectedAvatarTemp" 
                            alt="Selected Preview" 
                            class="relative w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-xl"
                        />
                        <div class="absolute bottom-0 right-0 bg-green-500 text-white rounded-full p-1 border-2 border-white dark:border-gray-800">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <div class="w-full px-2">
                        <div class="grid grid-cols-4 gap-3 sm:gap-4 justify-items-center">
                            <button 
                                v-for="(asset, index) in avatarAssets" 
                                :key="index" 
                                type="button"
                                @click="selectAvatar(asset)" 
                                class="relative rounded-full transition-all duration-200 focus:outline-none"
                                :class="[
                                    selectedAvatarTemp === asset 
                                        ? 'ring-2 ring-offset-2 ring-indigo-500 scale-110 opacity-100 z-10' 
                                        : 'opacity-60 hover:opacity-100 hover:scale-105 grayscale hover:grayscale-0'
                                ]"
                            >
                                <img :src="asset" class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover shadow-sm" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200/20">
                    <SecondaryButton @click="showAvatarModal = false">Cancel</SecondaryButton>
                    <PrimaryButton @click="saveAvatar" :disabled="avatarForm.processing" class="w-24 justify-center">
                        Save
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showPasswordModal" @close="closePasswordModal">
            <div class="p-6 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        Change Password
                    </h2>
                    <button @click="closePasswordModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-4">
                    <UpdatePasswordForm @close="closePasswordModal" />
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
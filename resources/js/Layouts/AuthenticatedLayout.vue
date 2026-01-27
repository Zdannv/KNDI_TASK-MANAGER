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
    nameForm.email = user.value.email; 
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

// --- MUTUAL EXCLUSIVITY LOGIC ---
// 1. Sidebar Kiri Buka -> Tutup Kanan
watch(openMenus, (isOpen) => {
    if (isOpen) {
         showProfilePanel.value = false;
    }
});

// 2. Sidebar Kanan Buka -> Tutup Kiri
watch(showProfilePanel, (isOpen) => {
    if (isOpen) {
        openMenus.value = false;
    } else {
        if (window.innerWidth >= 1024) {
             openMenus.value = true;
        }
    }
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
</script>

<template>
    <div class="min-h-screen bg-[#F2F5FA] dark:bg-slate-900 font-sans text-slate-600 dark:text-slate-300 relative selection:bg-indigo-500 selection:text-white overflow-x-hidden">
        <Head :title="title" />

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
            class="hidden md:flex fixed top-4 left-4 bottom-4 bg-[#0d1b3e] dark:bg-gray-800 z-50 transition-all duration-500 cubic-bezier(0.4, 0, 0.2, 1) shadow-2xl flex-col rounded-[35px] overflow-hidden" 
            :class="[
                openMenus ? 'w-72' : 'w-24',
                showProfilePanel ? '-translate-x-[200%]' : 'translate-x-0'
            ]"
        >
            <div 
                class="h-24 flex items-center shrink-0 text-white transition-all duration-300 relative z-20"
                :class="openMenus ? 'justify-between pl-8 pr-6' : 'justify-center px-0'" 
            >
                <div v-if="openMenus" class="flex items-center gap-3 overflow-hidden whitespace-nowrap animate-fade-in">
                    <div class="bg-white p-1.5 rounded-lg shadow-lg">
                        <img src="/icon_kndi.svg" alt="Logo" class="w-6 h-6 shrink-0" />
                    </div>
                    <span class="text-white font-bold text-2xl tracking-wide">KNDI.</span>
                </div>
                
                <button 
                    @click="openMenus = !openMenus" 
                    class="text-gray-300 hover:text-white transition focus:outline-none p-2 rounded-full hover:bg-white/10"
                >
                    <Hamburger :show="openMenus" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-6 custom-sidebar-menu no-scrollbar relative z-10">
                <Menus v-model="openMenus" />
            </div>

            </nav>


        <div 
            class="flex-1 flex flex-col min-h-screen transition-all duration-500 ease-out pt-16 md:pt-0" 
            :class="{
                // Sidebar Kiri Buka
                'md:pl-[20rem] md:pr-0': openMenus && !showProfilePanel, 
                
                // Sidebar Kiri Tutup (Kecil)
                'md:pl-[8rem] md:pr-0': !openMenus && !showProfilePanel,
                
                // Profile Sidebar Kanan Buka
                // pl-6: Jarak kiri sedikit
                // pr-[22rem]: Lebar sidebar (20rem) + margin (2rem)
                'md:pl-6 md:pr-[22rem]': showProfilePanel
            }"
        >
            <header class="hidden md:flex h-24 items-center justify-between px-8 sticky top-0 z-40 bg-[#F2F5FA]/95 dark:bg-slate-900/95 backdrop-blur-sm">
                <div>
                    <h1 class="text-3xl font-bold text-[#0d1b3e] dark:text-white tracking-tight">
                        Hello, {{ user.name.split(' ')[0] }}!
                    </h1>
                    <p class="text-sm text-slate-400 font-medium mt-1">Have a productive day.</p>
                </div>

                <div class="flex items-center gap-4">
                    <button 
                        @click="showProfilePanel = !showProfilePanel"
                        class="flex items-center gap-3 p-1.5 pr-5 rounded-full bg-white dark:bg-gray-800 shadow-sm hover:shadow-xl transition-all duration-300 group border border-transparent hover:border-indigo-100"
                    >
                        <div v-if="user.avatar" class="w-10 h-10 rounded-full overflow-hidden border-2 border-indigo-100 group-hover:border-indigo-300 transition">
                             <img :src="user.avatar" alt="User Avatar" class="w-full h-full object-cover">
                        </div>
                        <div v-else class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-sm border-2 border-white dark:border-gray-700">
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
            class="fixed top-0 right-0 h-screen w-80 bg-white dark:bg-gray-800 shadow-[-10px_0_30px_-5px_rgba(0,0,0,0.1)] z-[70] transform transition-transform duration-500 cubic-bezier(0.4, 0, 0.2, 1) overflow-y-auto"
            :class="showProfilePanel ? 'translate-x-0' : 'translate-x-full'"
        >
            <div class="p-6 flex flex-col h-full relative">
                
                <button @click="showProfilePanel = false" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg font-bold text-[#0d1b3e] dark:text-white mb-8 mt-2">User Profile</h2>

                <div class="flex flex-col items-center mb-8 w-full">
                    <div class="relative group cursor-pointer mb-6" @click="openAvatarModal">
                        <div class="w-24 h-24 rounded-full bg-indigo-50 border-[5px] border-white dark:border-gray-700 shadow-xl flex items-center justify-center text-3xl font-bold text-indigo-600 relative overflow-hidden">
                            <img v-if="user.avatar" :src="user.avatar" class="w-full h-full object-cover" alt="Profile">
                            <span v-else>{{ getInitials(user.name) }}</span>
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                <Pen class="text-white w-6 h-6" />
                            </div>
                        </div>
                    </div>

                    <div class="w-full text-center">
                        <div v-if="!isEditingName" class="flex items-center justify-center gap-2 group">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ user.name }}</h2>
                            <button @click="startEditingName" class="p-1 text-slate-300 group-hover:text-indigo-500 transition">
                                <Pen class="w-3.5 h-3.5" />
                            </button>
                        </div>
                        <div v-else class="flex items-center justify-center gap-2 animate-fade-in">
                            <TextInput v-model="nameForm.name" class="py-1 px-2 text-center text-sm font-bold w-40" @keyup.enter="saveName" />
                            <button @click="saveName" class="p-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700 shadow-md">✓</button>
                            <button @click="cancelEditingName" class="p-1.5 bg-gray-200 text-gray-600 rounded hover:bg-gray-300">✕</button>
                        </div>
                        <p class="text-slate-400 text-xs mt-1">{{ user.email }}</p>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="bg-slate-50 dark:bg-gray-700/50 rounded-xl p-5 mb-6">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-bold text-sm text-slate-700 dark:text-gray-200">Skills</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                             <template v-if="user.skills && user.skills.length">
                                <span v-for="(skillItem, index) in user.skills.slice(0, 5)" :key="index" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white text-indigo-600 border border-indigo-100 shadow-sm">
                                    {{ skillItem.skill }}
                                </span>
                            </template>
                            <span v-else class="text-xs text-slate-400 italic">No skills defined.</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-100 dark:border-gray-700">
                        <button 
                            @click="logout" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-600 rounded-lg transition-colors"
                        >
                            <Logout class="w-4 h-4" />
                            <span>Sign Out</span>
                        </button>
                    </div>
                </div>
            </div>
        </aside>

        <Modal :show="showAvatarModal" @close="showAvatarModal = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Choose Avatar</h2>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div v-for="(asset, index) in avatarAssets" :key="index" @click="selectAvatar(asset)" class="cursor-pointer rounded-full overflow-hidden border-4 transition-all hover:scale-105" :class="selectedAvatarTemp === asset ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-transparent'">
                        <img :src="asset" class="w-full h-auto">
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="showAvatarModal = false">Cancel</SecondaryButton>
                    <PrimaryButton @click="saveAvatar" :disabled="avatarForm.processing">Save</PrimaryButton>
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

/* =================================================================
   SIDEBAR STYLING - THE "CURVE" EFFECT
   ================================================================= */

/* Container Menu */
:deep(.custom-sidebar-menu ul) {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Base Link Style */
:deep(.custom-sidebar-menu a) {
    position: relative;
    display: flex;
    align-items: center;
    height: 50px;
    padding-left: 2rem;
    color: #94a3b8; /* text-slate-400 */
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    
    /* Default: Rounded di KIRI saja */
    margin-left: 1.5rem;
    border-top-left-radius: 30px;
    border-bottom-left-radius: 30px;
    
    /* Penting: Kanan harus 0 agar menempel dinding */
    margin-right: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

:deep(.custom-sidebar-menu a:hover:not(.active)) {
    color: white;
}

/* ----------------------------------------------------------------
   STATE ACTIVE: OPEN SIDEBAR
   ---------------------------------------------------------------- */
nav.w-72 :deep(.custom-sidebar-menu a.active) {
    background-color: #F2F5FA; 
    color: #0d1b3e; /* Biru Gelap */
    font-weight: 700;
    z-index: 10;
}

/* Ikon Active */
nav.w-72 :deep(.custom-sidebar-menu a.active svg),
nav.w-72 :deep(.custom-sidebar-menu a.active img) {
    color: #0d1b3e;
    transform: scale(1.1);
}

/* Curve Atas */
nav.w-72 :deep(.custom-sidebar-menu a.active::before) {
    content: "";
    position: absolute;
    right: 0;
    top: -30px; 
    width: 30px;
    height: 30px;
    background-color: transparent; 
    border-radius: 50%;
    box-shadow: 15px 15px 0 0 #F2F5FA; 
    pointer-events: none;
    z-index: 1;
}

/* Curve Bawah */
nav.w-72 :deep(.custom-sidebar-menu a.active::after) {
    content: "";
    position: absolute;
    right: 0;
    bottom: -30px; 
    width: 30px;
    height: 30px;
    background-color: transparent;
    border-radius: 50%;
    box-shadow: 15px -15px 0 0 #F2F5FA; 
    pointer-events: none;
    z-index: 1;
}

/* ----------------------------------------------------------------
   STATE ACTIVE: CLOSED SIDEBAR
   ---------------------------------------------------------------- */

/* Reset style curve saat sidebar kecil */
nav.w-24 :deep(.custom-sidebar-menu a) {
    justify-content: center;
    padding: 0 !important;
    margin-left: 12px !important;
    margin-right: 12px !important;
    border-radius: 16px !important;
}

/* Matikan Curve */
nav.w-24 :deep(.custom-sidebar-menu a::before),
nav.w-24 :deep(.custom-sidebar-menu a::after) {
    display: none !important;
}

/* Style Active Simple */
nav.w-24 :deep(.custom-sidebar-menu a.active) {
    background-color: white;
    color: #0d1b3e;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Sembunyikan teks */
nav.w-24 :deep(.custom-sidebar-menu span) {
    display: none;
}
</style>
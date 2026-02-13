<script setup>
import { computed, ref, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

// Import Icons
import User from "@/Components/Icon/User.vue";
import Build from "@/Components/Icon/Build.vue";
import Folder from "@/Components/Icon/Folder.vue";
import Document from "@/Components/Icon/Document.vue";
import News from "@/Components/Icon/News.vue";
import Clock from "@/Components/Icon/Clock.vue";
import Book from "@/Components/Icon/Book.vue";
import Warning from "@/Components/Icon/Warning.vue";
import Cloud from "@/Components/Icon/Cloud.vue";
import UserPlus from "@/Components/Icon/UserPlus.vue";

const props = defineProps({
    sidebarOpen: Boolean,
});

const page = usePage();
const userRole = computed(() => page.props.auth.user.role);

// 1. Definisi Menu
const rawMenuItems = computed(() => [
    { label: "Dashboard", route: "dashboard", icon: News, show: ["other", "pm"].includes(userRole.value), pattern: "dashboard" },
    { label: "Users", route: "user.list", icon: User, show: ["other", "pm"].includes(userRole.value), pattern: "user.list" },
    { label: "Project Owner", route: "client.list", icon: Build, show: ["other", "pm", "co"].includes(userRole.value), pattern: "client.list" },
    { label: "Projects", route: "project.list", icon: Folder, show: true, pattern: "project.list" },
    { label: "Tasks", route: "task.list", icon: Document, show: true, pattern: ["task.list", "task.show"] },
    { label: "Logtime", route: "logtime.list", icon: Clock, show: true, pattern: "logtime.list" },
    { label: "Program Log", route: "log.list", icon: Warning, show: userRole.value === "pm", pattern: "log.list" },
    { label: "Skill", route: "skill.list", icon: Book, show: true, pattern: "skill.list" },
    { label: "Attendance", route: "attendance", icon: UserPlus, show: userRole.value === "other", pattern: "attendance" },
    { label: "Import", route: "import.index", icon: Cloud, show: ["other", "co"].includes(userRole.value), pattern: "import.index" },
]);

const visibleMenuItems = computed(() => rawMenuItems.value.filter(item => item.show));

// 2. Logic Active Index (Reactive)
const activeIndex = computed(() => {
    return visibleMenuItems.value.findIndex(item => {
        if (Array.isArray(item.pattern)) {
            return item.pattern.some(p => route().current(p));
        }
        return route().current(item.pattern);
    });
});

// 3. Logic Animasi "Flying Dot"
const isMoving = ref(false);
const moveDirection = ref('down'); 
const ITEM_HEIGHT = 50; 
const GAP = 8; 

watch(activeIndex, (newVal, oldVal) => {
    if (newVal !== -1 && oldVal !== -1 && newVal !== oldVal) {
        moveDirection.value = newVal > oldVal ? 'down' : 'up';
        isMoving.value = true;
        
        setTimeout(() => {
            isMoving.value = false;
        }, 300); 
    }
});
</script>

<template>
    <div class="relative flex flex-col gap-2 overflow-visible">
        
        <div
            class="absolute z-0 bg-gradient-to-r from-white/95 to-white/70 dark:from-primary-600/40 dark:to-transparent backdrop-blur-md border border-white/40 dark:border-white/10 shadow-lg"
            :class="[
                'h-[50px] transform-gpu transition-all duration-300 ease-in-out',
                activeIndex === -1 ? 'opacity-0 scale-90' : 'opacity-100',
                
                sidebarOpen 
                    ? 'w-[calc(100%-16px)] ml-4 rounded-l-full' 
                    : 'w-[50px] left-1/2 -translate-x-1/2 rounded-full',
                
                isMoving 
                    ? 'scale-y-[0.4] scale-x-[0.4] rounded-full opacity-80'
                    : 'scale-y-100 scale-x-100',
            ]"
            :style="{
                transformOrigin: 'center', 
                top: `${activeIndex * (ITEM_HEIGHT + GAP)}px`
            }"
        >
        </div>

        <template v-for="(item, index) in visibleMenuItems" :key="index">
            <Link
                :href="route(item.route)"
                class="group relative z-10 flex items-center h-[50px] font-medium no-underline transition-colors duration-200"
                :class="[
                    index === activeIndex
                        ? 'text-primary-900 dark:text-primary-300 font-bold' 
                        : 'text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-primary-200' 
                ]"
            >
                <div 
                    class="flex items-center justify-center shrink-0 transition-all duration-300"
                    :class="[
                        index === activeIndex ? 'scale-110 text-primary-600 dark:text-primary-400' : 'group-hover:scale-110',
                        sidebarOpen ? 'ml-9 mr-3' : 'w-full'
                    ]"
                >
                    <component :is="item.icon" class="w-6 h-6" />
                </div>

                <span 
                    v-if="sidebarOpen" 
                    class="whitespace-nowrap transition-opacity duration-300 delay-75"
                >
                    {{ item.label }}
                </span>

                <div
                    v-if="!sidebarOpen"
                    class="absolute left-[65px] top-1/2 -translate-y-1/2 px-3 py-1.5 
                           bg-slate-800 dark:bg-primary-500 
                           text-white dark:text-primary-950
                           text-xs font-bold
                           rounded-md shadow-2xl border border-white/10
                           opacity-0 group-hover:opacity-100 group-hover:translate-x-2 translate-x-0
                           transition-all duration-200 pointer-events-none whitespace-nowrap z-[9999]"
                >
                    {{ item.label }}
                    <div class="absolute top-1/2 -left-1 -translate-y-1/2 border-4 border-transparent 
                                border-r-slate-800 dark:border-r-primary-500"></div>
                </div>
            </Link>
        </template>
    </div>
</template>

<style scoped>
/* Pastikan tidak ada parent yang memotong konten */
:deep(.relative) {
    overflow: visible !important;
}

.relative {
    user-select: none;
}
</style>
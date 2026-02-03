<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

// Import Icon
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

// Helper cek active route
const isActive = (routePattern) => route().current(routePattern);

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

// 2. Filter menu yang ditampilkan saja
const visibleMenuItems = computed(() => {
    return rawMenuItems.value.filter(item => item.show);
});

// 3. Cari Index Menu Aktif
const activeIndex = computed(() => {
    return visibleMenuItems.value.findIndex(item => {
        if (Array.isArray(item.pattern)) {
            return item.pattern.some(p => isActive(p));
        }
        return isActive(item.pattern);
    });
});

const ITEM_HEIGHT = 50; 
const GAP = 8; 
</script>

<template>
    <div class="relative flex flex-col gap-2">
        
        <div
            class="absolute left-0 z-0 bg-gradient-to-r from-white/95 to-white/70 dark:from-indigo-600/40 dark:to-transparent backdrop-blur-md border border-white/40 dark:border-white/10 shadow-lg transition-all duration-500 cubic-bezier(0.34, 1.56, 0.64, 1)"
            :class="[
                'h-[50px]', 
                activeIndex === -1 ? 'opacity-0 scale-90' : 'opacity-100 scale-100',
                sidebarOpen 
                    ? 'w-full ml-4 rounded-l-[30px] rounded-r-none mr-0' 
                    : 'w-[calc(100%-24px)] mx-3 rounded-xl'
            ]"
            :style="{
                transform: `translateY(${activeIndex * (ITEM_HEIGHT + GAP)}px)`
            }"
        >
        </div>

        <template v-for="(item, index) in visibleMenuItems" :key="index">
            <Link
                :href="route(item.route)"
                class="group relative z-10 flex items-center h-[50px] font-medium no-underline transition-colors duration-300"
                :class="[
                    (Array.isArray(item.pattern) ? item.pattern.some(p => isActive(p)) : isActive(item.pattern))
                        ? 'text-[#0d1b3e] dark:text-indigo-300' 
                        : 'text-slate-400 hover:text-white dark:text-slate-500 dark:hover:text-indigo-200' 
                ]"
            >
                <div 
                    class="flex items-center justify-center shrink-0 transition-transform duration-300"
                    :class="[
                        (Array.isArray(item.pattern) ? item.pattern.some(p => isActive(p)) : isActive(item.pattern)) ? 'scale-110 text-indigo-600 dark:text-indigo-400' : 'group-hover:scale-110',
                        sidebarOpen ? 'ml-9 mr-3' : 'w-full'
                    ]"
                >
                    <component :is="item.icon" class="w-6 h-6" />
                </div>

                <span 
                    v-show="sidebarOpen" 
                    class="whitespace-nowrap transition-opacity duration-300 delay-75"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0 overflow-hidden'"
                >
                    {{ item.label }}
                </span>

                <div
                    v-if="!sidebarOpen"
                    class="absolute left-[calc(100%+10px)] top-1/2 -translate-y-1/2 bg-[#0d1b3e]/90 dark:bg-slate-900/95 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap shadow-xl z-50 border border-white/20 dark:border-white/10"
                >
                    {{ item.label }}
                    <div class="absolute top-1/2 -left-1 -translate-y-1/2 border-4 border-transparent border-r-[#0d1b3e]/90 dark:border-r-slate-900/95"></div>
                </div>
            </Link>
        </template>
    </div>
</template>
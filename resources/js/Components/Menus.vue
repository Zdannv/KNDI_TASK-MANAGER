<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";

// Import Semua Icon
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
    modelValue: Boolean,
});

const page = usePage();
const userRole = computed(() => page.props.auth.user.role);

// Daftar Konfigurasi Menu (Array)
const menuItems = computed(() => [
    {
        label: "Dashboard",
        route: "dashboard",
        icon: News,
        show: ["other", "pm"].includes(userRole.value),
        active: route().current("dashboard"),
    },
    {
        label: "Users",
        route: "user.list",
        icon: User,
        show: ["other", "pm"].includes(userRole.value),
        active: route().current("user.list"),
    },
    {
        label: "Project Owner",
        route: "client.list",
        icon: Build,
        show: ["other", "pm", "co"].includes(userRole.value),
        active: route().current("client.list"),
    },
    {
        label: "Projects",
        route: "project.list",
        icon: Folder,
        show: true, 
        active: route().current("project.list"),
    },
    {
        label: "Tasks",
        route: "task.list",
        icon: Document,
        show: true,
        active: route().current("task.list") || route().current("task.show"),
    },
    {
        label: "Logtime",
        route: "logtime.list",
        icon: Clock,
        show: true,
        active: route().current("logtime.list"),
    },
    {
        label: "Program Log",
        route: "log.list",
        icon: Warning,
        show: userRole.value === "pm",
        active: route().current("log.list"),
    },
    {
        label: "Skill",
        route: "skill.list",
        icon: Book,
        show: true,
        active: route().current("skill.list"),
    },
        {
        label: "Attendance",
        route: "attendance.list",
        icon: UserPlus,
        show: userRole.value === "other",
        active: route().current("attendance.list"),
    },
    {
        label: "Import",
        route: "import.index",
        icon: Cloud,
        show: ["other", "co"].includes(userRole.value),
        active: route().current("import.index"),
    },
]);
</script>

<template>
    <div class="space-y-4">
        <template v-for="(menu, index) in menuItems" :key="index">
            <div v-if="menu.show" class="relative group">
                <NavLink
                    :href="route(menu.route)"
                    :active="menu.active"
                    class="flex items-center"
                >
                    <div
                        :class="{ 'me-2': modelValue }"
                        class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900 transition-colors"
                    >
                        <component :is="menu.icon" />
                    </div>
                    
                    <span
                        :class="{
                            hidden: !modelValue,
                            'inline-flex': modelValue,
                        }"
                        class="whitespace-nowrap transition-all duration-300"
                    >
                        {{ menu.label }}
                    </span>
                </NavLink>

                <span
                    v-if="!modelValue"
                    class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50 shadow-md"
                >
                    {{ menu.label }}
                </span>
            </div>
        </template>
    </div>
</template>
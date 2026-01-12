<script setup>
import { usePage } from "@inertiajs/vue3";
import NavLink from "@/Components/NavLink.vue";
import User from "@/Components/Icon/User.vue";
import Build from "@/Components/Icon/Build.vue";
import Folder from "@/Components/Icon/Folder.vue";
import Document from "@/Components/Icon/Document.vue";
import News from "@/Components/Icon/News.vue";
import Clock from "@/Components/Icon/Clock.vue";
import Book from "@/Components/Icon/Book.vue";
import Warning from "./Icon/Warning.vue";
import Cloud from "./Icon/Cloud.vue";

defineProps({
    modelValue: Boolean,
});

const role = usePage().props.auth.user.role
</script>

<template>
  <div class="space-y-4">
    <!-- Dashboard -->
    <div v-if="['other', 'pm'].includes(role)" class="relative group">
      <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <News />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Dashboard</span>
      </NavLink>
      <!-- Tooltip -->
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Dashboard
      </span>
    </div>

    <!-- Users -->
    <div v-if="['other', 'pm'].includes(role)" class="relative group">
      <NavLink :href="route('user.list')" :active="route().current('user.list')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <User />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Users</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Users
      </span>
    </div>

    <!-- Project Owner -->
    <div v-if="['other', 'pm', 'co'].includes(role)" class="relative group">
      <NavLink :href="route('client.list')" :active="route().current('client.list')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Build />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Project owner</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Project owner
      </span>
    </div>

    <!-- Projects -->
    <div class="relative group">
      <NavLink :href="route('project.list')" :active="route().current('project.list')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Folder />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Projects</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Projects
      </span>
    </div>

    <!-- Tasks -->
    <div class="relative group">
      <NavLink :href="route('task.list')" :active="route().current('task.list') || route().current('task.show')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Document />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Tasks</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Tasks
      </span>
    </div>

    <!-- Logtime -->
    <div class="relative group">
      <NavLink :href="route('logtime.list')" :active="route().current('logtime.list')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Clock />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Logtime</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Logtime
      </span>
    </div>

    <!-- Program Log -->
    <div v-if="role == 'pm'" class="relative group">
      <NavLink :href="route('log.list')" :active="route().current('log.list')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Warning />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Program Log</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Program Log
      </span>
    </div>

    <!-- Skill -->
    <div class="relative group">
      <NavLink :href="route('skill.list')" :active="route().current('skill.list')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Book />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Skill</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Skill
      </span>
    </div>

    <!-- Import -->
    <div v-if="['other', 'co'].includes(role)" class="relative group">
      <NavLink :href="route('import.index')" :active="route().current('import.index')" class="flex items-center">
        <div :class="{'me-2': modelValue}" class="p-[5px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-100 dark:hover:bg-gray-900">
          <Cloud />
        </div>
        <span :class="{'hidden': !modelValue, 'inline-flex': modelValue}">Import</span>
      </NavLink>
      <span v-if="!modelValue" class="absolute left-full ml-2 top-1/2 -translate-y-1/2 bg-indigo-100 dark:bg-gray-800 font-bold text-black dark:text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Import
      </span>
    </div>
  </div>
</template>
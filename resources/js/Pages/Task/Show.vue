<script setup>
import Pen from '@/Components/Icon/Pen.vue';
import Trash from '@/Components/Icon/Trash.vue';
import Close from '@/Components/Icon/Close.vue';
import Hamburger from '@/Components/Icon/Hamburger.vue';
import User from '@/Components/Icon/User.vue';
import UserPlus from '@/Components/Icon/UserPlus.vue';
import Chat from '@/Components/Icon/Chat.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskCreateEditForm from '@/Components/Form/TaskCreateEdit.vue';
import TaskAssignForm from '@/Components/Form/TaskAssign.vue';
import TaskPrForm from '@/Components/Form/TaskPr.vue';
import TaskCommentForm from '@/Components/Form/TaskComment.vue';
import TaskReplyForm from '@/Components/Form/TaskReply.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import moment from 'moment';

const showButtons = ref(false);
const openCreateEditForm = ref(false);
const openAssignForm = ref(false);
const openPrForm = ref(false);
const openCommentForm = ref(false);
const openReplyForm = ref(false);
const isEditMode = ref(false);
const selectedTask = ref(null);
const selectedComment = ref(null);
const isLoaded = ref(false);

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const page = usePage();
const id = computed(() => page.props.auth.user.id);
const role = computed(() => page.props.auth.user.role);

// --- Handle Back ---
const handleBack = () => {
  router.get(route('task.list'));
};

const handleDelete = () => {
  if (confirm('Are you sure you want to delete this task?')) {
    router.delete(route('task.destroy', props.task.id));
  }
};

const handleEdit = () => {
  isEditMode.value = true;
  selectedTask.value = props.task;
  openCreateEditForm.value = true;
};

const handleAssign = () => {
  selectedTask.value = props.task;
  openAssignForm.value = true;
};

const handlePr = () => {
  selectedTask.value = props.task;
  openPrForm.value = true;
};

const handleComment = () => {
  selectedTask.value = props.task;
  openCommentForm.value = true;
};

const handleReply = (id) => {
  const comment = props.prs.find(p => p.id === id);
  if (comment) {
    openReplyForm.value = true;
    selectedComment.value = comment;
  }
};

const markReviewDone = (taskId, reviewerId) => {
  if (!confirm('Mark review as completed?')) return;
  router.put(route('task.review.complete', { id: taskId, reviewerId }));
};

const handleClose = () => {
  if (confirm('Are you sure you want to close this task?')) {
    router.put(route('task.close', props.task.id));
  }
};

const handleCloseForm = () => {
  openCreateEditForm.value = false;
  openAssignForm.value = false;
  openPrForm.value = false;
  openCommentForm.value = false;
  openReplyForm.value = false;
  isEditMode.value = false;
  selectedTask.value = null;
  selectedComment.value = null;
};

const props = defineProps({
  task: {},
  users: {},
  project: {},
  projects: {},
  communicator: {},
  programmer: {},
  designer: {},
  totalTimeUsed: '',
  prs: {},
});

// --- UPDATED FORMAT DATE (Bisa pakai Jam) ---
const formatDate = (date, withTime = false) => {
  if (!date) return '-';
  // Format default: 21 January 2026
  let formatString = 'DD MMMM YYYY';
  // Kalau butuh jam (untuk komentar): 21 Jan 2026, 14:30
  if (withTime) formatString = 'DD MMM YYYY, HH:mm';
  
  return moment(date).format(formatString);
};

const formatJsonList = (jsonData) => {
  if (!jsonData) return '-';
  if (Array.isArray(jsonData)) {
    return jsonData.map(id => getNameUser(id) || id).join(', ');
  }
  return jsonData;
};

const getNameUser = (id) => {
  return id ? props.users.find(u => u.id === id)?.name : '-';
};

// --- CHART LOGIC ---
const logtimeSegments = computed(() => {
  const logs = props.task.logtimes || [];
  if (!logs.length) return [];

  const grouped = {};
  let totalCalc = 0;

  logs.forEach(log => {
    const uid = log.user_id;
    const time = Number(log.time_used);
    if (!grouped[uid]) {
      grouped[uid] = { 
        id: uid,
        name: getNameUser(uid) || 'Unknown', 
        time: 0 
      };
    }
    grouped[uid].time += time;
    totalCalc += time;
  });

  const colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#6366F1'];
  const circumference = 2 * Math.PI * 40; 
  let currentOffset = 0;

  return Object.values(grouped)
    .sort((a, b) => b.time - a.time)
    .map((item, index) => {
      const segmentLength = (item.time / totalCalc) * circumference;
      
      const data = {
        ...item,
        color: colors[index % colors.length],
        strokeDasharray: `${segmentLength} ${circumference}`,
        strokeDashoffset: -currentOffset,
        percentage: Math.round((item.time / totalCalc) * 100)
      };
      
      currentOffset += segmentLength;
      return data;
    });
});

const visibleButtons = computed(() => {
  const buttons = [];
  if (['other', 'pm'].includes(role.value)) {
    buttons.push({ action: 'assign', icon: User, handler: handleAssign, text: 'Assign' });
  }
  if (
    (['pm', 'pg', 'ds'].includes(role.value) && [props.task?.programmer, props.task?.designer].some(arr => arr?.includes(id.value))) ||
    ['other'].includes(role.value)
  ) {
    buttons.push({ action: 'review', icon: UserPlus, handler: handlePr, text: 'Review' });
  }
  if (
    props.task?.pl === id.value ||
    ['other'].includes(role.value) ||
    [props.task?.communicator, props.task?.programmer, props.task?.designer, props.task?.reviewer].some(arr => arr?.includes(id.value))
  ) {
    buttons.push({ action: 'comment', icon: Chat, handler: handleComment, text: 'Comment' });
  }
  if (['other', 'pm', 'co'].includes(role.value)) {
    buttons.push({ action: 'edit', icon: Pen, handler: handleEdit, text: 'Edit' });
  }
  if (['other', 'pm', 'co'].includes(role.value)) {
    buttons.push({ action: 'delete', icon: Trash, handler: handleDelete, text: 'Delete' });
  }
  if (
    (['pm', 'pg', 'ds'].includes(role.value) && [props.task?.programmer, props.task?.designer].some(arr => arr?.includes(id.value))) ||
    ['other'].includes(role.value)
  ) {
    buttons.push({ action: 'close', icon: Close, handler: handleClose, text: 'Close Task' });
  }
  return buttons;
});
</script>

<template>
  <Head title="Task" />
  <AuthenticatedLayout>
    <template #header>
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div
          class="flex justify-between px-5 py-3 items-center text-gray-800 dark:text-gray-200 bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg transition-all duration-700 ease-out"
          :class="{ 'opacity-100': isLoaded, 'translate-y-8 opacity-0': !isLoaded }"
        >
          <div class="flex items-center gap-4 lg:col-span-2">
            <button 
              @click="handleBack"
              class="p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors text-gray-600 dark:text-gray-300"
              title="Back to Task List"
            >
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
              </svg>
            </button>
              
            <h2 class="font-semibold text-xl leading-tight">
              {{ task.issue }}
            </h2>
          </div>

          <div class="lg:col-span-4 flex justify-end gap-2">
            <button
              v-for="button in visibleButtons"
              :key="button.action"
              @click="button.handler"
              class="flex gap-2 p-[8px] border rounded-md border-gray-400 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-950 transition-colors"
            >
              <component :is="button.icon" />
              <span class="hidden sm:inline">{{ button.text }}</span>
            </button>
          </div>
        </div>
      </div>
    </template>

    <div class="fixed sm:hidden right-9 bottom-9 z-50">
      <div
        class="shrink-0 inline-flex items-center justify-center p-2 rounded-full dark:text-gray-300 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out border bg-white dark:bg-gray-700 shadow-sm transition-all duration-800 ease-out delay-500"
        :class="{ 'translate-y-0 opacity-100 scale-100': isLoaded, 'translate-y-12 opacity-0 scale-75': !isLoaded }"
      >
        <Hamburger v-model="showButtons" />
      </div>
      <TransitionGroup tag="div" name="button-list">
        <button
          v-for="(button, index) in visibleButtons"
          v-show="showButtons"
          :key="button.action"
          @click="button.handler"
          class="fixed right-9 border rounded-full p-4 dark:text-white bg-white dark:bg-gray-700 shadow-sm"
          :style="{ bottom: `${36 + (index + 1) * 76}px` }"
        >
          <component :is="button.icon" />
        </button>
      </TransitionGroup>
    </div>

    <div v-if="openCreateEditForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-5xl w-full p-8 relative animate-in fade-in zoom-in duration-300 overflow-y-auto max-h-[90vh]">
        <TaskCreateEditForm :task="selectedTask" :projects="projects" :isEditMode="isEditMode" @close="handleCloseForm" />
      </div>
    </div>

    <div v-if="openAssignForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskAssignForm :task="selectedTask" :pl="users" :co="communicator" :pg="programmer" :ds="designer" @close="handleCloseForm" />
      </div>
    </div>

    <div v-if="openPrForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskPrForm :task="selectedTask" :pg="programmer" @close="handleCloseForm" />
      </div>
    </div>

    <div v-if="openCommentForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskCommentForm :task="selectedTask" @close="handleCloseForm" />
      </div>
    </div>

    <div v-if="openReplyForm" class="fixed inset-0 z-[100] px-4 flex items-center justify-center bg-black/60 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in duration-200">
        <TaskReplyForm :comment="selectedComment" @close="handleCloseForm" />
      </div>
    </div>

    <div
      class="py-8 transition-all duration-1000 ease-out delay-100"
      :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="mx-auto max-w-[100rem] sm:px-6 lg:px-8">
        <div class="overflow-x-auto bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-lg">

          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Task Overview</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Client</label><p class="text-gray-800 dark:text-gray-200">{{ project.client?.name || '-' }}</p></div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Project</label><p class="text-gray-800 dark:text-gray-200">{{ project.name || '-' }}</p></div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Type</label><p class="text-gray-800 dark:text-gray-200">{{ task.type }}</p></div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Creator</label><p class="text-gray-800 dark:text-gray-200">{{ getNameUser(task.creator) }}</p></div>
              </div>
              <div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Start Date</label><p class="text-gray-800 dark:text-gray-200">{{ formatDate(task.start_date) }}</p></div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Due Date</label><p class="text-gray-800 dark:text-gray-200">{{ formatDate(task.due_date) }}</p></div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">End Date</label><p class="text-gray-800 dark:text-gray-200">{{ formatDate(task.end_date) }}</p></div>
                <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Updater</label><p class="text-gray-800 dark:text-gray-200">{{ getNameUser(task.updater) }}</p></div>
              </div>
            </div>
          </div>

          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Team</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
              <div><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Project Leader</label><p class="text-gray-800 dark:text-gray-200">{{ getNameUser(task.pl) }}</p></div>
              <div><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Communicator</label><p class="text-gray-800 dark:text-gray-200">{{ formatJsonList(task.communicator) }}</p></div>
              <div><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Programmer</label><p class="text-gray-800 dark:text-gray-200">{{ formatJsonList(task.programmer) }}</p></div>
              <div><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Designer</label><p class="text-gray-800 dark:text-gray-200">{{ formatJsonList(task.designer) }}</p></div>
              <div><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Reviewer</label><p class="text-gray-800 dark:text-gray-200">{{ formatJsonList(task.reviewer) }}</p></div>
            </div>
          </div>

          <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Details</h3>
            <div class="mb-4"><label class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</label><p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ task.description || '-' }}</p></div>
            <div class="mb-4 flex flex-col">
              <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Ticket Link</label>
              <a :href="'//' + task.ticket_link" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ task.ticket_link }}</a>
            </div>
            <div class="mb-4 flex flex-col">
              <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Related Links</label>
              <div v-if="task.related_links?.length > 0" v-for="link in task.related_links" :key="link">
                <a :href="'//' + link" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline block">{{ link }}</a>
              </div>
              <div v-else class="text-gray-600 dark:text-gray-400" >-</div>
            </div>
            
            <div class="mb-4">
              <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Time Used</label>
              <div v-if="logtimeSegments.length > 0" class="flex flex-col sm:flex-row items-center gap-6 mt-2 bg-indigo-50 dark:bg-slate-800/50 p-4 rounded-lg border border-indigo-100 dark:border-slate-700">
                <div class="relative w-32 h-32 shrink-0">
                   <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                      <circle cx="50" cy="50" r="40" fill="transparent" stroke-width="15" class="stroke-gray-200 dark:stroke-gray-700" />
                      <circle 
                        v-for="seg in logtimeSegments" 
                        :key="seg.id"
                        cx="50" cy="50" r="40" 
                        fill="transparent" 
                        stroke-width="15" 
                        :stroke="seg.color"
                        :stroke-dasharray="seg.strokeDasharray"
                        :stroke-dashoffset="seg.strokeDashoffset"
                        class="transition-all duration-500 ease-out hover:opacity-80"
                      />
                   </svg>
                   <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                      <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ totalTimeUsed }}</span>
                      <span class="text-[0.6rem] text-gray-500 uppercase">Hours</span>
                   </div>
                </div>
                <div class="flex-1 w-full min-w-0">
                  <div class="flex flex-col gap-2">
                    <div v-for="seg in logtimeSegments" :key="seg.id" class="flex items-center justify-between text-sm">
                      <div class="flex items-center gap-2 min-w-0">
                        <span class="w-3 h-3 rounded-full shrink-0" :style="{ backgroundColor: seg.color }"></span>
                        <span class="text-gray-700 dark:text-gray-300 font-medium truncate" :title="seg.name">{{ seg.name }}</span>
                      </div>
                      <div class="flex items-center gap-3 shrink-0">
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ seg.percentage }}%</span>
                        <span class="font-bold text-gray-800 dark:text-gray-200 tabular-nums">{{ seg.time }}h</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <p v-else class="text-gray-800 dark:text-gray-200 mt-1 font-mono">
                {{ totalTimeUsed ? `${totalTimeUsed} hours` : '0 hours' }}
              </p>
            </div>
          </div>

          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Pull Request & Comments</h3>
            
            <div v-for="pr in prs" :key="pr.id" class="border border-indigo-400 dark:border-indigo-800 rounded-md pt-4 px-4 mb-4 bg-white dark:bg-gray-800" :class="{ 'pb-4': !pr.replies?.length, 'pb-2': pr.replies?.length }">
              
              <div class="flex justify-between items-start mb-2 border-b border-gray-100 dark:border-gray-700 pb-2">
                <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">
                  {{ getNameUser(pr.from) }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                  {{ formatDate(pr.created_at, true) }}
                </span>
              </div>

              <div class="flex flex-col">
                <div v-if="pr.pr_links && pr.pr_links.length > 0" class="mb-2">
                   <div v-for="link in pr.pr_links" :key="link">
                    <a :href="'//' + link" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm break-all">🔗 {{ link }}</a>
                  </div>
                </div>
                
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap text-sm mb-3">{{ pr.comment }}</p>
                
                <div class="flex justify-end">
                   <button @click="handleReply(pr.id)" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 hover:underline">
                     Reply
                   </button>
                </div>
              </div>

              <div v-if="pr.replies && pr.replies.length > 0" class="mt-3 pl-4 border-l-2 border-indigo-200 dark:border-gray-600 space-y-3">
                <div v-for="r in pr.replies" :key="r.id" class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-md">
                  <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold text-xs text-gray-700 dark:text-gray-300">
                      {{ r.user ? r.user.name : getNameUser(r.from) }}
                    </span>
                    <span class="text-[10px] text-gray-400">
                      {{ formatDate(r.created_at, true) }}
                    </span>
                  </div>
                  
                  <div v-if="r.pr_links && r.pr_links.length > 0" class="mb-1">
                     <div v-for="link in r.pr_links" :key="link">
                      <a :href="'//' + link" target="_blank" class="text-indigo-500 dark:text-indigo-300 hover:underline text-xs break-all">🔗 {{ link }}</a>
                    </div>
                  </div>

                  <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap text-xs">{{ r.comment }}</p>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
/* Animasi tombol floating (mobile) */
.button-list-enter-active,
.button-list-leave-active {
  transition: all 0.3s ease;
}
.button-list-enter-from,
.button-list-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
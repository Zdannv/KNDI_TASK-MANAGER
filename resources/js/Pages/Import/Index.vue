<script setup>
import Cloud from '@/Components/Icon/Cloud.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const form = useForm({
  file: null,
});

const fileInput = ref(null);
const isDragging = ref(false);
const isLoaded = ref(false);

onMounted(() => {
  setTimeout(() => {
    isLoaded.value = true;
  }, 100);
});

const submitForm = () => {
  if (!form.file) {
    alert('Please select a file first.');
    return;
  }
  form.post(route('import.store'));
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  form.file = file || null;
  updateFileLabel(file);
};

const handleDrop = (e) => {
  e.preventDefault();
  isDragging.value = false;
  const file = e.dataTransfer.files[0];
  if (file) {
    form.file = file;
    updateFileLabel(file);
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.value.files = dt.files;
  }
};

const handleDragOver = (e) => {
  e.preventDefault();
  isDragging.value = true;
};

const handleDragLeave = () => {
  isDragging.value = false;
};

const updateFileLabel = (file) => {
  const label = document.getElementById('file-label');
  if (file) {
    label.textContent = file.name;
  } else {
    label.textContent = 'Choose a file or drag it here';
  }
};

const triggerFileInput = () => {
  fileInput.value.click();
};

const downloadTemplate = () => {
  window.location.href = route('import.template');
};
</script>

<template>
  <Head title="Import Data" />
  <AuthenticatedLayout>
    <!-- Main Content-->
    <div
      class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 transition-all duration-700 ease-out"
      :class="{ 'opacity-100': isLoaded, 'translate-y-12 opacity-0': !isLoaded }"
    >
      <div class="w-full max-w-md">
        <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-sm border border-slate-200 dark:border-slate-800 shadow-lg dark:shadow-sm shadow-indigo-500 dark:shadow-indigo-800 rounded-xl p-8 space-y-6">
          
          <!-- Header -->
          <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
              Upload File
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Drag & drop your file here, or click to browse
            </p>
          </div>

          <!-- Upload Area -->
          <div
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @click="triggerFileInput"
            :class="[
              'bg-slate-50 dark:bg-gray-800 border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-all',
              isDragging
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30'
                : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'
            ]"
          >
            <input
              ref="fileInput"
              type="file"
              @change="handleFileChange"
              class="hidden"
              accept=".xlsx,.xls,.csv"
            />
            <svg
              class="mx-auto h-12 w-12 text-gray-400"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 48 48"
              aria-hidden="true"
            >
              <path
                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
            <p
              id="file-label"
              class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200"
            >
              Choose a file or drag it here
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              XLSX, XLS, CSV up to 10MB
            </p>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 justify-center">
            <button
              @click.prevent="downloadTemplate"
              type="button"
              class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2-8H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2z" />
              </svg>
              Download Template
            </button>
            <button
              @click.prevent="submitForm"
              type="button"
              :disabled="!form.file"
              :class="[
                'flex-1 inline-flex justify-center items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all',
                form.file
                  ? 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500'
                  : 'bg-gray-400 cursor-not-allowed opacity-60'
              ]"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
              </svg>
              Import File
            </button>
          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
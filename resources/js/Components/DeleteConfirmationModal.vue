<script setup>
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: "Delete Confirmation"
    },
    message: {
        type: String,
        default: "Are You Sure Want To Delete This Item?"
    },
    processing: Boolean,
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :show="show" @close="emit('close')" max-width="sm">
        <div class="p-6 bg-white/50 dark:bg-slate-800 text-black dark:text-white">
            <div class="text-center">
                <h1 class="text-lg font-bold text-black dark:text-white mb-2">
                    {{ title }}
                </h1>

                <p class="text-sm text-black dark:text-white mb-6">
                    {{ message }}
                </p>
            </div>

            <div class="flex justify-center gap-4">
                <button
                    @click="emit('close')"
                    class="px-4 py-2 bg-gray-400 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-200 transition"
                >
                    Cancel
                </button>
                <button
                    @click="emit('confirm')"
                    :disabled="processing"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-md transition disabled:opacity-50"
                >
                    {{ processing ? 'Deleting...' : 'Delete Now' }}
                </button>
            </div>
        </div>
    </Modal>
</template>
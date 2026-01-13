<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import moment from 'moment/moment';

const emit = defineEmits(['close']);

const props = defineProps({
    tasks: {}
});

const form = useForm({
    date: moment().format('YYYY-MM-DD'),
    task_id: '',
    time_used: ''
});

// --- PERBAIKAN DI SINI: Function Validasi ---
const validateTime = () => {
    if (form.time_used < 0) {
        form.time_used = 0;
    }
};

const submitForm = () => {
    form.post(route('logtime.store'), {
        onFinish: () => {
            emit('close');
        },
    });
};

const cancel = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Logtime</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <div>
                <InputLabel for="date" value="Date" />
                <input
                    type="date"
                    id="date"
                    class="mt-1 block w-full rounded-md"
                    v-model="form.date"
                    required
                    autocomplete="date"
                />
                <InputError class="mt-2" :message="form.errors.date" />
            </div>

            <div>
                <InputLabel for="type" value="Task" />
                <SelectInput
                    id="type"
                    v-model="form.task_id"
                    :options="tasks"
                    label="issue"
                    valueKey="id"
                    class="mt-1 block w-full"
                    placeholder="Search or select a task..."
                    :required="true"
                />
                <InputError class="mt-2" :message="form.errors.task_id" />
            </div>

            <div>
                <InputLabel for="time_used" value="Time used" />
                <TextInput
                    id="time_used"
                    type="number"
                    class="mt-1 block w-full"
                    v-model="form.time_used"
                    required
                    autofocus
                    autocomplete="time_used"
                    min="0"
                    @input="validateTime" 
                />
                <InputError class="mt-2" :message="form.errors.time_used" />
            </div>

            <div class="flex justify-end gap-4">
                <button
                    type="button"
                    @click="cancel"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</template>
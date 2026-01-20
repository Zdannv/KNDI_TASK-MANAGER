<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import Close from '../Icon/Close.vue';
import Plus from '../Icon/Plus.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';

const emit = defineEmits(['close']);

const props = defineProps({
    task: {},
    pg: {},
});

const availableReviewers = computed(() => {
    const assignProgrammerId = props.task?.programmer || [];

    if (Array.isArray(props.pg)) {
        return props.pg.filter(user => !assignProgrammerId.includes(user.id));
    }
});

const initialReviewers = Array.isArray(props.task?.reviewer)
    ? props.task.reviewer
    : [''];

const form = useForm({
    reviewer: initialReviewers,
    _method: 'PUT',
});

const submitForm = () => {
    form.reviewer = form.reviewer.filter(value => value !== '');

    form.post(route('task.prTask', props.task.id), {
        onFinish: () => emit('close'),
    });
};

const addReviewer = () => {
    form.reviewer.push('');
};

const removeReviewer = (index) => {
    if (form.reviewer.length > 1) {
        form.reviewer.splice(index, 1);
    } else {
        form.reviewer = []
    }
};

const cancel = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Task's Reviewer</h3>
        <form @submit.prevent="submitForm" class="space-y-4">

            <div>
                <InputLabel value="Reviewers" />
                <div v-for="(link, index) in form.reviewer" :key="form.reviewer[index]" class="mt-1 flex items-center gap-2">
                    <SelectInput
                        :id="'reviewer_' + index"
                        v-model="form.reviewer[index]"
                        :options="availableReviewers"
                        :array="form.reviewer"
                        label="name"
                        valueKey="id"
                        class="mt-1 block w-full"
                        placeholder="Search or select a Reviewer..."
                    />
                    <button
                        type="button"
                        @click="removeReviewer(index)"
                        class="px-3 py-2 text-gray-600 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-500 rounded-md"
                    >
                        <Close />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.reviewer" />
                <button
                    type="button"
                    @click="addReviewer"
                    class="mt-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                >
                    <Plus />
                </button>
            </div>

            <div class="flex justify-end gap-4">
                <button
                    type="button"
                    @click="cancel"
                    :disabled="form.processing"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 disabled:opacity-50"
                >
                    Cancel
                </button>
                
                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="{ 'opacity-25 cursor-not-allowed': form.processing }"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-all duration-200"
                >
                    {{ form.processing ? 'Saving...' : 'Save' }}
                </button>
            </div>
        </form>
    </div>
</template>
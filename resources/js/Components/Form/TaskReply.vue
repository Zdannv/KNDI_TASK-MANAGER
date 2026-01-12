<script setup>
import { useForm } from '@inertiajs/vue3';
import Close from '../Icon/Close.vue';
import Plus from '../Icon/Plus.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const emit = defineEmits(['close']);

const props = defineProps({
    comment: {},
});

const form = useForm({
    pr_links: [''],
    comment: ''
});

const submitForm = () => {
    form.pr_links = form.pr_links.filter(value => value !== '');

    form.post(route('task.replyTask', props.comment.id), {
        onFinish: () => emit('close'),
    });
};

const addLink = () => {
    form.pr_links.push('');
};

const removeLink = (index) => {
    if (form.pr_links.length > 1) {
        form.pr_links.splice(index, 1);
    } else {
        form.pr_links = []
    }
};

const cancel = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Reply Comment</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Links Field -->
            <div>
                <InputLabel value="Links" />
                <div v-for="(link, index) in form.pr_links" :key="index" class="mt-1 flex items-center gap-2">
                    <TextInput
                        :id="'pr_link_' + index"
                        type="text"
                        class="block w-full"
                        v-model="form.pr_links[index]"
                        :placeholder="'Link ' + (index + 1)"
                    />
                    <button
                        type="button"
                        @click="removeLink(index)"
                        class="px-3 py-2 text-gray-600 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-500 rounded-md"
                    >
                        <Close />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.pr_links" />
                <button
                    type="button"
                    @click="addLink"
                    class="mt-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                >
                    <Plus />
                </button>
            </div>

            <!-- Comment Field -->
            <div>
                <InputLabel for="comment" value="Comment" />
                <textarea
                    id="comment"
                    class="mt-1 block w-full rounded-md"
                    v-model="form.comment"
                    autocomplete="comment"
                />
                <InputError class="mt-2" :message="form.errors.comment" />
            </div>

            <!-- Form Actions -->
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
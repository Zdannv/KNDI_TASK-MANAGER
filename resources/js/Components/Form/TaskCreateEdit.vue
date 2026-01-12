<script setup>
import { useForm } from '@inertiajs/vue3';
import Close from '../Icon/Close.vue';
import Plus from '../Icon/Plus.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import moment from 'moment';

const emit = defineEmits(['close']);

const props = defineProps({
    task: {},
    projects: {},
    projectId: '',
    isEditMode: Boolean,
});

const initialLinks = props.isEditMode && Array.isArray(props.task?.related_links)
    ? props.task.related_links
    : [''];

const form = useForm({
    project_id: props.task?.project_id || Number(props.projectId)  || '',
    issue: props.task?.issue || '',
    type: props.task?.type || '',
    ticket_link: props.task?.ticket_link || '',
    related_links: initialLinks,
    description: props.task?.description || '',
    start_date: props.task?.start_date ? moment(props.task?.start_date).format('YYYY-MM-DD') : undefined,
    due_date: props.task?.due_date ? moment(props.task?.due_date).format('YYYY-MM-DD') : undefined,
    _method: props.isEditMode ? 'PUT' : undefined,
});

const submitForm = () => {
    form.related_links = form.related_links.filter(link => link.trim() !== '');

    const routeName = props.isEditMode ? 'task.editData' : 'task.store';
    const routeParams = props.isEditMode ? props.task.id : undefined;

    form.post(route(routeName, routeParams), {
        onFinish: () => emit('close'),
    });
};

const addRelatedLink = () => {
    form.related_links.push('');
};

const removeRelatedLink = (index) => {
    if (form.related_links.length > 1) {
        form.related_links.splice(index, 1);
    } else {
        form.related_links = []
    }
};

const cancel = () => {
    form.reset();
    emit('close');
};

const types = [
    { id: 'low', name: 'low' },
    { id: 'normal', name: 'normal' },
    { id: 'high', name: 'high' },
];
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ isEditMode ? 'Edit Task' : 'Add New Task' }}</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Project Field -->
            <div>
                <InputLabel for="type" value="Project" />
                <SelectInput
                    id="type"
                    v-model="form.project_id"
                    :options="projects"
                    label="name"
                    valueKey="id"
                    class="mt-1 block w-full"
                    placeholder="Search or select a project..."
                    :required="true"
                />
                <InputError class="mt-2" :message="form.errors.project_id" />
            </div>

            <!-- Issue Field -->
            <div>
                <InputLabel for="issue" value="Issue" />
                <TextInput
                    id="issue"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.issue"
                    required
                    autofocus
                    autocomplete="issue"
                />
                <InputError class="mt-2" :message="form.errors.issue" />
            </div>

            <!-- Type Field -->
            <div>
                <InputLabel for="type" value="Type" />
                <SelectInput
                    id="type"
                    v-model="form.type"
                    :options="types"
                    label="name"
                    valueKey="id"
                    class="mt-1 block w-full"
                    placeholder="Search or select a type..."
                />
                <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <!-- Ticket Link Field -->
            <div>
                <InputLabel for="ticket_link" value="Ticket link" />
                <TextInput
                    id="ticket_link"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.ticket_link"
                    required
                    autocomplete="ticket_link"
                />
                <InputError class="mt-2" :message="form.errors.ticket_link" />
            </div>

            <!-- Related Links Field -->
            <div>
                <InputLabel value="Related Links" />
                <div v-for="(link, index) in form.related_links" :key="index" class="mt-1 flex items-center gap-2">
                    <TextInput
                        :id="'related_link_' + index"
                        type="text"
                        class="block w-full"
                        v-model="form.related_links[index]"
                        :placeholder="'Related link ' + (index + 1)"
                    />
                    <button
                        type="button"
                        @click="removeRelatedLink(index)"
                        class="px-3 py-2 text-gray-600 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-500 rounded-md"
                    >
                        <Close />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.related_links" />
                <button
                    type="button"
                    @click="addRelatedLink"
                    class="mt-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                >
                    <Plus />
                </button>
            </div>

            <!-- Description Field -->
            <div>
                <InputLabel for="description" value="Description" />
                <textarea
                    id="description"
                    class="mt-1 block w-full rounded-md"
                    v-model="form.description"
                    autocomplete="description"
                />
                <InputError class="mt-2" :message="form.errors.description" />
            </div>

            <!-- Start and Due Date Fields -->
            <div class="flex justify-between gap-4">
                <!-- Start Date Field -->
                <div class="w-1/2">
                    <InputLabel for="start_date" value="Start Date" />
                    <input
                        type="date"
                        id="start_date"
                        class="mt-1 block w-full rounded-md"
                        v-model="form.start_date"
                        required
                        autocomplete="start_date"
                    />
                    <InputError class="mt-2" :message="form.errors.start_date" />
                </div>

                <!-- Due Date Field -->
                <div class="w-1/2">
                    <InputLabel for="due_date" value="Due Date" />
                    <input
                        type="date"
                        id="due_date"
                        class="mt-1 block w-full rounded-md"
                        v-model="form.due_date"
                        autocomplete="due_date"
                    />
                    <InputError class="mt-2" :message="form.errors.due_date" />
                </div>
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
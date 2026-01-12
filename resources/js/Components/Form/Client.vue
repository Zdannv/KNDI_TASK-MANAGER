<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const emit = defineEmits(['close']);

const props = defineProps({
    client: {},
    isEditMode: Boolean,
});

const form = useForm({
    name: props.client?.name || '',
    _method: props.isEditMode ? 'PUT' : undefined,
});

const submitForm = () => {
    const routeName = props.isEditMode ? 'client.update' : 'client.store';
    const routeParams = props.isEditMode ? props.client.id : undefined;

    form.post(route(routeName, routeParams), {
        onFinish: () => emit('close'),
    });
};

const cancel = () => {
    form.reset('name');
    emit('close');
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ isEditMode ? 'Edit Client' : 'Add New Client' }}</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Name Field -->
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
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
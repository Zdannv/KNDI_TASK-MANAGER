<script setup>
import { useForm } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

const emit = defineEmits(['close']);

const form = useForm({
    skill: '',
});

const submitForm = () => {
    form.post(route('skill.store'), {
        onFinish: () => {
            emit('close');
        },
    });
};

const cancel = () => {
    form.reset('skill');
    emit('close');
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Skill</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Skill Field -->
            <div>
                <InputLabel for="skill" value="Skill" />

                <TextInput
                    id="skill"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.skill"
                    required
                    autofocus
                    autocomplete="skill"
                />

                <InputError class="mt-2" :message="form.errors.skill" />
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
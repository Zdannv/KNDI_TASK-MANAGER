<script setup>
import { useForm } from '@inertiajs/vue3';
import Close from '../Icon/Close.vue';
import Plus from '../Icon/Plus.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';

const emit = defineEmits(['close']);

const props = defineProps({
    task: {},
    pl: {},
    co: {},
    pg: {},
    ds: {},
});

const initialComunicators = Array.isArray(props.task?.communicator)
    ? props.task.communicator
    : [''];

const initialProgrammers = Array.isArray(props.task?.programmer)
    ? props.task.programmer
    : [''];

const initialDesigners = Array.isArray(props.task?.designer)
    ? props.task.designer
    : [''];

const form = useForm({
    pl: props.task?.pl || '',
    communicator: initialComunicators,
    programmer: initialProgrammers,
    designer: initialDesigners,
    _method: 'PUT',
});

const submitForm = () => {
    form.communicator = form.communicator.filter(value => value !== '');
    form.programmer = form.programmer.filter(value => value !== '');
    form.designer = form.designer.filter(value => value !== '');

    form.post(route('task.assignTask', props.task.id), {
        onFinish: () => emit('close'),
    });
};

const addCommunicator = () => {
    form.communicator.push('');
};

const removeCommunicator = (index) => {
    if (form.communicator.length > 1) {
        form.communicator.splice(index, 1);
    } else {
        form.communicator = []
    }
};

const addProgrammer = () => {
    form.programmer.push('');
};

const removeProgrammer = (index) => {
    if (form.programmer.length > 1) {
        form.programmer.splice(index, 1);
    } else {
        form.programmer = []
    }
};

const addDesigner = () => {
    form.designer.push('');
};

const removeDesigner = (index) => {
    if (form.designer.length > 1) {
        form.designer.splice(index, 1);
    } else {
        form.designer = []
    }
};

const cancel = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Task</h3>
        <form @submit.prevent="submitForm" class="space-y-4">
            <!-- Leader Field -->
            <div>
                <InputLabel for="type" value="Project Leader" />
                <SelectInput
                    id="type"
                    v-model="form.pl"
                    :options="pl"
                    label="name"
                    valueKey="id"
                    class="mt-1 block w-full"
                    placeholder="Search or select a project leader..."
                />
                <InputError class="mt-2" :message="form.errors.pl" />
            </div>

            <!-- Communicators Field -->
            <div>
                <InputLabel value="Communicators" />
                <div v-for="(link, index) in form.communicator" :key="form.communicator[index]" class="mt-1 flex items-center gap-2">
                    <SelectInput
                        :id="'related_link_' + index"
                        v-model="form.communicator[index]"
                        :options="co"
                        :array="form.communicator"
                        label="name"
                        valueKey="id"
                        class="mt-1 block w-full"
                        placeholder="Search or select a Communicator..."
                    />
                    <button
                        type="button"
                        @click="removeCommunicator(index)"
                        class="px-3 py-2 text-gray-600 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-500 rounded-md"
                    >
                        <Close />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.communicator" />
                <button
                    type="button"
                    @click="addCommunicator"
                    class="mt-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                >
                    <Plus />
                </button>
            </div>

            <!-- Programmers Field -->
            <div>
                <InputLabel value="Programmers" />
                <div v-for="(link, index) in form.programmer" :key="form.programmer[index]" class="mt-1 flex items-center gap-2">
                    <SelectInput
                        :id="'related_link_' + index"
                        v-model="form.programmer[index]"
                        :options="pg"
                        :array="form.programmer"
                        label="name"
                        valueKey="id"
                        class="mt-1 block w-full"
                        placeholder="Search or select a Programmer..."
                    />
                    <button
                        type="button"
                        @click="removeProgrammer(index)"
                        class="px-3 py-2 text-gray-600 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-500 rounded-md"
                    >
                        <Close />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.programmer" />
                <button
                    type="button"
                    @click="addProgrammer"
                    class="mt-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                >
                    <Plus />
                </button>
            </div>

            <!-- Designer Field -->
            <div>
                <InputLabel value="Designer" />
                <div v-for="(link, index) in form.designer" :key="form.designer[index]" class="mt-1 flex items-center gap-2">
                    <SelectInput
                        :id="'related_link_' + index"
                        v-model="form.designer[index]"
                        :options="ds"
                        :array="form.designer"
                        label="name"
                        valueKey="id"
                        class="mt-1 block w-full"
                        placeholder="Search or select a Designer..."
                    />
                    <button
                        type="button"
                        @click="removeDesigner(index)"
                        class="px-3 py-2 text-gray-600 dark:text-gray-100 text-sm border border-gray-300 dark:border-gray-500 rounded-md"
                    >
                        <Close />
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.designer" />
                <button
                    type="button"
                    @click="addDesigner"
                    class="mt-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700"
                >
                    <Plus />
                </button>
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
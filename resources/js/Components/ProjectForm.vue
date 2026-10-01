<script setup>
import { reactive } from 'vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
    statuses: {
        type: Array,
        required: true,
    },
    priorities: {
        type: Array,
        required: true,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    message: {
        type: String,
        default: '',
    },
    submitting: {
        type: Boolean,
        default: false,
    },
    submitLabel: {
        type: String,
        default: 'Save project',
    },
});

const emit = defineEmits(['submit']);

const form = reactive({
    clientName: props.project?.clientName ?? '',
    projectName: props.project?.projectName ?? '',
    description: props.project?.description ?? '',
    status: props.project?.status ?? props.statuses[0] ?? '',
    priority: props.project?.priority ?? props.priorities[1] ?? props.priorities[0] ?? '',
    startDate: props.project?.startDate ?? '',
    dueDate: props.project?.dueDate ?? '',
});

const fieldClass = 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-stone-900 shadow-sm outline-none transition focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20';

function fieldError(name) {
    const value = props.errors?.[name];

    if (Array.isArray(value)) {
        return value[0] ?? '';
    }

    return value || '';
}

function inputClass(name) {
    return [fieldClass, fieldError(name) ? 'border-rose-400' : 'border-stone-300'];
}

function submit() {
    emit('submit', {
        clientName: form.clientName,
        projectName: form.projectName,
        description: form.description,
        status: form.status,
        priority: form.priority,
        startDate: form.startDate,
        dueDate: form.dueDate,
    });
}
</script>

<template>
    <form class="space-y-4" novalidate @submit.prevent="submit">
        <p v-if="message" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
            {{ message }}
        </p>

        <FormField label="Client name" :error="fieldError('clientName')" required>
            <input v-model="form.clientName" type="text" maxlength="255" autocomplete="organization" :class="inputClass('clientName')" />
        </FormField>

        <FormField label="Project name" :error="fieldError('projectName')" required>
            <input v-model="form.projectName" type="text" maxlength="255" :class="inputClass('projectName')" />
        </FormField>

        <FormField label="Description" :error="fieldError('description')">
            <textarea v-model="form.description" rows="4" maxlength="5000" :class="inputClass('description')" />
        </FormField>

        <div class="grid gap-4 sm:grid-cols-2">
            <FormField label="Status" :error="fieldError('status')" required>
                <select v-model="form.status" :class="inputClass('status')">
                    <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                </select>
            </FormField>

            <FormField label="Priority" :error="fieldError('priority')" required>
                <select v-model="form.priority" :class="inputClass('priority')">
                    <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                </select>
            </FormField>

            <FormField label="Start date" :error="fieldError('startDate')" required>
                <input v-model="form.startDate" type="date" :class="inputClass('startDate')" />
            </FormField>

            <FormField label="Due date" :error="fieldError('dueDate')" required>
                <input v-model="form.dueDate" type="date" :class="inputClass('dueDate')" />
            </FormField>
        </div>

        <div class="flex justify-end pt-2">
            <button
                type="submit"
                class="rounded-lg bg-teal-800 px-4 py-2 text-sm font-medium text-white hover:bg-teal-900 disabled:opacity-60"
                :disabled="submitting"
            >
                {{ submitting ? 'Saving...' : submitLabel }}
            </button>
        </div>
    </form>
</template>

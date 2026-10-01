<script setup>
import PriorityBadge from '@/Components/PriorityBadge.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { formatDate, isOverdue } from '@/lib/dates';

defineProps({
    project: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <dl class="space-y-4 text-sm">
        <div>
            <dt class="text-stone-500">Client</dt>
            <dd class="mt-1 font-medium text-stone-900">{{ project.clientName }}</dd>
        </div>
        <div>
            <dt class="text-stone-500">Description</dt>
            <dd class="mt-1 leading-6 text-stone-800">{{ project.description || 'No description provided.' }}</dd>
        </div>
        <div class="flex flex-wrap gap-6">
            <div>
                <dt class="text-stone-500">Status</dt>
                <dd class="mt-1"><StatusBadge :status="project.status" /></dd>
            </div>
            <div>
                <dt class="text-stone-500">Priority</dt>
                <dd class="mt-1"><PriorityBadge :priority="project.priority" /></dd>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-stone-500">Start date</dt>
                <dd class="mt-1 text-stone-900">{{ formatDate(project.startDate) }}</dd>
            </div>
            <div>
                <dt class="text-stone-500">Due date</dt>
                <dd class="mt-1" :class="isOverdue(project) ? 'font-medium text-rose-700' : 'text-stone-900'">
                    {{ formatDate(project.dueDate) }}
                    <span v-if="isOverdue(project)"> · Overdue</span>
                </dd>
            </div>
        </div>
    </dl>
</template>

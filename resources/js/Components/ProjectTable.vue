<script setup>
import PriorityBadge from '@/Components/PriorityBadge.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { formatDate, isOverdue } from '@/lib/dates';

defineProps({
    projects: {
        type: Array,
        required: true,
    },
    sort: {
        type: String,
        required: true,
    },
    direction: {
        type: String,
        required: true,
    },
});

defineEmits(['sort', 'view', 'edit', 'delete']);

function ariaSort(active, direction) {
    if (!active) {
        return 'none';
    }

    return direction === 'asc' ? 'ascending' : 'descending';
}
</script>

<template>
    <div v-if="projects.length === 0" class="rounded-2xl border border-dashed border-stone-300 bg-white px-6 py-16 text-center">
        <p class="text-base font-medium text-stone-900">No projects match these filters</p>
        <p class="mt-1 text-sm text-stone-500">Try another search, or clear the status and priority filters.</p>
    </div>

    <div v-else>
        <div class="hidden overflow-hidden rounded-2xl border border-stone-200 bg-white md:block">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-stone-200 bg-stone-50 text-stone-500">
                    <tr>
                        <th class="px-4 py-3 font-medium" :aria-sort="ariaSort(sort === 'projectName', direction)">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-stone-900" @click="$emit('sort', 'projectName')">
                                Project
                                <span v-if="sort === 'projectName'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 font-medium" :aria-sort="ariaSort(sort === 'clientName', direction)">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-stone-900" @click="$emit('sort', 'clientName')">
                                Client
                                <span v-if="sort === 'clientName'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 font-medium" :aria-sort="ariaSort(sort === 'status', direction)">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-stone-900" @click="$emit('sort', 'status')">
                                Status
                                <span v-if="sort === 'status'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 font-medium" :aria-sort="ariaSort(sort === 'priority', direction)">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-stone-900" @click="$emit('sort', 'priority')">
                                Priority
                                <span v-if="sort === 'priority'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 font-medium" :aria-sort="ariaSort(sort === 'dueDate', direction)">
                            <button type="button" class="inline-flex items-center gap-1 hover:text-stone-900" @click="$emit('sort', 'dueDate')">
                                Due
                                <span v-if="sort === 'dueDate'">{{ direction === 'asc' ? '↑' : '↓' }}</span>
                            </button>
                        </th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <tr v-for="project in projects" :key="project.id" class="align-top">
                        <td class="px-4 py-4">
                            <p class="font-medium text-stone-900">{{ project.projectName }}</p>
                            <p class="mt-1 max-w-sm text-stone-500">{{ project.description || 'No description' }}</p>
                        </td>
                        <td class="px-4 py-4 text-stone-700">{{ project.clientName }}</td>
                        <td class="px-4 py-4"><StatusBadge :status="project.status" /></td>
                        <td class="px-4 py-4"><PriorityBadge :priority="project.priority" /></td>
                        <td class="px-4 py-4" :class="isOverdue(project) ? 'font-medium text-rose-700' : 'text-stone-700'">
                            <p>{{ formatDate(project.dueDate) }}</p>
                            <p v-if="isOverdue(project)" class="text-xs">Overdue</p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex justify-end gap-3">
                                <button type="button" class="font-medium text-teal-800 hover:text-teal-950" @click="$emit('view', project)">View</button>
                                <button type="button" class="font-medium text-stone-700 hover:text-stone-950" @click="$emit('edit', project)">Edit</button>
                                <button type="button" class="font-medium text-rose-700 hover:text-rose-900" @click="$emit('delete', project)">Delete</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="space-y-3 md:hidden">
            <article v-for="project in projects" :key="project.id" class="rounded-2xl border border-stone-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-medium text-stone-950">{{ project.projectName }}</h2>
                        <p class="mt-1 text-sm text-stone-500">{{ project.clientName }}</p>
                    </div>
                    <PriorityBadge :priority="project.priority" />
                </div>
                <p class="mt-3 text-sm leading-6 text-stone-600">{{ project.description || 'No description' }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <StatusBadge :status="project.status" />
                    <span class="text-sm" :class="isOverdue(project) ? 'font-medium text-rose-700' : 'text-stone-500'">
                        Due {{ formatDate(project.dueDate) }}
                        <template v-if="isOverdue(project)"> · Overdue</template>
                    </span>
                </div>
                <div class="mt-4 flex gap-4 text-sm">
                    <button type="button" class="font-medium text-teal-800" @click="$emit('view', project)">View</button>
                    <button type="button" class="font-medium text-stone-700" @click="$emit('edit', project)">Edit</button>
                    <button type="button" class="font-medium text-rose-700" @click="$emit('delete', project)">Delete</button>
                </div>
            </article>
        </div>
    </div>
</template>

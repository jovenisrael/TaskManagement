<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    statuses: {
        type: Array,
        required: true,
    },
    priorities: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['search', 'status', 'priority', 'create']);

const search = ref(props.filters.search ?? '');
let timer = null;

watch(
    () => props.filters.search,
    (value) => {
        search.value = value ?? '';
    },
);

function onSearch() {
    clearTimeout(timer);
    timer = setTimeout(() => emit('search', search.value), 300);
}

const selectClass = 'rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm text-stone-900 outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20';
</script>

<template>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-1 flex-col gap-3 sm:flex-row">
            <label class="relative min-w-0 flex-1">
                <span class="sr-only">Search projects</span>
                <input
                    v-model="search"
                    type="search"
                    placeholder="Search client, project, or description"
                    class="w-full rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm outline-none focus:border-teal-700 focus:ring-2 focus:ring-teal-700/20"
                    @input="onSearch"
                />
            </label>

            <label>
                <span class="sr-only">Filter by status</span>
                <select :value="filters.status" :class="selectClass" @change="emit('status', $event.target.value)">
                    <option value="">All statuses</option>
                    <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
                </select>
            </label>

            <label>
                <span class="sr-only">Filter by priority</span>
                <select :value="filters.priority" :class="selectClass" @change="emit('priority', $event.target.value)">
                    <option value="">All priorities</option>
                    <option v-for="priority in priorities" :key="priority" :value="priority">{{ priority }}</option>
                </select>
            </label>
        </div>

        <button
            type="button"
            class="rounded-lg bg-teal-800 px-4 py-2 text-sm font-medium text-white hover:bg-teal-900"
            @click="emit('create')"
        >
            New project
        </button>
    </div>
</template>

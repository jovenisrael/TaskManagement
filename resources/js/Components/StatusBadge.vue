<script setup>
const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const tones = {
    Planning: 'bg-stone-100 text-stone-700 ring-stone-200',
    'In Progress': 'bg-teal-50 text-teal-800 ring-teal-200',
    'On Hold': 'bg-amber-50 text-amber-800 ring-amber-200',
    Completed: 'bg-emerald-50 text-emerald-800 ring-emerald-200',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
        :class="tones[props.status] ?? 'bg-stone-100 text-stone-700 ring-stone-200'"
    >
        {{ status }}
    </span>
</template>

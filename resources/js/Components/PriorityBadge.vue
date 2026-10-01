<script setup>
const props = defineProps({
    priority: {
        type: String,
        required: true,
    },
});

const tones = {
    Low: 'bg-stone-100 text-stone-600 ring-stone-200',
    Medium: 'bg-sky-50 text-sky-800 ring-sky-200',
    High: 'bg-rose-50 text-rose-800 ring-rose-200',
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset"
        :class="tones[props.priority] ?? 'bg-stone-100 text-stone-700 ring-stone-200'"
    >
        {{ priority }}
    </span>
</template>

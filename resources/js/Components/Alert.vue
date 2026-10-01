<script setup>
defineProps({
    message: {
        type: String,
        default: '',
    },
    tone: {
        type: String,
        default: 'success',
    },
});

defineEmits(['close']);
</script>

<template>
    <div
        v-if="message"
        class="flex items-start justify-between gap-4 rounded-lg border px-4 py-3 text-sm"
        :class="tone === 'error' ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-teal-200 bg-teal-50 text-teal-900'"
        role="status"
    >
        <p>{{ message }}</p>
        <button type="button" class="shrink-0 font-medium underline-offset-2 hover:underline" @click="$emit('close')">
            Dismiss
        </button>
    </div>
</template>

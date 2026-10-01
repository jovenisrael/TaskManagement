<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label class="block">
        <span class="mb-1.5 block text-sm font-medium text-stone-700">
            {{ label }}
            <span v-if="required" class="text-rose-700">*</span>
        </span>
        <slot />
        <p v-if="error" class="mt-1.5 text-sm text-rose-700">{{ error }}</p>
    </label>
</template>

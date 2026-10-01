<script setup>
import Modal from '@/Components/Modal.vue';

defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
    confirmLabel: {
        type: String,
        default: 'Delete',
    },
    busy: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :open="open" :title="title" @close="$emit('close')">
        <p class="text-sm leading-6 text-stone-600">{{ message }}</p>
        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                class="rounded-lg px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100"
                :disabled="busy"
                @click="$emit('close')"
            >
                Cancel
            </button>
            <button
                type="button"
                class="rounded-lg bg-rose-700 px-4 py-2 text-sm font-medium text-white hover:bg-rose-800 disabled:opacity-60"
                :disabled="busy"
                @click="$emit('confirm')"
            >
                {{ busy ? 'Deleting...' : confirmLabel }}
            </button>
        </div>
    </Modal>
</template>

<script setup>
import { onBeforeUnmount, ref, watch } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close']);
const panel = ref(null);

function onKeydown(event) {
    if (event.key === 'Escape') {
        emit('close');
    }
}

watch(
    () => props.open,
    (open) => {
        document.body.style.overflow = open ? 'hidden' : '';

        if (open) {
            document.addEventListener('keydown', onKeydown);
            requestAnimationFrame(() => panel.value?.focus());
        } else {
            document.removeEventListener('keydown', onKeydown);
        }
    },
);

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeydown);
});
</script>

<template>
    <Teleport to="body">
        <div v-if="open" class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center">
            <button type="button" class="absolute inset-0 bg-stone-950/40" aria-label="Close dialog" @click="emit('close')" />
            <div
                ref="panel"
                role="dialog"
                aria-modal="true"
                aria-labelledby="dialog-title"
                tabindex="-1"
                class="relative z-10 max-h-[90vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl outline-none"
            >
                <div class="mb-5 flex items-start justify-between gap-4">
                    <h2 id="dialog-title" class="text-lg font-semibold text-stone-950">{{ title }}</h2>
                    <button type="button" class="rounded-md px-2 py-1 text-sm text-stone-500 hover:bg-stone-100 hover:text-stone-900" @click="emit('close')">
                        Close
                    </button>
                </div>
                <slot />
            </div>
        </div>
    </Teleport>
</template>

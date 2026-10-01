<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div class="min-h-screen">
        <header class="border-b border-stone-200 bg-white">
            <div class="mx-auto flex max-w-6xl items-end justify-between gap-4 px-4 py-5 sm:px-6">
                <div>
                    <p class="text-xs font-semibold tracking-[0.18em] text-teal-800">Digital agency</p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-950">
                        {{ page.props.appName }}
                    </h1>
                </div>
                <p class="hidden text-sm text-stone-500 sm:block">Client work, deadlines, and priority</p>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6">
            <slot />
        </main>
    </div>
</template>

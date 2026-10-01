<script setup>
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import Alert from '@/Components/Alert.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import Modal from '@/Components/Modal.vue';
import ProjectDetail from '@/Components/ProjectDetail.vue';
import ProjectForm from '@/Components/ProjectForm.vue';
import ProjectTable from '@/Components/ProjectTable.vue';
import ProjectToolbar from '@/Components/ProjectToolbar.vue';
import { projectApi } from '@/lib/projects';

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
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

const page = usePage();
const mode = ref(null);
const selected = ref(null);
const detail = ref(null);
const detailLoading = ref(false);
const detailError = ref('');
const formErrors = ref({});
const formError = ref('');
const saving = ref(false);
const notice = ref('');
const noticeTone = ref('success');

const stats = computed(() => [
    { label: 'Showing', value: props.projects.length },
    { label: 'Planning', value: props.projects.filter((project) => project.status === 'Planning').length },
    { label: 'In progress', value: props.projects.filter((project) => project.status === 'In Progress').length },
    { label: 'On hold', value: props.projects.filter((project) => project.status === 'On Hold').length },
    { label: 'Completed', value: props.projects.filter((project) => project.status === 'Completed').length },
]);

const queryErrors = computed(() => Object.values(page.props.errors ?? {}).flat().filter(Boolean));

function apply(patch) {
    router.get('/', {
        search: props.filters.search ?? '',
        status: props.filters.status ?? '',
        priority: props.filters.priority ?? '',
        sort: props.filters.sort ?? 'dueDate',
        direction: props.filters.direction ?? 'asc',
        ...patch,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function closeDialogs() {
    mode.value = null;
    selected.value = null;
    detail.value = null;
    detailError.value = '';
    formErrors.value = {};
    formError.value = '';
}

function showNotice(message, tone = 'success') {
    notice.value = message;
    noticeTone.value = tone;
}

function openCreate() {
    closeDialogs();
    mode.value = 'create';
}

function openEdit(project) {
    closeDialogs();
    selected.value = project;
    mode.value = 'edit';
}

async function openView(project) {
    closeDialogs();
    selected.value = project;
    mode.value = 'view';
    detailLoading.value = true;

    try {
        const body = await projectApi.show(project.id);
        detail.value = body.data;
    } catch (error) {
        detailError.value = error.message;
    } finally {
        detailLoading.value = false;
    }
}

function openDelete(project) {
    closeDialogs();
    selected.value = project;
    mode.value = 'delete';
}

function onSort(column) {
    apply({
        sort: column,
        direction: props.filters.sort === column && props.filters.direction === 'asc' ? 'desc' : 'asc',
    });
}

async function saveProject(payload) {
    saving.value = true;
    formErrors.value = {};
    formError.value = '';

    try {
        if (mode.value === 'edit' && selected.value) {
            await projectApi.update(selected.value.id, payload);
            showNotice('Project updated.');
        } else {
            await projectApi.create(payload);
            showNotice('Project created.');
        }

        closeDialogs();
        router.reload({ preserveState: true, preserveScroll: true });
    } catch (error) {
        formErrors.value = error.errors ?? {};
        const fieldMessages = Object.values(formErrors.value).flat();
        formError.value = fieldMessages.length ? '' : error.message;
    } finally {
        saving.value = false;
    }
}

async function confirmDelete() {
    if (!selected.value) {
        return;
    }

    saving.value = true;

    try {
        await projectApi.destroy(selected.value.id);
        showNotice('Project deleted.');
        closeDialogs();
        router.reload({ preserveState: true, preserveScroll: true });
    } catch (error) {
        showNotice(error.message, 'error');
        closeDialogs();
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Head title="Projects" />

    <AppLayout>
        <div class="space-y-5">
            <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                <article v-for="stat in stats" :key="stat.label" class="rounded-2xl border border-stone-200 bg-white px-4 py-3">
                    <p class="text-xs font-medium tracking-wide text-stone-500 uppercase">{{ stat.label }}</p>
                    <p class="mt-1 text-2xl font-semibold text-stone-950">{{ stat.value }}</p>
                </article>
            </section>

            <Alert v-if="queryErrors.length" tone="error" :message="queryErrors[0]" @close="apply({ status: '', priority: '', sort: 'dueDate', direction: 'asc' })" />
            <Alert :message="notice" :tone="noticeTone" @close="notice = ''" />

            <section class="rounded-2xl border border-stone-200 bg-white p-4">
                <ProjectToolbar
                    :filters="filters"
                    :statuses="statuses"
                    :priorities="priorities"
                    @search="apply({ search: $event })"
                    @status="apply({ status: $event })"
                    @priority="apply({ priority: $event })"
                    @create="openCreate"
                />
            </section>

            <ProjectTable
                :projects="projects"
                :sort="filters.sort"
                :direction="filters.direction"
                @sort="onSort"
                @view="openView"
                @edit="openEdit"
                @delete="openDelete"
            />
        </div>

        <Modal :open="mode === 'create'" title="New project" @close="closeDialogs">
            <ProjectForm
                :statuses="statuses"
                :priorities="priorities"
                :errors="formErrors"
                :message="formError"
                :submitting="saving"
                submit-label="Create project"
                @submit="saveProject"
            />
        </Modal>

        <Modal :open="mode === 'edit'" title="Edit project" @close="closeDialogs">
            <ProjectForm
                v-if="selected"
                :key="selected.id"
                :project="selected"
                :statuses="statuses"
                :priorities="priorities"
                :errors="formErrors"
                :message="formError"
                :submitting="saving"
                submit-label="Save changes"
                @submit="saveProject"
            />
        </Modal>

        <Modal :open="mode === 'view'" :title="detail?.projectName || selected?.projectName || 'Project'" @close="closeDialogs">
            <p v-if="detailLoading" class="text-sm text-stone-500">Loading project...</p>
            <p v-else-if="detailError" class="text-sm text-rose-700">{{ detailError }}</p>
            <ProjectDetail v-else-if="detail" :project="detail" />
        </Modal>

        <ConfirmDialog
            :open="mode === 'delete'"
            title="Delete project"
            :message="selected ? `Delete ${selected.projectName} for ${selected.clientName}? This cannot be undone.` : ''"
            :busy="saving"
            @close="closeDialogs"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>

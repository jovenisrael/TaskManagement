export function formatDate(value) {
    if (!value) {
        return '';
    }

    const [year, month, day] = value.split('-').map(Number);
    const date = new Date(year, month - 1, day);

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    });
}

export function isOverdue(project) {
    if (!project?.dueDate || project.status === 'Completed') {
        return false;
    }

    const [year, month, day] = project.dueDate.split('-').map(Number);
    const due = new Date(year, month - 1, day);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    return due < today;
}

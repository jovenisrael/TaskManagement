export async function request(path, options = {}) {
    const response = await fetch(path, {
        ...options,
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(options.headers ?? {}),
        },
    });

    const text = await response.text();
    let body = null;

    if (text) {
        try {
            body = JSON.parse(text);
        } catch {
            body = { message: 'Unexpected response from the server.' };
        }
    }

    if (!response.ok) {
        const error = new Error(body?.message || 'Something went wrong.');
        error.status = response.status;
        error.errors = body?.errors ?? {};
        throw error;
    }

    return body;
}

export const projectApi = {
    show(id) {
        return request(`/projects/${id}`);
    },
    create(payload) {
        return request('/projects', {
            method: 'POST',
            body: JSON.stringify(payload),
        });
    },
    update(id, payload) {
        return request(`/projects/${id}`, {
            method: 'PUT',
            body: JSON.stringify(payload),
        });
    },
    destroy(id) {
        return request(`/projects/${id}`, {
            method: 'DELETE',
        });
    },
};

export async function fetchPosts() {
    const response = await fetch('http://localhost:8000/api/posts');
    return response.json();
}

export async function createPost(data) {
    const response = await fetch('http://localhost:8000/api/posts', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    return response.json();
}

export async function deletePost(id) {
    await fetch(`http://localhost:8000/api/posts/${id}`, { method: 'DELETE' });
}


export async function fetchSeasons() {
    const response = await fetch("http://localhost:8000/api/seasons"); 
    return response.json();
}

export async function createSeason(formData) {
    const response = await fetch("http://localhost:8000/api/seasons", { 
        method: "POST",
        body: formData,
    });
    return response.json();
}

export async function updateSeason(id, formData) {
    const response = await fetch(`http://localhost:8000/api/seasons/${id}`, { 
        method: "PUT",
        body: formData,
    });
    return response.json();
}

export async function deleteSeason(id) {
    await fetch(`http://localhost:8000/api/seasons/${id}`, { method: "DELETE" }); 
}

export async function fetchCategories() {
    const response = await fetch("http://localhost:8000/api/categories"); 
    return response.json();
}

export async function createCategory(formData) {
    const response = await fetch("http://localhost:8000/api/categories", { 
        method: "POST",
        body: formData,
    });
    return response.json();
}

export async function updateCategory(id, formData) {
    const response = await fetch(`http://localhost:8000/api/categories/${id}`, { 
        method: "PUT",
        body: formData,
    });
    return response.json();
}

export async function deleteCategory(id) {
    await fetch(`http://localhost:8000/api/categories/${id}`, { method: "DELETE" }); 
}
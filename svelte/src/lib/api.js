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

export async function fetchCategory(id) {
    const response = await fetch(`http://localhost:8000/api/categories/${id}`);
    if (!response.ok) {
        throw new Error(`Nepodarilo sa načítať kategóriu (HTTP ${response.status})`);
    }
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

export async function fetchRestaurant(id) {
    const response = await fetch(`http://localhost:8000/api/restaurants/${id}`);
    if (!response.ok) {
      throw new Error(`Nepodarilo sa načítať reštauráciu (HTTP ${response.status})`);
    }
    return response.json();
  }
  

export async function fetchRestaurants() {
    const response = await fetch("http://localhost:8000/api/restaurants"); 
    return response.json();
}

export async function createRestaurant(formData) {
    const response = await fetch("http://localhost:8000/api/restaurants", { 
        method: "POST",
        body: formData,
    });
    return response.json();
}

export async function updateRestaurant(id, formData) {
    formData.append('_method', 'PUT'); 

    const response = await fetch(`http://localhost:8000/api/restaurants/${id}`, {
        method: "POST", 
        body: formData,
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        }
    });

    return response.json();
}


export async function deleteRestaurant(id) {
    await fetch(`http://localhost:8000/api/restaurants/${id}`, { method: "DELETE" }); 
}

export async function fetchReviews() {
    const response = await fetch("http://localhost:8000/api/reviews"); 
    return response.json();
}
export async function fetchReview(id) {
    const response = await fetch(`http://localhost:8000/api/reviews/${id}`);
    if (!response.ok) {
        throw new Error(`Nepodarilo sa načítať recenziu (HTTP ${response.status})`);
    }
    return response.json();
}
export async function createReview(formData) {
    const response = await fetch("http://localhost:8000/api/reviews", { 
        method: "POST",
        body: formData,
    });
    return response.json();
}

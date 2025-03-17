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
export async function updatePost(postId, formData) {
    formData.append("_method", "PUT");
    const response = await fetch(`http://localhost:8000/api/posts/${postId}`, {
      method: "POST",
      body: formData
    });
    if (!response.ok) {
      throw new Error(`Nepodarilo sa upraviť príspevok (HTTP ${response.status}).`);
    }
    return response.json();
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
export async function updateReview(id, formData) {
    formData.append('_method', 'PUT'); 

    const response = await fetch(`http://localhost:8000/api/reviews/${id}`, {
        method: "POST", 
        body: formData,
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        }
    });

    return response.json();
}
export async function deleteReview(id, userEmail) {
    const response = await fetch(`http://localhost:8000/api/reviews/${id}`, {
        method: "DELETE",
        credentials: "include",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mail: userEmail }) 
    });

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || "Chyba pri mazaní recenzie.");
    }

    return response.json();
}
export async function fetchMainInfos() {
    const response = await fetch("http://localhost:8000/api/main-info");
    return response.json();
}
export async function updateMainInfo(id,data) {
    const response = await fetch("http://localhost:8000/api/main-info", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    });
    return response.json();
}

export async function fetchAdditionalInfos() {
    const response = await fetch("http://localhost:8000/api/additional-info");
    if (!response.ok) {
        throw new Error(`Nepodarilo sa načítať additional_info (HTTP ${response.status})`);
    }
    return response.json();
}

export async function createAdditionalInfo(formData) {
    const response = await fetch("http://localhost:8000/api/additional-info", {
      method: "POST",
      body: formData
    });
    if (!response.ok) {
      throw new Error("Chyba pri vytváraní additional_info.");
    }
    return response.json();
  }
  

export async function updateAdditionalInfo(id, formData) {
    formData.append("_method", "PUT");
    const response = await fetch(`http://localhost:8000/api/additional-info/${id}`, {
        method: "POST",
        body: formData
        });
        if (!response.ok) {
            throw new Error("Chyba pri úprave additional_info.");
        }
        return response.json();
}

export async function deleteAdditionalInfo(id) {
    const res = await fetch(`http://localhost:8000/api/additional-info/${id}`, { method: "DELETE" });
    if (!res.ok) {
        throw new Error("Chyba pri mazaní additional_info.");
    }
    return res.json();
}


export async function fetchEvents() {
    const res = await fetch("http://localhost:8000/api/events");
    if (!res.ok) {
      throw new Error("Nepodarilo sa načítať udalosti.");
    }
    return res.json();
  }
  
  // CREATE event
  export async function createEvent(formData) {
    const res = await fetch("http://localhost:8000/api/events", {
      method: "POST",
      body: formData
    });
    if (!res.ok) {
      throw new Error("Chyba pri vytváraní podujatia.");
    }
    return res.json();
  }
  
  export async function fetchEvent(id) {
    const res = await fetch(`http://localhost:8000/api/events/${id}`);
    if (!res.ok) {
      throw new Error("Nepodarilo sa načítať podujatie.");
    }
    return res.json();
  }
  
  export async function updateEvent(eventId, formData) {
    formData.append("_method", "PUT");
    const response = await fetch(`http://localhost:8000/api/events/${eventId}`, {
      method: "POST",
      body: formData
    });
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || "Nepodarilo sa upraviť podujatie.");
    }
    return response.json();
  }
  
  
  export async function deleteEvent(id) {
    const res = await fetch(`http://localhost:8000/api/events/${id}`, {
      method: "DELETE"
    });
    if (!res.ok) {
      throw new Error("Chyba pri mazaní podujatia.");
    }
    return res.json();
  }

<script>
  import { onMount } from "svelte";

  export let seasonId;
  export let categoryId;
  export let goTo;
  export let isAuthenticated;
  export let userRole;

  let posts = [];
  let isLoading = true;
  let errorMessage = "";

  async function fetchPosts() {
    try {
      const queryParams = new URLSearchParams();
      if (seasonId) queryParams.append("season_id", seasonId);
      if (categoryId) queryParams.append("category_id", categoryId);

      const url = `http://localhost:8000/api/posts?${queryParams.toString()}`;
      console.log("Načítavam príspevky z:", url);

      const res = await fetch(url);
      if (!res.ok) throw new Error("Nepodarilo sa načítať príspevky");
      posts = await res.json();
      console.log("Načítané príspevky:", posts);
    } catch (err) {
      console.error(err);
      errorMessage = "Nepodarilo sa načítať príspevky.";
    } finally {
      isLoading = false;
    }
  }

  async function deletePost(id) {
    if (!confirm("Naozaj chceš vymazať tento príspevok?")) return;
    await fetch(`http://localhost:8000/api/posts/${id}`, { method: "DELETE" });
    posts = posts.filter((p) => p.id !== id);
  }

  onMount(fetchPosts);
</script>

<style>
  .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 25px;
    font-family: sans-serif;
    background: none; /* Bez pozadia */
  }

  
  .category-card {
    flex: 1 1 300px;
    max-width: 350px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
  }

  .category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
  }

  .category-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
    opacity: 0.85;
    transition: transform 0.4s ease, opacity 0.4s ease, box-shadow 0.4s ease;
  }

  .category-image:hover {
    opacity: 1;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
  }

  h2 {
    font-size: 22px;
    margin: 10px 0;
    color: #333;
  }

  .buttons {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
    gap: 10px;
  }

  
  button {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    background-color: rgb(200, 195, 195);
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
  }

  button::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 50%;
    border-top-left-radius: 30px;
    border-top-right-radius: 30px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.4), rgba(255,255,255,0.05));
    pointer-events: none;
  }

  .message {
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    color: #777;
    margin-top: 50px;
  }
</style>

<div class="container">
  {#if isLoading}
    <p class="message">Načítavam príspevky...</p>
  {:else if errorMessage}
    <p class="message" style="color: red;">{errorMessage}</p>
  {:else if posts.length === 0}
    <p class="message">Žiadne príspevky neboli nájdené.</p>
  {:else}
    {#each posts as post}
      <!-- Každý príspevok vyzerá rovnako ako "kategória" -->
      <div class="category-card" on:click={() => goTo("post-detail", post.id)}>
        {#if post.gallery && post.gallery.images && post.gallery.images.length > 0}
        <img
          class="category-image"
          src={`http://localhost:8000/storage/${post.gallery.images[0].path}`}
          alt={post.name}
        />
      {:else}
        <img
          class="category-image"
          src="placeholder-image.jpg"
          alt="Obrázok nie je dostupný"
        />
      {/if}
        <h2>{post.name}</h2>
        {#if isAuthenticated&& userRole === 'admin'} 
          <div class="buttons">
            <button on:click={() => goTo("edit-post", post.id)} on:click|stopPropagation>📝 Upraviť</button>
            <button on:click={() => deletePost(post.id)}>🗑️ Vymazať</button>
          </div>
        {/if}
      </div>
    {/each}
  {/if}
</div>

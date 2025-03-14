<script>
  import { onMount } from "svelte";
  export let goTo;
  export let isAuthenticated;
  export let seasonId;
  export let userRole;

  let categories = [];
  let isLoading = true;
  let errorMessage = "";

  async function fetchCategories() {
    try {
      console.log("Načítavam kategórie...");
      const res = await fetch("http://localhost:8000/api/categories");
      
      if (!res.ok) throw new Error("Nepodarilo sa načítať kategórie");

      categories = await res.json();
      console.log("Načítané kategórie:", categories);
    } catch (err) {
      console.error(err);
      errorMessage = "Nepodarilo sa načítať kategórie.";
    } finally {
      isLoading = false;
    }
  }

  async function deleteCategory(id) {
    if (!confirm("Naozaj chceš vymazať túto kategóriu?")) return;
    await fetch(`http://localhost:8000/api/categories/${id}`, { method: "DELETE" });
    categories = categories.filter(category => category.id !== id);
  }

  onMount(fetchCategories);
</script>

<style>
  .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 25px;
    font-family: sans-serif;
    background: none;
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

  /* ŠTÝL TLAČIDIEL (rovnaký ako v sezónach) */
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

  .edit {
    background-color: rgb(200, 195, 195);
  }

  .delete {
    background-color: rgb(200, 195, 195);
  }

  button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
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
    <p class="message">Načítavam kategórie...</p>
  {:else if errorMessage}
    <p class="message" style="color: red;">{errorMessage}</p>
  {:else if categories.length === 0}
    <p class="message">Žiadne kategórie neboli nájdené.</p>
  {:else}
    {#each categories as category}
      <div class="category-card" on:click={() => goTo("posts", { seasonId, categoryId: category.id })}>
        {#if category.image && category.image.path}
          <img class="category-image" src={`http://localhost:8000/storage/${category.image.path}`} alt={category.name} />
        {:else}
          <img class="category-image" src="placeholder-image.jpg" alt="Obrázok nie je dostupný" />
        {/if}
        <h2>{category.name}</h2>

        {#if isAuthenticated&& userRole === 'admin'} 
          <div class="buttons">
            <button class="edit" on:click={() => goTo('edit-category', category.id)} on:click|stopPropagation>📝 Upraviť</button>
            <button class="delete" on:click={() => deleteCategory(category.id)} on:click|stopPropagation>🗑️ Vymazať</button>
          </div>
        {/if}
      </div>
    {/each}
  {/if}
</div>

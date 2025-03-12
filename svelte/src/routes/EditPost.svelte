<script>
  import { onMount } from "svelte";

  export let postId;
  export let goTo;

  let name = "";
  let description = "";
  let postal_code = "";
  let town = "";
  let street = "";
  let descriptive_number = "";
  let opening_hours = "";
  let id_season = "";
  let id_category = "";
  let url_address = "";
  let imageFiles = [];
  let existingImages = [];

  let isLoading = true;
  let errorMessage = "";

  let seasons = [];
  let categories = [];

  async function fetchSeasons() {
    try {
      const res = await fetch("http://localhost:8000/api/seasons");
      if (!res.ok) throw new Error("Nepodarilo sa načítať sezóny");
      seasons = await res.json();
    } catch (err) {
      console.error("Chyba pri načítaní sezón:", err);
    }
  }

  async function fetchCategories() {
    try {
      const res = await fetch("http://localhost:8000/api/categories");
      if (!res.ok) throw new Error("Nepodarilo sa načítať kategórie");
      categories = await res.json();
    } catch (err) {
      console.error("Chyba pri načítaní kategórií:", err);
    }
  }

  async function fetchPost() {
    try {
      const res = await fetch(`http://localhost:8000/api/posts/${postId}`);
      if (!res.ok) {
        throw new Error(`Nepodarilo sa načítať príspevok (HTTP ${res.status})`);
      }
      const post = await res.json();

      name = post.name || "";
      description = post.description || "";
      postal_code = post.address?.postal_code || "";
      town = post.address?.town?.name || "";
      street = post.address?.street || "";
      descriptive_number = post.address?.descriptive_number || "";
      opening_hours = post.opening_hours || "";
      id_season = post.id_season || "";
      id_category = post.id_category || "";
      url_address = post.url_address || "";

      if (post.gallery && post.gallery.images) {
        existingImages = post.gallery.images.map(img => img.path);
      }
    } catch (err) {
      errorMessage = err.message;
      console.error("Chyba pri načítaní príspevku:", err);
    } finally {
      isLoading = false;
    }
  }

  function handleFileChange(e) {
    imageFiles = Array.from(e.target.files);
  }

  async function updatePost() {
    const formData = new FormData();

    formData.append("name", name);
    formData.append("description", description);
    formData.append("postal_code", postal_code);
    formData.append("town", town);
    formData.append("street", street);
    formData.append("descriptive_number", descriptive_number);
    formData.append("opening_hours", opening_hours);
    formData.append("id_season", id_season);
    formData.append("id_category", id_category);
    formData.append("url_address", url_address);

    for (let i = 0; i < imageFiles.length; i++) {
      formData.append("images[]", imageFiles[i]);
    }

    try {
      const response = await fetch(`http://localhost:8000/api/posts/${postId}`, {
        method: "POST",
        headers: {
          "X-HTTP-Method-Override": "PUT"
        },
        body: formData
      });

      if (!response.ok) {
        const errorData = await response.json();
        console.error("Chyba pri ukladaní:", errorData);
        alert("Nepodarilo sa upraviť príspevok. Skontrolujte dáta a skúste znova.");
        return;
      }

      alert("Príspevok bol aktualizovaný!");
      goTo("posts");
    } catch (error) {
      console.error(error);
      alert("Nepodarilo sa upraviť príspevok. Skontrolujte dáta a skúste znova.");
    }
  }

  onMount(() => {
    fetchSeasons();
    fetchCategories();
    fetchPost();
  });
</script>

<style>
  .form-container {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    font-family: sans-serif;
    text-align: center;
  }

  .form-group {
    text-align: left;
    margin-bottom: 15px;
  }

  .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #555;
  }

  .form-group input,
  .form-group textarea,
  .form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
  }


  .form-group input:focus,
  .form-group textarea:focus,
  .form-group select:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
  }

  .button-group {
    margin-top: 20px;
    text-align: center;
  }

  .custom-button {
    padding: 12px 24px;
    border: none;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: #2196f3;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .custom-button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
  }
  .existing-images img {
    width: 120px;
    height: 90px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
  }
  .existing-images {
    margin: 10px 0;
    text-align: center;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
  }

</style>

<div class="form-container">
  <h1>Upraviť príspevok</h1>

  {#if errorMessage}
    <p class="error-message">{errorMessage}</p>
  {/if}

  {#if isLoading}
    <p>Načítavam príspevok...</p>
  {:else}
    <form on:submit|preventDefault={updatePost}>
      <div class="form-group">
        <label for="name">Názov príspevku:</label>
        <input id="name" type="text" bind:value={name} required />
      </div>

      <div class="form-group">
        <label for="description">Popis:</label>
        <textarea id="description" rows="3" bind:value={description}></textarea>
      </div>

      <div class="form-group">
        <label for="postal_code">PSČ:</label>
        <input id="postal_code" type="text" bind:value={postal_code} required />
      </div>

      <div class="form-group">
        <label for="town">Mesto:</label>
        <input id="town" type="text" bind:value={town} required />
      </div>

      <div class="form-group">
        <label for="street">Ulica:</label>
        <input id="street" type="text" bind:value={street} required />
      </div>

      <div class="form-group">
        <label for="descriptive_number">Číslo domu:</label>
        <input id="descriptive_number" type="text" bind:value={descriptive_number} required />
      </div>

      <div class="form-group">
        <label for="id_season">Sezóna:</label>
        <select id="id_season" bind:value={id_season} required>
          {#each seasons as season}
            <option value={season.id}>{season.name}</option>
          {/each}
        </select>
      </div>

      <div class="form-group">
        <label for="id_category">Kategória:</label>
        <select id="id_category" bind:value={id_category} required>
          {#each categories as category}
            <option value={category.id}>{category.name}</option>
          {/each}
        </select>
      </div>

      <div class="form-group">
        <label for="url_address">URL adresa (web):</label>
        <input id="url_address" type="text" bind:value={url_address} />
      </div>

      {#if existingImages.length > 0}
        <div class="existing-images">
          {#each existingImages as imgPath}
            <img src={`http://localhost:8000/storage/${imgPath}`} alt="Obrázok" />
          {/each}
        </div>
      {/if}

      <div class="form-group">
        <label for="newImages">Nové obrázky:</label>
        <input id="newImages" type="file" multiple accept="image/*" on:change={handleFileChange} />
      </div>

      <div class="button-group">
        <button type="submit" class="custom-button">Uložiť</button>
        <button type="button" class="custom-button back-btn" on:click={() => goTo("posts")}>Späť</button>
      </div>
    </form>
  {/if}
</div>

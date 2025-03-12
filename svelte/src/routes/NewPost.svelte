<script>
  import { onMount } from "svelte";

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

  function handleFileChange(e) {
    imageFiles = Array.from(e.target.files);
  }

  async function submitPost() {
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
      const response = await fetch("http://localhost:8000/api/posts", {
        method: "POST",
        body: formData
      });

      if (!response.ok) {
        throw new Error("Nepodarilo sa vytvoriť príspevok.");
      }

      alert("Príspevok bol pridaný!");
      goTo("posts");
    } catch (error) {
      console.error("Chyba pri vytváraní príspevku:", error);
      alert("Nepodarilo sa pridať príspevok. Skontrolujte dáta a skúste znova.");
    }
  }

  onMount(() => {
    fetchSeasons();
    fetchCategories();
  });
</script>

<style>
  .form-container {
    max-width: 700px; /* Zvýšená šírka formulára */
    margin: 40px auto;
    background: #fff;
    padding: 30px; /* Väčší padding pre lepší vzhľad */
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    font-family: sans-serif;
    text-align: center;
  }

  .form-container h1 {
    margin-bottom: 20px;
    font-size: 2rem; /* Trochu väčšie pre lepšiu čitateľnosť */
    color: #333;
  }

  .form-group {
    text-align: left;
    margin-bottom: 15px;
    width: 100%; /* Aby sa roztiahli na celú šírku kontajnera */
  }

  .form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #555;
  }

  .form-group input[type="text"],
  .form-group textarea,
  .form-group select {
    width: 100%; /* Všetky polia rovnako široké */
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  .form-group textarea {
    resize: vertical;
    min-height: 100px;
  }

  /* Dropdowny - rovnaký štýl ako inputy */
  .form-group select {
    cursor: pointer;
    appearance: none;
    background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="gray"><path d="M7 10l5 5 5-5z"></path></svg>') no-repeat right 12px center;
    background-size: 16px;
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
</style>

<div class="form-container">
  <h1>Pridať príspevok</h1>
  <form on:submit|preventDefault={submitPost}>
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
      <label for="opening_hours">Otváracie hodiny (ak relevantné):</label>
      <input id="opening_hours" type="text" bind:value={opening_hours} />
    </div>

    <!-- Dropdown pre sezónu -->
    <div class="form-group">
      <label for="id_season">Sezóna:</label>
      <select id="id_season" bind:value={id_season} required>
        <option value="" disabled>Vyber sezónu</option>
        {#each seasons as season}
          <option value={season.id}>{season.name}</option>
        {/each}
      </select>
    </div>

    <!-- Dropdown pre kategóriu -->
    <div class="form-group">
      <label for="id_category">Kategória:</label>
      <select id="id_category" bind:value={id_category} required>
        <option value="" disabled>Vyber kategóriu</option>
        {#each categories as category}
          <option value={category.id}>{category.name}</option>
        {/each}
      </select>
    </div>

    <div class="form-group">
      <label for="url_address">URL adresa (web):</label>
      <input id="url_address" type="text" bind:value={url_address} />
    </div>

    <div class="form-group">
      <label for="images">Obrázky príspevku:</label>
      <input id="images" type="file" multiple accept="image/*" on:change={handleFileChange} />
    </div>

    <div class="button-group">
      <button type="submit" class="custom-button submit-btn">Pridať príspevok</button>
    </div>
  </form>
</div>

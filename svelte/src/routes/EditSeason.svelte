<script>
  import { onMount } from "svelte";

  export let selectedSeasonId;
  export let goTo;

  let name = "";
  let image = null;
  let existingImage = "";

  async function fetchSeason() {
    const res = await fetch(`http://localhost:8000/api/seasons/${selectedSeasonId}`);
    const season = await res.json();
    name = season.name;
    existingImage = season.image.path;
  }

  async function updateSeason() {
    const formData = new FormData();
    formData.append("name", name);
    if (image) {
      formData.append("image", image);
    }

    const response = await fetch(`http://localhost:8000/api/seasons/${selectedSeasonId}`, {
      method: "POST",
      headers: { "X-HTTP-Method-Override": "PUT" },
      body: formData,
    });

    if (!response.ok) {
      const errorData = await response.json();
      console.error("Chyba pri ukladaní:", errorData);
      return;
    }

    goTo("seasons");
  }

  onMount(fetchSeason);
</script>

<style>
  .form-container {
    max-width: 500px;
    margin: 40px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    font-family: sans-serif;
    text-align: center;
  }

  .form-container h1 {
    margin-bottom: 20px;
    font-size: 1.8rem;
    color: #333;
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

  .form-group input[type="text"],
  .form-group input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
  }

  .existing-image {
    margin: 10px 0;
    text-align: center;
  }

  .existing-image img {
    max-width: 100%;
    height: auto;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
  }

  .button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    gap: 10px;
  }

  /* Vlastné tlačidlá podľa pôvodného štýlu */
  .custom-button {
    flex: 1;
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: rgb(200, 195, 195);
    cursor: pointer;
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .custom-button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
  }

  .custom-button::after {
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

  .save-btn {
    background-color: #2196f3;
  }

  .back-btn {
    background-color: #777;
  }
</style>

<div class="form-container">
  <h1>Upraviť sezónu</h1>
  <div class="form-group">
    <label for="seasonName">Názov sezóny:</label>
    <input id="seasonName" type="text" bind:value={name} placeholder="Zadajte názov sezóny" />
  </div>
  {#if existingImage}
    <div class="existing-image">
      <img src={`http://localhost:8000/storage/${existingImage}`} alt="Aktuálny obrázok" />
    </div>
  {/if}
  <div class="form-group">
    <label for="newImage">Nový obrázok:</label>
    <input id="newImage" type="file" on:change={(e) => image = e.target.files[0]} />
  </div>
  <div class="button-group">
    <button class="custom-button save-btn" on:click={updateSeason}>
      Uložiť
    </button>
    <button class="custom-button back-btn" on:click={() => goTo("seasons")}>
      Späť
    </button>
  </div>
</div>

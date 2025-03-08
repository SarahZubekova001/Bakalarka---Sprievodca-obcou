<script>
  import { onMount } from "svelte";
  import { fetchRestaurant, updateRestaurant } from "../lib/api.js";

  export let restaurantId;
  export let goTo;

  let name = "";
  let opening_hours = "";
  let phone_number = "";
  let postal_code = "";
  let town = "";
  let street = "";
  let descriptive_number = "";
  let url_address = "";
  let imageFiles = [];
  let existingImages = [];

  let isLoading = true;
  let errorMessage = "";

  onMount(async () => {
    await fetchRestaurantDetail();
  });

  async function fetchRestaurantDetail() {
    try {
      const res = await fetchRestaurant(restaurantId);
      
      name = res.name || "";
      opening_hours = res.opening_hours || "";
      phone_number = res.phone_number || "";
      postal_code = res.address ? res.address.postal_code || "" : "";
      town = res.address && res.address.town ? res.address.town.name || "" : "";
      street = res.address ? res.address.street || "" : "";
      descriptive_number = res.address ? res.address.descriptive_number || "" : "";
      url_address = res.url_address || "";

      existingImages = res.gallery && res.gallery.images
        ? res.gallery.images.map(img => img.path)
        : [];
    } catch (err) {
      errorMessage = "Chyba pri načítaní reštaurácie.";
      console.error(err);
    } finally {
      isLoading = false;
    }
  }

  async function submitEditRestaurant() {
    const formData = new FormData();
    formData.append("name", name);
    formData.append("opening_hours", opening_hours);
    formData.append("phone_number", phone_number);
    formData.append("postal_code", postal_code);
    formData.append("town", town);
    formData.append("street", street);
    formData.append("descriptive_number", descriptive_number);
    formData.append("url_address", url_address);

    for (let i = 0; i < imageFiles.length; i++) {
      formData.append("images[]", imageFiles[i]);
    }

    try {
      await updateRestaurant(restaurantId, formData);
      alert("Reštaurácia bola úspešne upravená!");
      goTo("restaurants");
    } catch (error) {
      console.error("Chyba pri úprave reštaurácie:", error);
      alert("Nepodarilo sa upraviť reštauráciu.");
    }
  }

  function handleFileChange(e) {
    imageFiles = Array.from(e.target.files);
  }
</script>

{#if isLoading}
  <p>Načítavam údaje reštaurácie...</p>
{:else if errorMessage}
  <p style="color: red;">{errorMessage}</p>
{:else}
  <div class="form-container">
    <h1>Upraviť reštauráciu</h1>
    <form on:submit|preventDefault={submitEditRestaurant}>
      <div class="form-group">
        <label for="name">Názov reštaurácie:</label>
        <input id="name" type="text" bind:value={name} required />
      </div>

      <div class="form-group">
        <label for="opening_hours">Otváracie hodiny:</label>
        <input id="opening_hours" type="text" bind:value={opening_hours} />
      </div>

      <div class="form-group">
        <label for="phone_number">Telefónne číslo:</label>
        <input id="phone_number" type="text" bind:value={phone_number} />
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
        <label for="url_address">URL adresa:</label>
        <input id="url_address" type="text" bind:value={url_address} />
      </div>

      <div class="form-group">
        <label>Existujúce obrázky:</label>
        <div class="image-preview">
          {#each existingImages as image}
            <img
              src={`http://localhost:8000/storage/${image}`}
              alt="Reštaurácia"
              class="preview-img"
            />
          {/each}
        </div>
      </div>

      <div class="form-group">
        <label for="images">Nové obrázky:</label>
        <input id="images" type="file" multiple accept="image/*" on:change={handleFileChange} />
      </div>

      <div class="button-group">
        <button type="submit" class="custom-button submit-btn">
          Uložiť zmeny
        </button>
      </div>
    </form>
  </div>
{/if}

<style>
  .form-container {
    max-width: 600px;
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

  .image-preview {
    display: flex;
    gap: 10px;
    margin-top: 10px;
  }

  .preview-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }

  .button-group {
    margin-top: 20px;
    text-align: center;
  }

  .custom-button {
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    font-size: 1rem;
    font-weight: bold;
    color: #fff;
    background-color: rgb(200, 195, 195);
    cursor: pointer;
  }

  .custom-button:hover {
    transform: scale(1.05);
  }
</style>

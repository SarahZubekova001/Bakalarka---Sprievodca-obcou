<script>
  import { onMount } from "svelte";
  import { fetchAdditionalInfos, updateAdditionalInfo } from "../lib/api.js";
  export let goTo;
  export let additionalInfoId;

  let name = "";
  let text = "";
  let postal_code = "";
  let town = "";
  let street = "";
  let descriptive_number = "";
  let imageFile = null;
  let errorMessage = "";
  let isLoading = true;
  let isSubmitting = false;

  onMount(async () => {
    try {
      const infos = await fetchAdditionalInfos();
      const item = infos.find(i => i.id == additionalInfoId);
      if (!item) throw new Error("Záznam neexistuje.");
      name = item.name;
      text = item.text || "";
      if (item.address) {
        postal_code = item.address.postal_code || "";
        town = item.address.town ? item.address.town.name : "";
        street = item.address.street || "";
        descriptive_number = item.address.descriptive_number || "";
      }
    } catch (err) {
      errorMessage = err.message;
    } finally {
      isLoading = false;
    }
  });

  function handleFileChange(e) {
    imageFile = e.target.files[0];
  }

  async function submitForm() {
    isSubmitting = true;
    try {
      const formData = new FormData();
      formData.append("name", name);
      formData.append("text", text);
      formData.append("postal_code", postal_code);
      formData.append("town", town);
      formData.append("street", street);
      formData.append("descriptive_number", descriptive_number);
      if (imageFile) {
        formData.append("image_file", imageFile);
      }
      await updateAdditionalInfo(additionalInfoId, formData);
      goTo("maininfo");
    } catch (err) {
      errorMessage = err.message;
    } finally {
      isSubmitting = false;
    }
  }
</script>

{#if isLoading}
  <p class="loading">Načítavam údaje...</p>
{:else}
  <div class="form-container">
    <h2>Upraviť záznam</h2>
    {#if errorMessage}
      <p class="error">{errorMessage}</p>
    {/if}
    <form on:submit|preventDefault={submitForm}>
      <label for="name">Názov</label>
      <input id="name" type="text" bind:value={name} required />

      <label for="text">Popis</label>
      <textarea id="text" bind:value={text} rows="4"></textarea>

      <label for="postal_code">PSČ</label>
      <input id="postal_code" type="text" bind:value={postal_code} required />

      <label for="town">Mesto</label>
      <input id="town" type="text" bind:value={town} required />

      <label for="street">Ulica</label>
      <input id="street" type="text" bind:value={street} required />

      <label for="descriptive_number">Číslo domu</label>
      <input id="descriptive_number" type="text" bind:value={descriptive_number} required />

      <label for="image_file">Obrázok (voliteľné)</label>
      <input id="image_file" type="file" accept="image/*" on:change={handleFileChange} />

      <button type="submit" disabled={isSubmitting}>
        {isSubmitting ? "Ukladám..." : "Uložiť"}
      </button>
    </form>
  </div>
{/if}

<style>
  .loading {
    text-align: center;
    margin: 2rem auto;
    font-family: sans-serif;
  }
  .form-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 1rem;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    font-family: sans-serif;
  }
  h2 {
    margin-bottom: 1rem;
    text-align: center;
  }
  .error {
    color: red;
    margin-bottom: 1rem;
    text-align: center;
  }
  form {
    display: flex;
    flex-direction: column;
  }
  label {
    margin: 0.5rem 0 0.2rem;
    font-weight: bold;
  }
  input, textarea {
    padding: 0.5rem;
    margin-bottom: 1rem;
    border-radius: 4px;
    border: 1px solid #ccc;
    width: 100%;
  }
  button {
    align-self: flex-end;
    padding: 0.7rem 1.5rem;
    border: none;
    border-radius: 4px;
    background: #28a745;
    color: #fff;
    cursor: pointer;
  }
  button:hover {
    background: #218838;
  }
  @media (max-width: 600px) {
    .form-container {
      margin: 1rem;
    }
    button {
      width: 100%;
    }
  }
</style>

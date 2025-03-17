<script>
  import { onMount } from "svelte";
  import { updateEvent } from "../lib/api.js";

  export let eventId;
  export let goTo;

  let name = "";
  let date = "";
  let time = "";
  let text = "";
  let postal_code = "";
  let town = "";
  let street = "";
  let descriptive_number = "";
  let imageFile = null; 

  let existingImage = null; 

  let errorMessage = "";
  let isLoading = true;
  let isSubmitting = false;

  onMount(fetchEvent);

  async function fetchEvent() {
    try {
      const res = await fetch(`http://localhost:8000/api/events/${eventId}`);
      if (!res.ok) throw new Error("Nepodarilo sa načítať podujatie.");
      const data = await res.json();

      name = data.name || "";
      date = data.date || "";
      time = data.time || "";
      text = data.text || "";

      if (data.address) {
        postal_code = data.address.postal_code || "";
        street = data.address.street || "";
        descriptive_number = data.address.descriptive_number || "";
        if (data.address.town) {
          town = data.address.town.name || "";
        }
      }

      if (data.image) {
        existingImage = { id: data.image.id, path: data.image.path };
      }

    } catch (err) {
      errorMessage = err.message;
    } finally {
      isLoading = false;
    }
  }

  function handleFileChange(e) {
    imageFile = e.target.files[0];
  }

  async function submitForm() {
    isSubmitting = true;
    errorMessage = "";
    try {
      const formData = new FormData();
      formData.append("name", name);
      formData.append("date", date);
      formData.append("time", time);
      formData.append("text", text);
      formData.append("postal_code", postal_code);
      formData.append("town", town);
      formData.append("street", street);
      formData.append("descriptive_number", descriptive_number);

      if (imageFile) {
        formData.append("image_file", imageFile);
      }

      await updateEvent(eventId, formData);
      goTo("events");
    } catch (err) {
      errorMessage = err.message;
    } finally {
      isSubmitting = false;
    }
  }

  async function deleteExistingImage(imageId) {
  if (!confirm("Naozaj chcete vymazať tento obrázok?")) return;
  try {
    const response = await fetch(`http://localhost:8000/api/images/${imageId}`, {
      method: "DELETE"
    });
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || "Chyba pri mazaní obrázka.");
    }
    existingImage = null;
  } catch (err) {
    alert(err.message);
  }
}

</script>

{#if isLoading}
  <p>Načítavam údaje...</p>
{:else}
  <div class="form-container">
    <h2>Upraviť podujatie</h2>
    {#if errorMessage}
      <p class="error">{errorMessage}</p>
    {/if}

    <form on:submit|preventDefault={submitForm} enctype="multipart/form-data">
      <label for="name">Názov podujatia:</label>
      <input id="name" type="text" bind:value={name} required />

      <label for="date">Dátum:</label>
      <input id="date" type="date" bind:value={date} required />

      <label for="time">Čas:</label>
      <input id="time" type="time" bind:value={time} />

      <label for="text">Popis:</label>
      <textarea id="text" rows="3" bind:value={text}></textarea>

      <label for="postal_code">PSČ:</label>
      <input id="postal_code" type="text" bind:value={postal_code} />

      <label for="town">Mesto:</label>
      <input id="town" type="text" bind:value={town} />

      <label for="street">Ulica:</label>
      <input id="street" type="text" bind:value={street} />

      <label for="descriptive_number">Popisné číslo:</label>
      <input id="descriptive_number" type="text" bind:value={descriptive_number} />

      {#if existingImage}
        <div class="existing-image-wrapper">
          <img
            class="existing-image"
            src={`http://localhost:8000/storage/${existingImage.path}`}
            alt="Podujatie obrázok"
          />
          <button
            type="button"
            class="delete-image-btn"
            on:click={() => deleteExistingImage(existingImage.id)}
          >
            ✕
          </button>
        </div>
      {/if}

      <label for="image_file">Nový obrázok (voliteľné):</label>
      <input id="image_file" type="file" accept="image/*" on:change={handleFileChange} />

      <button type="submit" disabled={isSubmitting}>
        {isSubmitting ? "Ukladám..." : "Uložiť"}
      </button>
    </form>
  </div>
{/if}

<style>
  .form-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 1rem;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
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
    font: inherit;
    width: 100%;
  }

  /* existing image preview + delete button */
  .existing-image-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 1rem;
  }
  .existing-image {
    width: 120px;
    height: 90px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
  }
  .delete-image-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #dc3545;
    border: none;
    color: #fff;
    border-radius: 4px;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 1rem;
    line-height: 1rem;
    text-align: center;
    padding: 0;
  }
  .delete-image-btn:hover {
    background: #c82333;
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

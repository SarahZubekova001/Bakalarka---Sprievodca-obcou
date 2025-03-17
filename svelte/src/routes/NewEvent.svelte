<script>
  import { createEvent } from "../lib/api.js"; // your API call
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

  let errorMessage = "";
  let isSubmitting = false;

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

      const resp = await createEvent(formData);
      goTo("events"); 
    } catch (err) {
      errorMessage = err.message;
    } finally {
      isSubmitting = false;
    }
  }
</script>

<div class="form-container">
  <h2>Pridať nové podujatie</h2>
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

    <label for="image_file">Obrázok (voliteľné):</label>
    <input id="image_file" type="file" accept="image/*" on:change={handleFileChange} />

    <button type="submit" disabled={isSubmitting}>
      {isSubmitting ? "Ukladám..." : "Uložiť"}
    </button>
  </form>
</div>

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
</style>

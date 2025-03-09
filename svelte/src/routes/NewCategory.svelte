<script>
  import { createCategory } from '../lib/api.js';
  
  export let goTo;

  let name = "";
  let image = null;
  let errorMessage = "";
  let isLoading = false;

  async function handleSubmit() {
    if (!name || !image) {
      errorMessage = "Meno a obrázok sú povinné!";
      return;
    }

    isLoading = true;
    errorMessage = "";

    const formData = new FormData();
    formData.append("name", name);
    formData.append("image", image);

    try {
      await createCategory(formData);
      goTo("categories");
    } catch (error) {
      errorMessage = "Nepodarilo sa vytvoriť kategóriu.";
      console.error(error);
    } finally {
      isLoading = false;
    }
  }
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

  h1 {
    margin-bottom: 20px;
    font-size: 1.8rem;
    color: #333;
  }

  .form-group {
    text-align: left;
    margin-bottom: 15px;
  }

  label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #555;
  }

  input[type="text"], input[type="file"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
  }

  .button-group {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    gap: 10px;
  }

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
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .custom-button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
  }

  .save-btn {
    background-color: #2196f3;
  }

  .back-btn {
    background-color: #777;
  }

  .error {
    color: red;
    margin-top: 10px;
  }
</style>

<div class="form-container">
  <h1>Pridať kategóriu</h1>

  {#if errorMessage}
    <p class="error">{errorMessage}</p>
  {/if} 
  

  <div class="form-group">
    <label for="categoryName">Názov kategórie:</label>
    <input id="categoryName" type="text" bind:value={name} placeholder="Zadajte názov kategórie" />
  </div>

  <div class="form-group">
    <label for="categoryImage">Obrázok kategórie:</label>
    <input id="categoryImage" type="file" on:change={(e) => image = e.target.files[0]} />
  </div>

  <div class="button-group">
    <button class="custom-button save-btn" on:click={handleSubmit} disabled={isLoading}>
      {isLoading ? "Ukladám..." : "Uložiť"}
    </button>
    <button class="custom-button back-btn" on:click={() => goTo("categories")}>
      Späť
    </button>
  </div>
</div>

<script>
  import { createSeason } from '../lib/api.js';

  export let goTo;

  let name = "";
  let imageFile = null;

  async function submitSeason() {
    const formData = new FormData();
    formData.append('name', name);
    if (imageFile) {
      formData.append('image', imageFile);
    }

    try {
      await createSeason(formData);
      alert("Sezóna bola pridaná!");
      goTo('seasons');
    } catch (error) {
      console.error("Chyba pri vytváraní sezóny:", error);
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

  .button-group {
    margin-top: 20px;
    text-align: center;
  }

  /* Vlastné tlačidlo podľa pôvodného štýlu */
  .custom-button {
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
    background: linear-gradient(
      to bottom,
      rgba(255,255,255,0.4),
      rgba(255,255,255,0.05)
    );
    pointer-events: none;
  }

  .submit-btn {
    
  }
</style>

<div class="form-container">
  <h1>Pridať novú sezónu</h1>
  <form on:submit|preventDefault={submitSeason}>
    <div class="form-group">
      <label for="seasonName">Názov sezóny:</label>
      <input
        id="seasonName"
        type="text"
        bind:value={name}
        placeholder="Zadajte názov sezóny"
        required
      />
    </div>
    <div class="form-group">
      <label for="seasonImage">Obrázok sezóny:</label>
      <input
        id="seasonImage"
        type="file"
        on:change={(e) => imageFile = e.target.files[0]}
      />
    </div>
    <div class="button-group">
      <button type="submit" class="custom-button submit-btn">
        Vytvoriť sezónu
      </button>
    </div>
  </form>
</div>

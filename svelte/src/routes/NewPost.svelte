<script>
  export let goTo;

  // Polia z tabuľky "post"
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

  // Keď užívateľ nahrá súbory (obrázky)
  function handleFileChange(e) {
    imageFiles = Array.from(e.target.files);
  }

  // Funkcia na odoslanie formulára
  async function submitPost() {
    const formData = new FormData();

    // Základné textové polia
    formData.append("name", name);
    formData.append("description", description);
    formData.append("postal_code", postal_code);
    formData.append("town", town);
    formData.append("street", street);
    formData.append("descriptive_number", descriptive_number);
    formData.append("opening_hours", opening_hours);
    formData.append("id_season", id_season);   // Môžeš zmeniť na select, ak chceš
    formData.append("id_category", id_category); // Tiež prípadne select
    formData.append("url_address", url_address);

    // Obrázky
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
</script>

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
  .form-group textarea,
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
      <textarea
        id="description"
        rows="3"
        bind:value={description}
        placeholder="Krátky popis..."
      ></textarea>
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

    <div class="form-group">
      <label for="id_season">Sezóna (ID):</label>
      <input
        id="id_season"
        type="text"
        bind:value={id_season}
        placeholder="Napr. 1, 2, ..."
      />
    </div>

    <div class="form-group">
      <label for="id_category">Kategória (ID):</label>
      <input
        id="id_category"
        type="text"
        bind:value={id_category}
        placeholder="Napr. 3, 4, ..."
        required
      />
    </div>

    <div class="form-group">
      <label for="url_address">URL adresa (web):</label>
      <input id="url_address" type="text" bind:value={url_address} />
    </div>

    <div class="form-group">
      <label for="images">Obrázky príspevku:</label>
      <input
        id="images"
        type="file"
        multiple
        accept="image/*"
        on:change={handleFileChange}
      />
    </div>

    <div class="button-group">
      <button type="submit" class="custom-button submit-btn">
        Pridať príspevok
      </button>
    </div>
  </form>
</div>

<script>
  import { onMount } from "svelte";
  import { 
    fetchAdditionalInfos,
    deleteAdditionalInfo
  } from '../lib/api.js';

  export let goTo;
  export let userRole;

  let mainInfo = null;
  let errorMessage = "";
  let isLoading = true;
  let currentIndex = 0; 
  let additionalInfos = [];

  onMount(async () => {
    try {
      const [mainRes, addRes] = await Promise.all([
        fetch("http://localhost:8000/api/maininfo"),
        fetchAdditionalInfos()
      ]);

      if (!mainRes.ok) throw new Error("Nepodarilo sa načítať main_info");
      mainInfo = await mainRes.json();

      additionalInfos = addRes; 
    } catch (err) {
      errorMessage = err.message;
      console.error(err);
    } finally {
      isLoading = false;
    }
  });

  function prevSlide() {
    if (!mainInfo?.gallery?.images?.length) return;
    currentIndex = (currentIndex - 1 + mainInfo.gallery.images.length) % mainInfo.gallery.images.length;
  }
  function nextSlide() {
    if (!mainInfo?.gallery?.images?.length) return;
    currentIndex = (currentIndex + 1) % mainInfo.gallery.images.length;
  }

  function createAdditionalInfo() {
    goTo("add-additional-info");
  }
  function editAdditionalInfo(id) {
    goTo("edit-additional-info", id);
  }
  async function removeAdditionalInfo(id) {
    if (!confirm("Naozaj chcete vymazať tento záznam?")) return;
    try {
      await deleteAdditionalInfo(id);
      additionalInfos = additionalInfos.filter(item => item.id !== id);
    } catch (err) {
      console.error(err);
      alert("Nepodarilo sa vymazať záznam.");
    }
  }
</script>

{#if isLoading}
  <p>Načítavam údaje...</p>
{:else if errorMessage}
  <p style="color:red">{errorMessage}</p>
{:else}
  <div class="slider-container">
    {#if mainInfo.gallery && mainInfo.gallery.images && mainInfo.gallery.images.length > 0}
      <div 
        class="slider"
        style="transform: translateX({-currentIndex * 100}vw);"
      >
        {#each mainInfo.gallery.images as image}
          <img
            class="slide"
            src={`http://localhost:8000/storage/${image.path}`}
            alt="Obrázok galérie"
          />
        {/each}
      </div>

      {#if mainInfo.gallery.images.length > 1}
        <button class="prev" on:click={prevSlide}>❮</button>
        <button class="next" on:click={nextSlide}>❯</button>
      {/if}
    {:else}
      <p class="empty-gallery">Galéria zatiaľ nenastavená</p>
    {/if}
  </div>

  <div class="info-section">
    <h2>{mainInfo.town_name}</h2>
    <p>{mainInfo.description}</p>

    {#if mainInfo.logo_image}
      <img
        src={`http://localhost:8000/storage/${mainInfo.logo_image.path}`}
        alt="Logo"
        class="logo"
      />
    {:else}
      <p>Logo zatiaľ nenastavené</p>
    {/if}

    {#if userRole === "admin"}
      {#if mainInfo.id}
        <button on:click={() => goTo("edit-maininfo", mainInfo.id)}>Upraviť</button>
      {:else}
        <button on:click={() => goTo("add-maininfo")}>Pridať</button>
      {/if}
    {/if}
  </div>

  <div class="additional-info-section">
  <h3>Ďalšie body v obci</h3>

  {#if additionalInfos.length === 0}
    <p class="no-info">Žiadne ďalšie informácie.</p>
  {:else}
    <div class="card-grid">
      {#each additionalInfos as item}
        <div class="card">
          {#if item.image}
            <img class="card-img" src={`http://localhost:8000/storage/${item.image.path}`} alt="Obrázok" />
          {:else}
            <img class="card-img" src="placeholder.jpg" alt="Obrázok" />
          {/if}
          <div class="card-content">
            <h4>{item.name}</h4>
            <p>{item.text}</p>
            {#if item.address}
              <p class="card-address">
                {item.address.street} {item.address.descriptive_number}, {item.address.postal_code} {item.address.town_name}
              </p>
            {/if}
            {#if userRole === "admin"}
              <div class="actions">
                <button on:click={() => editAdditionalInfo(item.id)}>Upraviť</button>
                <button on:click={() => removeAdditionalInfo(item.id)}>Vymazať</button>
              </div>
            {/if}
          </div>
        </div>
      {/each}
    </div>
  {/if}

  {#if userRole === "admin"}
    <button class="add-info-btn" on:click={createAdditionalInfo}>
      Pridať nový záznam
    </button>
  {/if}
</div>

{/if}

<style>
  .slider-container {
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    width: 100vw;
    height: 70vh; 
    overflow: hidden;
    background: #f9f9f9;
  }
  .slider {
    display: flex;
    height: 100%;
    transition: transform 0.5s ease-in-out;
  }
  .slide {
    min-width: 100vw;
    height: 100%;
    object-fit: cover;
  }
  .prev, .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.5);
    color: #fff;
    border: none;
    font-size: 2rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    z-index: 10;
  }
  .prev {
    left: 10px;
  }
  .next {
    right: 10px;
  }
  .empty-gallery {
    text-align: center;
    line-height: 70vh;
  }

  .info-section {
    max-width: 800px;
    margin: 2rem auto;
    text-align: center;
  }
  .info-section h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
  }
  .info-section p {
    margin-bottom: 1rem;
  }
  .logo {
    max-width: 200px;
    margin: 1rem auto;
    display: block;
  }
  button {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    background-color: #28a745;
    color: #fff;
    cursor: pointer;
    margin-top: 1rem;
  }
  button:hover {
    background-color: #218838;
  }

  .additional-info-section {
    padding: 2.5rem;
    color: #fff;
    text-align: center;
  }
  .additional-info-section h3 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
  }
  .no-info {
    font-size: 1rem;
    color: #ccc;
  }

  /* Grid pre karty – min. šírka 300px, aby sa typicky zmestili 3 vedľa seba na väčších displejoch */
  .card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }

  /* Samotná karta */
  .card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: left;
    color: #333;
    display: flex;
    flex-direction: column;
  }

  /* Obrázok menší, aby boli karty kompaktnejšie */
  .card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  /* Obsah karty */
  .card-content {
    padding: 1rem;
    flex: 1;
  }

  .card-content h4 {
    margin: 0 0 0.8rem;
    font-size: 1.4rem;
    color: #222;
  }

  .card-content p {
    margin: 0 0 1rem;
    font-size: 1rem;
    line-height: 1.4;
  }
  .card-address {
  font-size: 0.95rem;
  color: #666;
  margin-top: 0.5rem;
}


  .actions {
    display: flex;
    gap: 0.5rem;
  }

  .actions button {
    padding: 8px 14px;
    border: none;
    border-radius: 4px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background 0.3s;
  }
  .actions button:first-of-type {
    background: #007bff;
    color: #fff;
  }
  .actions button:first-of-type:hover {
    background: #0069d9;
  }
  .actions button:last-of-type {
    background: #dc3545;
    color: #fff;
  }
  .actions button:last-of-type:hover {
    background: #c82333;
  }

  .add-info-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    background: #555;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
  }
  .add-info-btn:hover {
    background: #444;
  }

  /* Responzívne úpravy */
  @media (max-width: 600px) {
    .additional-info-section {
      padding: 1.5rem;
    }
    .card-img {
      height: 160px;
    }
    .card-content h4 {
      font-size: 1.2rem;
      margin-bottom: 0.6rem;
    }
    .card-content p {
      font-size: 0.95rem;
      margin-bottom: 0.8rem;
    }
    .add-info-btn {
      width: 100%;
      margin-top: 1rem;
    }
  }
</style>

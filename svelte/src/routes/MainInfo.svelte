<script>
  import { onMount } from "svelte";

  export let goTo;
  export let userRole;

  let mainInfo = null;
  let errorMessage = "";
  let isLoading = true;
  let currentIndex = 0; // index pre slider

  onMount(async () => {
    try {
      const res = await fetch("http://localhost:8000/api/maininfo");
      if (!res.ok) throw new Error("Nepodarilo sa načítať údaje.");
      mainInfo = await res.json();
    } catch (err) {
      errorMessage = err.message;
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
{/if}

<style>
  .slider-container {
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    width: 100vw;
    height: 70vh; /* Môžeš zmeniť podľa potreby */
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
    line-height: 70vh; /* aby to bolo vertikálne vycentrované */
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
</style>

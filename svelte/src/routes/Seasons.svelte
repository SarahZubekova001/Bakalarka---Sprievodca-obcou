<script>
  import { onMount } from "svelte";

  export let goTo;
  export let isAuthenticated; 
  export let userRole;

  let seasons = [];
  let mainInfo = null;
  let isLoadingSeasons = true;
  let isLoadingMainInfo = true;
  let errorMessage = "";
  let currentIndex = 0; // index pre slider

  async function fetchSeasons() {
    try {
      const res = await fetch("http://localhost:8000/api/seasons");
      if (!res.ok) throw new Error("Nepodarilo sa načítať sezóny");
      seasons = await res.json();
    } catch (err) {
      console.error(err);
    } finally {
      isLoadingSeasons = false;
    }
  }

  async function fetchMainInfo() {
    try {
      const res = await fetch("http://localhost:8000/api/maininfo");
      if (!res.ok) throw new Error("Nepodarilo sa načítať main_info");
      mainInfo = await res.json();
      console.log("mainInfo data:", mainInfo);
    } catch (err) {
      console.error(err);
      errorMessage = err.message;
    } finally {
      isLoadingMainInfo = false;
    }
  }

  onMount(async () => {
    await Promise.all([fetchSeasons(), fetchMainInfo()]);
  });

  function prevSlide() {
    if (!mainInfo?.gallery?.images?.length) return;
    currentIndex = (currentIndex - 1 + mainInfo.gallery.images.length) % mainInfo.gallery.images.length;
  }

  function nextSlide() {
    if (!mainInfo?.gallery?.images?.length) return;
    currentIndex = (currentIndex + 1) % mainInfo.gallery.images.length;
  }

  function goToMainInfo() {
    goTo("maininfo");
  }
</script>

{#if isLoadingSeasons || isLoadingMainInfo}
  <p>Načítavam údaje...</p>
{:else if errorMessage}
  <p style="color:red">{errorMessage}</p>
{:else}
  <!-- SLIDER -->
  <div class="slider-container">
    {#if mainInfo && mainInfo.gallery && mainInfo.gallery.images && mainInfo.gallery.images.length > 0}
      <div 
        class="slider"
        style="transform: translateX({-currentIndex * 100}vw);"
      >
        {#each mainInfo.gallery.images as image}
          <div class="slide">
            <img
              src={`http://localhost:8000/storage/${image.path}`}
              alt="Obrázok galérie"
            />
          </div>
        {/each}
      </div>
      {#if mainInfo.gallery.images.length > 1}
        <button class="prev" on:click={prevSlide}>❮</button>
        <button class="next" on:click={nextSlide}>❯</button>
      {/if}
    {:else}
      <div class="slider-fallback">
        <p>Galéria zatiaľ nenastavená</p>
      </div>
    {/if}
    <div class="slider-text">
      <h1>{mainInfo ? mainInfo.town_name : "Názov obce"}</h1>
      <p class="more-info" on:click={goToMainInfo}>Zisti viac o obci</p>
    </div>
  </div>

  <div class="seasons-container">
    {#each seasons as season}
      <div class="season-card" on:click={() => goTo('categories', { seasonId: season.id })}>
        {#if season.image?.path}
          <img src={`http://localhost:8000/storage/${season.image.path}`} alt={season.name} />
        {:else}
          <img src="placeholder.jpg" alt="N/A" />
        {/if}
        <h2>{season.name}</h2>

        {#if isAuthenticated && userRole === 'admin'}
          <div class="buttons">
            <button class="edit" on:click={() => goTo('edit-season', season.id)} on:click|stopPropagation>
              📝 Upraviť
            </button>
            <button class="delete" on:click={() => deleteSeason(season.id)} on:click|stopPropagation>
              🗑️ Vymazať
            </button>
          </div>
        {/if}
      </div>
    {/each}
  </div>
{/if}

<style>
  /* SLIDER CONTAINER: roztiahnuté cez celú šírku */
  .slider-container {
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    width: 100vw;
    height: 40vh; /* Nižšia výška pre menej scrollovania */
    overflow: hidden;
    background: #000;
  }

  /* Slider wrapper */
  .slider {
    display: flex;
    height: 100%;
    transition: transform 0.5s ease-in-out;
  }

  /* Každý slide – 100% viewport width */
  .slide {
    min-width: 100vw;
    height: 100%;
  }
  .slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: brightness(0.6); /* Tmavší efekt */
  }

  .slider-fallback {
    width: 100%;
    height: 100%;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Šípky */
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

  /* Slider text – umiestnený v ľavom hornom rohu */
  .slider-text {
    position: absolute;
    top: 20px;
    left: 20px;
    z-index: 11;
    color: #fff;
    text-shadow: 0 2px 5px rgba(0,0,0,0.5);
  }
  .slider-text h1 {
    margin: 0;
    font-size: 2rem;
    font-family: Georgia, serif;
  }
  .more-info {
    margin-top: 0.5rem;
    font-size: 1.2rem;
    font-family: Georgia, serif;
    text-decoration: underline;
    cursor: pointer;
  }
  .more-info:hover {
    opacity: 0.8;
  }

  /* GRID SEZÓN */
  .seasons-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 25px;
    background: none;
    margin-top: 40px;
  }
  .season-card {
    flex: 1 1 300px;
    max-width: 350px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
  }
  .season-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.15);
  }
  .season-card img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
    opacity: 0.85;
    transition: transform 0.4s ease, opacity 0.4s ease, box-shadow 0.4s ease;
  }
  .season-card img:hover {
    opacity: 1;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
  }
  .season-card h2 {
    font-size: 22px;
    margin: 10px 0;
    color: #333;
  }
  .buttons {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
    gap: 10px;
  }
  button.edit, button.delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    color: #fff;
    background-color: rgb(200,195,195);
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    transition: transform 0.3s, box-shadow 0.3s;
  }
  button.edit:hover, button.delete:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
  }
  @media (max-width: 600px) {
    .slider-container {
      height: 30vh;
    }
    .slider-text h1 {
      font-size: 1.5rem;
    }
    .more-info {
      font-size: 1rem;
    }
    .seasons-container {
      flex-direction: column;
      align-items: center;
    }
    .season-card {
      max-width: 90%;
    }
  }
</style>

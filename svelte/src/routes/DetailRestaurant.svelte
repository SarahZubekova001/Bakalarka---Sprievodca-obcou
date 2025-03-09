<script>
  import { onMount } from "svelte";
  export let restaurantId;

  let restaurant = null;
  let errorMessage = "";
  let isLoading = false;
  let lat = null;
  let lng = null;
  let mapUrl = "";

  let currentIndex = 0;
  let interval;

  onMount(async () => {
    await fetchRestaurantDetail();
    startAutoSlide();
  });

  async function fetchRestaurantDetail() {
    isLoading = true;
    try {
      const res = await fetch(`http://localhost:8000/api/restaurants/${restaurantId}`);
      if (!res.ok) {
        throw new Error(`Nepodarilo sa načítať reštauráciu (HTTP ${res.status}).`);
      }
      restaurant = await res.json();
      await fetchCoordinates();
    } catch (err) {
      errorMessage = err.message;
      console.error("Chyba pri načítaní:", err);
    } finally {
      isLoading = false;
    }
  }

  function getFullAddress() {
    if (!restaurant || !restaurant.address) return "";
    const addr = restaurant.address;
    let cityName = addr.town ? addr.town.name : "";
    
    return `${addr.street} ${addr.descriptive_number}, ${addr.postal_code} ${cityName}, Slovakia`;
  }

  async function fetchCoordinates() {
    const address = getFullAddress();
    const apiKey = "AIzaSyB9QQLMhRMvk5sEoEuX6kSs-UvobBRo6zE"; 
    const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(address)}&key=${apiKey}`;

    try {
      const res = await fetch(url);
      const data = await res.json();

      if (data.status === "OK") {
        lat = data.results[0].geometry.location.lat;
        lng = data.results[0].geometry.location.lng;
        mapUrl = `https://www.google.com/maps/embed/v1/place?key=${apiKey}&q=${lat},${lng}`;
      } else {
        console.error("Chyba pri získavaní súradníc:", data);
      }
    } catch (error) {
      console.error("Chyba pri načítaní geokódovania:", error);
    }
  }

  function prevSlide() {
    currentIndex = (currentIndex - 1 + restaurant.gallery.images.length) % restaurant.gallery.images.length;
  }

  function nextSlide() {
    currentIndex = (currentIndex + 1) % restaurant.gallery.images.length;
  }

  function startAutoSlide() {
    if (restaurant && restaurant.gallery && restaurant.gallery.images.length > 1) {
      interval = setInterval(() => {
        nextSlide();
      }, 4000);
    }
  }
</script>

{#if isLoading}
  <p>Načítavam reštauráciu...</p>
{:else if errorMessage}
  <p style="color: red;">{errorMessage}</p>
{:else if !restaurant}
  <p>Reštaurácia sa nenašla.</p>
{:else}
  <div class="detail-container">
    <!-- Luxusný slider -->
    {#if restaurant.gallery && restaurant.gallery.images && restaurant.gallery.images.length > 0}
      <div class="slider-container">
        <div class="slider">
          {#each restaurant.gallery.images as image, i}
            <img
              class="slide"
              src={`http://localhost:8000/storage/${image.path}`}
              alt={restaurant.name}
              style="transform: translateX({-100 * currentIndex}%);"
            />
          {/each}
        </div>
        <button class="prev" on:click={prevSlide}>❮</button>
        <button class="next" on:click={nextSlide}>❯</button>
      </div>
    {:else}
      <img class="main-image" src="placeholder-image.jpg" alt="Obrázok nie je dostupný" />
    {/if}

    <h1>{restaurant.name}</h1>
    {#if restaurant.phone_number}
      <p><strong>Telefón:</strong> {restaurant.phone_number}</p>
    {/if}
    {#if restaurant.opening_hours}
      <p><strong>Otváracie hodiny:</strong> {restaurant.opening_hours}</p>
    {/if}
    {#if restaurant.url_address}
      <p>
        <strong>Web:</strong>
        <a href={restaurant.url_address} target="_blank" rel="noopener noreferrer">
          {restaurant.url_address}
        </a>
      </p>
    {/if}

    <p><strong>Adresa:</strong> {getFullAddress()}</p>

    <h2>Mapa</h2>
    {#if lat && lng}
      <iframe width="600" height="450" style="border:0" loading="lazy" allowfullscreen src={mapUrl}></iframe>
    {:else}
      <p>Načítavam mapu...</p>
    {/if}
  </div>
{/if}

<style>
  .detail-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
    font-family: sans-serif;
  }

  .slider-container {
    position: relative;
    width: 100%;
    max-width: 600px;
    margin: auto;
    overflow: hidden;
    border-radius: 12px;
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
  }

  .slider {
    display: flex;
    transition: transform 0.5s ease-in-out;
    width: 100%;
  }

  .slide {
    width: 100%;
    height: 350px;
    object-fit: cover;
    flex-shrink: 0;
  }

  .prev, .next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    font-size: 20px;
    border-radius: 50%;
    transition: background 0.3s;
  }

  .prev:hover, .next:hover {
    background: rgba(0, 0, 0, 0.8);
  }

  .prev {
    left: 10px;
  }

  .next {
    right: 10px;
  }

  h1 {
    font-size: 26px;
    margin-top: 1rem;
    color: #333;
  }

  p {
    font-size: 18px;
    margin: 5px 0;
    color: #555;
  }

  iframe {
    width: 100%;
    max-width: 600px;
    border-radius: 6px;
  }
</style>

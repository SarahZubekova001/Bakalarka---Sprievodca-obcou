<script>
  import { onMount } from "svelte";

  export let restaurantId;

  let restaurant = null;
  let errorMessage = "";
  let isLoading = false;
  let lat = null;
  let lng = null;
  let mapUrl = "";

  onMount(async () => {
    await fetchRestaurantDetail();
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
</script>

{#if isLoading}
  <p>Načítavam reštauráciu...</p>
{:else if errorMessage}
  <p style="color: red;">{errorMessage}</p>
{:else if !restaurant}
  <p>Reštaurácia sa nenašla.</p>
{:else}
  <div class="detail-container">
    {#if restaurant.gallery && restaurant.gallery.images && restaurant.gallery.images.length > 0}
      <img class="main-image"
        src={`http://localhost:8000/storage/${restaurant.gallery.images[0].path}`}
        alt={restaurant.name}
      />
    {:else}
      <img class="main-image"
        src="placeholder-image.jpg"
        alt="Obrázok nie je dostupný"
      />
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

    <h2>Galéria</h2>
    <div class="gallery">
      {#if restaurant.gallery && restaurant.gallery.images && restaurant.gallery.images.length > 0}
        {#each restaurant.gallery.images as image, i}
          {#if i > 0}
            <img class="gallery-img"
              src={`http://localhost:8000/storage/${image.path}`}
              alt="Obrázok z galérie"
            />
          {/if}
        {/each}
      {:else}
        <p>Galéria nemá žiadne obrázky.</p>
      {/if}
    </div>

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

  .main-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
  }

  .gallery-img {
    width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }

  iframe {
    width: 100%;
    max-width: 600px;
    border-radius: 6px;
  }
</style>

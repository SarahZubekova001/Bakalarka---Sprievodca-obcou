<script>
  import { onMount } from "svelte";
  export let goTo;
  export let isAuthenticated;
  export let userRole;
  
  let restaurants = [];

  async function fetchRestaurants() {
    try {
      const res = await fetch("http://localhost:8000/api/restaurants");
      if (!res.ok) throw new Error("Nepodarilo sa načítať reštaurácie");
      restaurants = await res.json();
    } catch (err) {
      console.error(err);
    }
  }

  onMount(fetchRestaurants);

  async function deleteRestaurant(id) {
    if (!confirm("Naozaj chceš vymazať túto reštauráciu?")) return;
    await fetch(`http://localhost:8000/api/restaurants/${id}`, { method: "DELETE" });
    restaurants = restaurants.filter((r) => r.id !== id);
  }
</script>

<style>
  .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 25px;
    font-family: sans-serif;
    background: none; /* Odstránené sivé pozadie */
  }

  .restaurant-card {
    flex: 1 1 300px;
    max-width: 350px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer; /* Pridaný pointer pre lepšiu UX */
  }

  .restaurant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
  }

  .restaurant-image {
    width: 100%;
    height: 250px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
    opacity: 0.85; /* Štandardne mierne priehľadné */
    transition: transform 0.4s ease, opacity 0.4s ease, box-shadow 0.4s ease;
  }

  .restaurant-image:hover {
    opacity: 1;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
  }

  h2 {
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

  button {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 20px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    background-color: rgb(200, 195, 195);
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s, box-shadow 0.3s;
  }

  button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
  }

  button::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 50%;
    border-top-left-radius: 30px;
    border-top-right-radius: 30px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.4), rgba(255,255,255,0.05));
    pointer-events: none;
  }

  @media (max-width: 600px) {
    .container {
      flex-direction: column;
      align-items: center;
    }
    .restaurant-card {
      max-width: 90%;
    }
  }
</style>


<div class="container">
  {#each restaurants as restaurant}
    <div class="restaurant-card" on:click={() => goTo("restaurant-detail", restaurant.id)}>
      <!-- Ak galéria obsahuje obrázok, zobraz ho -->
      {#if restaurant.gallery && restaurant.gallery.images && restaurant.gallery.images.length > 0}
        <img
          class="restaurant-image"
          src={`http://localhost:8000/storage/${restaurant.gallery.images[0].path}`}
          alt={restaurant.name}
        />
      {:else}
        <img
          class="restaurant-image"
          src="placeholder-image.jpg"
          alt="Obrázok nie je dostupný"
        />
      {/if}

      <h2>{restaurant.name}</h2>
      {#if isAuthenticated&& userRole === 'admin'} 
        <div class="buttons">
          <button class="edit" on:click={() => goTo("edit-restaurant", restaurant.id)} on:click|stopPropagation>
            📝 Upraviť
          </button>
          <button class="delete" on:click={() => deleteRestaurant(restaurant.id)} on:click|stopPropagation>
            🗑️ Vymazať
          </button>
        </div>
       {/if}
    </div>
  {/each}
</div>

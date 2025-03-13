<script>
  import { onMount } from "svelte";

  export let restaurantId;
  export let isAuthenticated = false;
  export let userEmail = "";

  let restaurant = null;
  let errorMessage = "";
  let isLoading = false;

  let reviews = [];         
  let editingReviewId = null;
  let editingText = "";
  let editingEvaluation = 5;
  let newComment = "";     
  let newEvaluation = 5;    
  let isSubmitting = false;

  let lat = null;
  let lng = null;
  let mapUrl = "";

  let currentIndex = 0;
  let interval;

  onMount(async () => {
    await fetchRestaurantDetail();
    await fetchReviews();      
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

  async function fetchReviews() {
    try {
      const res = await fetch(`http://localhost:8000/api/reviews?restaurantId=${restaurantId}`);
      if (!res.ok) {
        throw new Error("Nepodarilo sa načítať komentáre.");
      }
      reviews = await res.json();
    } catch (err) {
      console.error("Chyba pri načítaní komentárov:", err);
    }
  }

   async function deleteReview(id) {
    if (!confirm("Naozaj chcete vymazať recenziu?")) return;
    try {
      const res = await fetch(`http://localhost:8000/api/reviews/${id}`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ mail: userEmail }),
      });

      if (!res.ok) throw new Error("Chyba pri mazaní recenzie.");
      await fetchReviews();
    } catch (err) {
      console.error(err);
    }
  }

  function editReview(review) {
    console.log("Upravujem recenziu s ID:", review.id);
    editingReviewId = review.id;
    editingText = review.text;
    editingEvaluation = review.evaluation;
  }

  async function submitEditReview() {
    if (!editingText.trim()) {
      alert("Recenzia nemôže byť prázdna.");
      return;
    }
    try {
      console.log("Odosielam úpravu pre ID:", editingReviewId);
      const res = await fetch(`http://localhost:8000/api/reviews/${editingReviewId}`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          mail: userEmail,
          text: editingText,
          evaluation: editingEvaluation
        }),
      });

      if (!res.ok) throw new Error("Chyba pri úprave recenzie.");
      editingReviewId = null;
      editingText = "";
      editingEvaluation = 5;
      await fetchReviews();
    } catch (err) {
      console.error("Chyba pri odoslaní editácie:", err);
    }
  }

  

  function setEditRating(value) {
    editingEvaluation = value;
  }

  async function submitReview() {
    if (!isAuthenticated) {
      alert("Musíte byť prihlásený na pridanie recenzie.");
      return;
    }
    if (!newComment.trim()) {
      alert("Recenzia nemôže byť prázdna.");
      return;
    }

    isSubmitting = true;
    try {
      const res = await fetch("http://localhost:8000/api/reviews", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          mail: userEmail,        
          id_restaurant: restaurantId, 
          text: newComment,
          evaluation: newEvaluation
        }),
      });

      if (!res.ok) throw new Error("Chyba pri odosielaní recenzie.");

      newComment = "";
      newEvaluation = 5;

      await fetchReviews();
    } catch (err) {
      console.error("Chyba pri odosielaní:", err);
    } finally {
      isSubmitting = false;
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
    if (!address) return;

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
    if (restaurant?.gallery?.images?.length > 1) {
      interval = setInterval(() => {
        nextSlide();
      }, 4000);
    }
  }

  function setRating(value) {
    newEvaluation = value;
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
    {#if restaurant.gallery?.images?.length > 0}
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
        {#if restaurant.gallery.images.length > 1}
          <button class="prev" on:click={prevSlide}>❮</button>
          <button class="next" on:click={nextSlide}>❯</button>
        {/if}

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
      <iframe
        width="600"
        height="450"
        style="border:0"
        loading="lazy"
        allowfullscreen
        src={mapUrl}
      ></iframe>
    {:else}
      <p>Načítavam mapu...</p>
    {/if}
    reviews

    <h2>Recenzie</h2>
    {#if reviews.length > 0}
      <ul class="review-list">
        {#each reviews as review}
          <li class="review">
            <strong>{review.mail}</strong> – 
            <span class="stars">
              {#each Array(5) as _, i}
                <span class="star {i < review.evaluation ? 'filled' : ''}">★</span>
              {/each}
            </span>
            <p>{review.text}</p>
            {#if isAuthenticated && (userEmail === review.mail)}
              <button on:click={() => editReview(review)}>Upraviť</button>
              <button on:click={() => deleteReview(review.id)}>Vymazať</button>
            {/if}
            {#if editingReviewId === review.id}
              <div class="edit-form">
                <h3>Upraviť recenziu</h3>
                <textarea bind:value={editingText}></textarea>
                <div class="rating">
                  {#each Array(5) as _, i}
                    <span class="star {i < editingEvaluation ? 'filled' : ''}" on:click={() => setEditRating(i + 1)}>★</span>
                  {/each}
                </div>
                <button on:click={submitEditReview}>Uložiť zmeny</button>
                <button on:click={() => editingReviewId = null}>Zrušiť</button>
              </div>
            {/if}
          </li>
        {/each}
      </ul>
    {:else}
        <p>Zatiaľ žiadne recenzie.</p>
    {/if}

    

    {#if isAuthenticated}
      <div class="review-form">
        <label>Pridať recenziu:</label>
        <textarea bind:value={newComment} placeholder="Napíš svoju recenziu..."></textarea>
        <label>Hodnotenie:</label>
        <div class="rating">
          {#each Array(5) as _, i}
            <span class="star {i < newEvaluation ? 'filled' : ''}" on:click={() => setRating(i + 1)}>★</span>
          {/each}
        </div>
        <button on:click={submitReview} disabled={isSubmitting}>
          {isSubmitting ? "Odosielanie..." : "Odoslať"}
        </button>
      </div>
    {:else}
      <p>Prihláste sa na pridanie recenzie.</p>
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
  .review-list {
    list-style: none;
    padding: 0;
  }

  .stars {
    font-size: 1.2rem;
  }
  .rating {
    display: flex;
    gap: 5px;
    margin: 10px 0;
  }

  .star {
    font-size: 2rem;
    cursor: pointer;
    color: gray; 
    transition: color 0.3s;
  }

  .star.filled {
    color: gold; 
  }

  .star:hover {
    color: orange; 
  }
  .review {
    background: #f9f9f9;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
  }

  .review-form {
    display: flex;
    flex-direction: column;
    margin-top: 20px;
  }

  textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
  }
  input {
    width: 60px;
    margin: 10px 0;
  }

  button {
    padding: 10px;
    border: none;
    background: #28a745;
    color: white;
    cursor: pointer;
    border-radius: 5px;
  }

  button:disabled {
    background: gray;
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

  .main-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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

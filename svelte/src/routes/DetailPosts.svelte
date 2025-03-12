<script>
  import { onMount } from "svelte";

  export let postId;
  export let isAuthenticated;
  export let userEmail;
  let post = null;
  let errorMessage = "";
  let isLoading = false;
  let comments = [];
  let newComment = "";
  let newEvaluation = 5;
  let isSubmitting = false;
  let lat = null;
  let lng = null;
  let mapUrl = "";

  let currentIndex = 0;
  let interval;

  onMount(async () => {
    await fetchPostDetail();
    await fetchComments();
    startAutoSlide();
  });

  async function fetchPostDetail() {
    isLoading = true;
    try {
      const res = await fetch(`http://localhost:8000/api/posts/${postId}`);
      if (!res.ok) {
        throw new Error(`Nepodarilo sa načítať príspevok (HTTP ${res.status}).`);
      }
      post = await res.json();

      if (post.address) {
        await fetchCoordinates();
      }
    } catch (err) {
      errorMessage = err.message;
      console.error("Chyba pri načítaní príspevku:", err);
    } finally {
      isLoading = false;
    }
  }
  async function fetchComments() {
    try {
      const res = await fetch(`http://localhost:8000/api/reviews?postId=${postId}`);
      if (!res.ok) throw new Error("Nepodarilo sa načítať komentáre.");
      comments = await res.json();
    } catch (err) {
      console.error("Chyba pri načítaní komentárov:", err);
    }
  }

  async function submitComment() {
  if (!isAuthenticated) {
    alert("Musíte byť prihlásený na pridanie komentára.");
    return;
  }
  if (!newComment.trim()) {
    alert("Komentár nemôže byť prázdny.");
    return;
  }

  isSubmitting = true;
  try {
    const res = await fetch("http://localhost:8000/api/reviews", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        mail: userEmail,       // <--- sem dáš email
        id_post: postId,       // <--- recenzia k príspevku
        text: newComment,
        evaluation: newEvaluation,
      }),
    });

    if (!res.ok) throw new Error("Chyba pri odosielaní komentára.");

    newComment = "";
    newEvaluation = 5;
    await fetchComments(); 
  } catch (err) {
    console.error("Chyba pri odosielaní:", err);
  } finally {
    isSubmitting = false;
  }
}


  function getFullAddress() {
    if (!post || !post.address) return "";
    const addr = post.address;
    
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
    if (post && post.gallery && post.gallery.images.length > 0) {
      currentIndex = (currentIndex - 1 + post.gallery.images.length) % post.gallery.images.length;
    }
  }

  function nextSlide() {
    if (post && post.gallery && post.gallery.images.length > 0) {
      currentIndex = (currentIndex + 1) % post.gallery.images.length;
    }
  }

  function startAutoSlide() {
    if (post && post.gallery && post.gallery.images && post.gallery.images.length > 1) {
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
  <p>Načítavam príspevok...</p>
{:else if errorMessage}
  <p style="color: red;">{errorMessage}</p>
{:else if !post}
  <p>Príspevok sa nenašiel.</p>
{:else}
  <div class="detail-container">
    <!-- Slider obrázkov -->
    {#if post.gallery && post.gallery.images && post.gallery.images.length > 0}
      <div class="slider-container">
        <div class="slider" style="transform: translateX({-100 * currentIndex}%);">
          {#each post.gallery.images as image, i}
            <img class="slide" src={`http://localhost:8000/storage/${image.path}`} alt={post.name} />
          {/each}
        </div>
        <!-- Navigačné šípky, zobraz len ak viac ako 1 obrázok -->
        {#if post.gallery.images.length > 1}
          <button class="prev" on:click={prevSlide}>❮</button>
          <button class="next" on:click={nextSlide}>❯</button>
        {/if}
      </div>
    {:else}
      <img class="main-image" src="placeholder-image.jpg" alt="Obrázok nie je dostupný" />
    {/if}

    
    <h1>{post.name}</h1>

    
    {#if post.opening_hours}
      <p><strong>Otváracie hodiny:</strong> {post.opening_hours}</p>
    {/if}

    
    {#if post.url_address}
      <p>
        <strong>Web:</strong>
        <a href={post.url_address} target="_blank" rel="noopener noreferrer">
          {post.url_address}
        </a>
      </p>
    {/if}

    
    {#if post.description}
      <p>{post.description}</p>
    {/if}

    
    {#if post.address}
      <p><strong>Adresa:</strong> {getFullAddress()}</p>
    {/if}

    
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

    
    <h2>Komentáre</h2>
    {#if comments.length > 0}
      <ul class="comment-list">
        {#each comments as comment}
          <li class="comment">
            <strong>{comment.mail}</strong> – 
            <span class="stars">
              {#each Array(5) as _, i}
                <span class="star {i < comment.evaluation ? 'filled' : ''}">★</span>
              {/each}
            </span>
            <p>{comment.text}</p>
          </li>
        {/each}
      </ul>
    {:else}
      <p>Zatiaľ žiadne komentáre.</p>
    {/if}

    
    {#if isAuthenticated}
      <div class="comment-form">
        <label>Pridať komentár:</label>
        <textarea bind:value={newComment} placeholder="Napíš svoj komentár..."></textarea>

        <label>Hodnotenie:</label>
        <div class="rating">
          {#each Array(5) as _, i}
            <span class="star {i < newEvaluation ? 'filled' : ''}" on:click={() => setRating(i + 1)}>★</span>
          {/each}
        </div>

        <button on:click={submitComment} disabled={isSubmitting}>
          {isSubmitting ? "Odosielanie..." : "Odoslať"}
        </button>
      </div>
    {:else}
      <p>Prihláste sa na pridanie komentára.</p>
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
  .comment-list {
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
    color: gray; /* Predvolene sivé hviezdičky */
    transition: color 0.3s;
  }

  .star.filled {
    color: gold; /* Po kliknutí sa zmenia na zlatú */
  }

  .star:hover {
    color: orange; /* Pri hoveri sa zmenia na oranžovú */
  }
  .comment {
    background: #f9f9f9;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
  }

  .comment-form {
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

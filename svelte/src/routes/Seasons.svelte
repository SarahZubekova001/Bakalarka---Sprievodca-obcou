<script>
  import { onMount } from "svelte";
  export let goTo;
  
  let seasons = [];

  async function fetchSeasons() {
    try {
      const res = await fetch("http://localhost:8000/api/seasons");
      if (!res.ok) throw new Error("Nepodarilo sa načítať sezóny");
      seasons = await res.json();
    } catch (err) {
      console.error(err);
    }
  }

  onMount(fetchSeasons);

  async function deleteSeason(id) {
    if (!confirm("Naozaj chceš vymazať túto sezónu?")) return;
    await fetch(`http://localhost:8000/api/seasons/${id}`, { method: "DELETE" });
    seasons = seasons.filter(season => season.id !== id);
  }
</script>

<style>
  /* Kontajner s kartami */
  .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 25px;
    font-family: sans-serif;
    background: #f5f9fd; /* Jemné pozadie */
  }

  /* Každá karta sezóny */
  .season-card {
    flex: 1 1 300px;         /* Responzívne rozdelenie šírky */
    max-width: 350px;        /* Mierne obmedzenie maximálnej šírky karty */
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  /* Hover efekt karty */
  .season-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
  }

  /* Obrázok sezóny */
  img {
    width: 100%;
    height: 250px;         /* Väčší obrázok */
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
  }

  /* Nadpis sezóny */
  h2 {
    font-size: 22px;
    margin: 10px 0;
    color: #333;
  }

  /* Sekcia pre tlačidlá */
  .buttons {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
    gap: 10px;
  }

  /* ----- OVÁLNE TLAČIDLÁ S JEDNOU FARBOU ----- */
  button {
    /* Tvar a rozloženie */
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;               /* Medzera medzi emoji a textom */
    padding: 10px 20px;
    border: none;
    border-radius: 30px;    /* „Pill“ tvar */
    cursor: pointer;
    font-weight: bold;
    font-size: 1rem;

    /* Farba textu a tieň */
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);

    /* Jedna farba pre celé tlačidlo */
    background-color:rgb(200, 195, 195); /* Môžeš zmeniť na #ccc, #aaa atď. */

    /* Jemný tieň tlačidla */
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);

    /* Animácia pri hover */
    transition: transform 0.3s, box-shadow 0.3s;
  }

  button:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 12px rgba(0,0,0,0.25);
  }

  /* „Lesklý“ efekt cez pseudo-element */
  button::after {
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
    pointer-events: none; /* aby hover fungoval cez tento prvok */
  }

  /* Responsivita pre menšie displeje */
  @media (max-width: 600px) {
    .container {
      flex-direction: column;
      align-items: center;
    }
    .season-card {
      max-width: 90%;
    }
  }
</style>

<div class="container">
  {#each seasons as season}
    <div class="season-card">
      <img
        src={`http://localhost:8000/storage/${season.image.path}`}
        alt={season.name}
      />
      <h2>{season.name}</h2>
      <div class="buttons">
        <button class="edit" on:click={() => goTo('edit-season', season.id)}>
          📝 Upraviť
        </button>
        <button class="delete" on:click={() => deleteSeason(season.id)}>
          🗑️ Vymazať
        </button>
      </div>
    </div>
  {/each}
</div>

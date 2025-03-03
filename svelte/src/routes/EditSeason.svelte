<script>
  import { onMount } from "svelte";

  export let selectedSeasonId;
  export let goTo;

  let name = "";
  let image = null;
  let existingImage = "";

  async function fetchSeason() {
    const res = await fetch(`http://localhost:8000/api/seasons/${selectedSeasonId}`);
    const season = await res.json();
    name = season.name;
    existingImage = season.image.path;
  }

  async function updateSeason() {
    const formData = new FormData();
    formData.append("name", name);
    if (image) {
        formData.append("image", image);
    }

    const response = await fetch(`http://localhost:8000/api/seasons/${selectedSeasonId}`, {
        method: "POST", // ⚠️ POUŽÍVAME POST A OVERRIDE!
        headers: { 
            "X-HTTP-Method-Override": "PUT" // Laravel pochopí, že je to UPDATE
        },
        body: formData,
    });

    if (!response.ok) {
        const errorData = await response.json();
        console.error("Chyba pri ukladaní:", errorData);
        return;
    }

    goTo("seasons");
}

  // 🔥 Načítame sezónu pri načítaní komponentu
  onMount(fetchSeason);
</script>

<h1>Upraviť sezónu</h1>
<input type="text" bind:value={name} placeholder="Názov sezóny" />
<img src={`http://localhost:8000/storage/${existingImage}`} alt="Aktuálny obrázok" width="100" />
<input type="file" on:change={(e) => image = e.target.files[0]} />
<button on:click={updateSeason}>Uložiť</button>
<button on:click={() => goTo("seasons")}>Späť</button>

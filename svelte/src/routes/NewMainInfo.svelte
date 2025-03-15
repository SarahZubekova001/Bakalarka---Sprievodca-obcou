<script>
  import { onMount } from "svelte";

  let town_name = "";
  let description = "";
  let logo = null;
  let isLoading = true;
  let errorMessage = "";

  onMount(async () => {
    try {
      const res = await fetch("http://localhost:8000/api/maininfo");
      if (!res.ok) throw new Error("Nepodarilo sa načítať main_info.");
      const data = await res.json();
      
      town_name = data.town_name;
      description = data.description;
      logo = data.logo;
    } catch (err) {
      errorMessage = err.message;
    } finally {
      isLoading = false;
    }
  });

  async function submitForm() {
    try {
      errorMessage = "";
      const res = await fetch("http://localhost:8000/api/maininfo", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ town_name, description, logo })
      });
      if (!res.ok) throw new Error("Nepodarilo sa uložiť main_info.");
      const updatedData = await res.json();
      town_name = updatedData.town_name;
      description = updatedData.description;
      logo = updatedData.logo;

      alert("Úspešne uložené!");
    } catch (err) {
      errorMessage = err.message;
    }
  }
</script>

{#if isLoading}
  <p>Načítavam...</p>
{:else}
  {#if errorMessage}
    <p style="color:red">{errorMessage}</p>
  {/if}

  <form on:submit|preventDefault={submitForm}>
    <div>
      <label>Town name:</label>
      <input type="text" bind:value={town_name} />
    </div>

    <div>
      <label>Description:</label>
      <textarea bind:value={description}></textarea>
    </div>


    <div>
      <label>Logo súbor:</label>
      <input type="file" accept="image/*"  />
    </div>

    <button type="submit">Uložiť</button>
  </form>
{/if}

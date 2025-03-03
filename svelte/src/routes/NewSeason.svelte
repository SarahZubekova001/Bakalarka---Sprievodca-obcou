<script>
    import { createSeason } from '../lib/api.js';

    export let goTo; // ✅ Použi správny názov namiesto `navigate`

    let name = "";
    let imageFile = null;

    async function submitSeason() {
        const formData = new FormData();
        formData.append('name', name);
        if (imageFile) {
            formData.append('image', imageFile);
        }

        try {
            await createSeason(formData);
            alert("Sezóna bola pridaná!");
            goTo('seasons'); // ✅ Správne presmerovanie
        } catch (error) {
            console.error("Chyba pri vytváraní sezóny:", error);
        }
    }
</script>

<h1>Pridať novú sezónu</h1>
<form on:submit|preventDefault={submitSeason}>
    <label>Názov:
        <input type="text" bind:value={name} required>
    </label>
    <label>Obrázok:
        <input type="file" on:change={(e) => imageFile = e.target.files[0]}>
    </label>
    <button type="submit">Vytvoriť sezónu</button>
</form>

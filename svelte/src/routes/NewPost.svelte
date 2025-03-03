<script>
    import { createPost } from '../lib/api.js';

    let name = "";
    let description = "";

    async function submitPost() {
        await createPost({ name, description });
        alert("Príspevok bol vytvorený!");
    }
</script>

<h1>Nový príspevok</h1>
<form on:submit|preventDefault={submitPost}>
    <label>Názov:
        <input type="text" bind:value={name} required>
    </label>
    <label>Popis:
        <textarea bind:value={description}></textarea>
    </label>
    <button type="submit">Vytvoriť</button>
</form>

<style>
    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 300px;
    }
    
    input, textarea {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #28a745;
        color: white;
        padding: 10px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
    }
</style>

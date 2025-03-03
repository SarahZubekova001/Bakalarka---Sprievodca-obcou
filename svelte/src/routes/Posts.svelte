<script>
    import { onMount } from 'svelte';
    import { fetchPosts, deletePost } from '../lib/api.js';

    let posts = [];

    onMount(async () => {
        posts = await fetchPosts();
    });

    async function removePost(id) {
        if (confirm("Naozaj chceš vymazať tento príspevok?")) {
            await deletePost(id);
            posts = posts.filter(post => post.id !== id);
        }
    }
</script>

<h1 class="title">Zoznam príspevkov</h1>
<button class="add-button">➕ Pridať príspevok</button>

<table class="posts-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Názov</th>
            <th>Popis</th>
            <th>Akcie</th>
        </tr>
    </thead>
    <tbody>
        {#each posts as post, index}
        <tr>
            <td>{index + 1}</td>
            <td>{post.name}</td>
            <td>{post.description}</td>
            <td>
                <button class="edit-button">✏️ Upraviť</button>
                <button class="delete-button" on:click={() => removePost(post.id)}>🗑️ Vymazať</button>
            </td>
        </tr>
        {/each}
    </tbody>
</table>

<style>
    .title {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .add-button {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 15px;
    }

    .posts-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .posts-table th, .posts-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .posts-table th {
        background-color: #343a40;
        color: white;
    }

    .edit-button {
        background-color: #ffc107;
        color: black;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        margin-right: 5px;
    }

    .delete-button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }
</style>

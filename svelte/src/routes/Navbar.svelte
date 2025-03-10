<script>
  import { onMount } from "svelte";
  export let isAuthenticated = false;
  export let logout;
  export let goTo;
  export let userRole;

  let seasons = [];
  let categories = [];

  async function fetchSeasons() {
    try {
      const res = await fetch("http://localhost:8000/api/seasons");
      if (!res.ok) throw new Error("Nepodarilo sa načítať sezóny");
      seasons = await res.json();
    } catch (err) {
      console.error("Chyba pri načítaní sezón:", err);
    }
  }

  async function fetchCategories() {
    try {
      const res = await fetch("http://localhost:8000/api/categories");
      if (!res.ok) throw new Error("Nepodarilo sa načítať kategórie");
      categories = await res.json();
    } catch (err) {
      console.error("Chyba pri načítaní kategórií:", err);
    }
  }

  onMount(() => {
    fetchSeasons();
    fetchCategories();
  });
</script>

<nav class="navbar">
  <div class="nav-container">
    <a class="logo" on:click={() => goTo("seasons")}>Obec</a>

    <ul class="nav-links">
      <!-- Dynamické sezóny -->
      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("seasons")}>Sezóny</a>
        <ul class="dropdown-content">
          {#each seasons as season}
            <li>
              <a on:click={() => goTo(`season/${season.id}`)}>{season.name}</a>
            </li>
          {/each}
          {#if isAuthenticated && userRole === "admin"}
            <li>
              <a on:click={() => goTo("add-season")}>Pridať sezónu</a>
            </li>
          {/if}
        </ul>
      </li>

      <!-- Dynamické kategórie -->
      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("categories")}>Kategórie</a>
        <ul class="dropdown-content">
          {#each categories as category}
            <li>
              <a on:click={() => goTo(`category/${category.id}`)}>{category.name}</a>
            </li>
          {/each}
          {#if isAuthenticated && userRole === "admin"}
            <li>
              <a on:click={() => goTo("add-category")}>Pridať kategóriu</a>
            </li>
          {/if}
        </ul>
      </li>

      <!-- Reštaurácie -->
      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("restaurants")}>Reštaurácie</a>
        <ul class="dropdown-content">
          {#if isAuthenticated && userRole === "admin"}
            <li>
              <a on:click={() => goTo("add-restaurant")}>Pridať reštauráciu</a>
            </li>
          {/if}
        </ul>
      </li>

      <!-- Prihlásiť / Odhlásiť -->
      {#if isAuthenticated}
        <li>
          <button class="auth-button" on:click={logout}>Odhlásiť</button>
        </li>
      {:else}
        <li>
          <button class="auth-button" on:click={() => goTo("login")}>Prihlásiť</button>
          <button class="auth-button" on:click={() => goTo("register")}>Registrovat sa</button>
        </li>
      {/if}
    </ul>
  </div>
</nav>

<style>
  .navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(135deg, rgb(108, 210, 198) 0%, #e1f6ff 100%);
    color: #333;
    padding: 12px 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    font-family: sans-serif;
  }

  .nav-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .logo {
    font-size: 1.6rem;
    font-weight: 700;
    text-decoration: none;
    color: #333;
    cursor: pointer;
  }

  .nav-links {
    list-style: none;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin: 0;
    padding: 0;
  }

  .nav-links a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 0;
    transition: color 0.2s ease;
    cursor: pointer;
  }

  .dropdown {
    position: relative;
    display: inline-block;
  }

  .dropbtn {
    background: transparent;
    border: none;
    font-weight: 500;
    color: #333;
    cursor: pointer;
    padding: 8px 0;
    font-size: 1rem;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    background-color: #fff;
    min-width: 160px;
    margin-top: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    border-radius: 4px;
    z-index: 999;
    list-style: none;
    padding: 0;
  }

  .dropdown:hover .dropdown-content {
    display: block;
  }

  .dropdown-content li a {
    display: block;
    padding: 10px 16px;
    text-decoration: none;
    color: #333;
  }

  .dropdown-content li a:hover {
    background-color: #f0f0f0;
  }

  .auth-button {
    background: #fff;
    color: #333;
    border: none;
    padding: 8px 16px;
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .auth-button:hover {
    background: #f0f0f0;
  }
</style>

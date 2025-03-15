<script>
  import { onMount } from "svelte";

  export let goTo;
  export let isAuthenticated;
  export let userRole;
  export let userName;
  export let logout;

  let mainInfo = null;
  let errorMessage = "";
  let isLoading = true;

  onMount(async () => {
    try {
      const res = await fetch("http://localhost:8000/api/maininfo");
      if (!res.ok) throw new Error("Nepodarilo sa načítať údaje o obci");
      mainInfo = await res.json();
    } catch (err) {
      errorMessage = err.message;
      console.error(err);
    } finally {
      isLoading = false;
    }
  });

  function goToMainInfo() {
    goTo("maininfo");
  }

  function toggleUserMenu() {
    showUserMenu = !showUserMenu;
  }
  
  let showUserMenu = false;
</script>

<nav class="navbar">
  <div class="navbar-left">
    {#if isLoading}
      <div class="logo-placeholder">Loading...</div>
    {:else if errorMessage}
      <div class="logo-placeholder">Error</div>
    {:else if mainInfo && mainInfo.logo_image}
      <img 
        src={`http://localhost:8000/storage/${mainInfo.logo_image.path}`} 
        alt="Logo obce" 
        class="navbar-logo"
        on:click={goToMainInfo}
      />
    {:else}
      <div class="logo-placeholder" on:click={goToMainInfo}>Logo</div>
    {/if}
  </div>
  <div class="navbar-center">
    <ul class="nav-links">
      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("seasons")}>Home</a>
        <ul class="dropdown-content">
          <!-- Prípadne položky pre sezóny -->
          {#if isAuthenticated && userRole === "admin"}
            <li><a on:click={() => goTo("add-season")}>Pridať sezónu</a></li>
          {/if}
        </ul>
      </li>

      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("categories")}>Kategórie</a>
        <ul class="dropdown-content">
          <!-- Prípadne položky pre kategórie -->
          {#if isAuthenticated && userRole === "admin"}
            <li><a on:click={() => goTo("add-category")}>Pridať kategóriu</a></li>
          {/if}
        </ul>
      </li>

      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("restaurants")}>Reštaurácie</a>
        <ul class="dropdown-content">
          {#if isAuthenticated && userRole === "admin"}
            <li><a on:click={() => goTo("add-restaurant")}>Pridať reštauráciu</a></li>
          {/if}
        </ul>
      </li>

      <li class="dropdown">
        <a class="dropbtn" on:click={() => goTo("maininfo")}>O Obci</a>
      </li>
    </ul>
  </div>
  <div class="navbar-right">
    {#if isAuthenticated}
      <div class="user-menu">
        <div class="user-info" on:click={() => showUserMenu = !showUserMenu}>
          <span class="user-icon">👤</span>
          <span class="user-name">{userName}</span>
          <span class="user-role">({userRole})</span>
        </div>
        {#if showUserMenu}
          <ul class="user-dropdown">
            {#if userRole === "admin"}
              <li><a on:click={() => goTo("manage-accounts")}>⚙️ Spravovať účty</a></li>
            {/if}
            <li><a on:click={logout}>🚪 Odhlásiť sa</a></li>
          </ul>
        {/if}
      </div>
    {:else}
      <button class="auth-button" on:click={() => goTo("login")}>Prihlásiť sa</button>
      <button class="auth-button" on:click={() => goTo("register")}>Registrovať sa</button>
    {/if}
  </div>
</nav>

<style>
  .navbar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(135deg, rgb(108,210,198) 0%, #e1f6ff 100%);
    color: #333;
    padding: 12px 20px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    z-index: 1000;
    font-family: sans-serif;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .navbar-left, .navbar-center, .navbar-right {
    display: flex;
    align-items: center;
  }
  .navbar-left {
    /* Logo bude úplne vľavo */
    flex: 0 0 auto;
  }
  .navbar-logo {
    max-height: 50px;
    cursor: pointer;
  }
  .logo-placeholder {
    font-size: 1.5rem;
    font-weight: bold;
    cursor: pointer;
  }
  .navbar-center {
    flex: 1;
    display: flex;
    justify-content: center;
  }
  .nav-links {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
  }
  .nav-links a, .dropbtn {
    color: #333;
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
  }
  .dropdown {
    position: relative;
    display: inline-block;
  }
  .dropdown-content {
    display: none;
    position: absolute;
    background: #fff;
    min-width: 160px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    border-radius: 4px;
    z-index: 100;
    list-style: none;
    padding: 0;
  }
  .dropdown:hover .dropdown-content {
    display: block;
  }
  .dropdown-content li a {
    display: block;
    padding: 10px;
    color: #333;
    text-decoration: none;
  }
  .dropdown-content li a:hover {
    background: #f0f0f0;
  }
  .navbar-right {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .user-menu {
    position: relative;
  }
  .user-info {
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
  }
  .user-icon {
    font-size: 1.2rem;
  }
  .user-name, .user-role {
    font-size: 0.9rem;
  }
  .user-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    list-style: none;
    padding: 0;
    margin: 5px 0 0 0;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    z-index: 100;
  }
  .user-dropdown li a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
  }
  .user-dropdown li a:hover {
    background: #f0f0f0;
  }
  .auth-button {
    background: #fff;
    color: #333;
    border: 1px solid #ccc;
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

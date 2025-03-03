<script>
  export let isAuthenticated = false;
  export let logout;
  export let goTo;
</script>

<nav class="navbar">
  <div class="nav-container">
    <a class="logo" on:click={() => goTo("seasons")}>Zuberec</a>

    <ul class="nav-links">
      <li><a on:click={() => goTo("seasons")}>Domov</a></li>
      <li><a on:click={() => goTo("add-season")}>Pridať sezónu</a></li>

      {#if isAuthenticated}
        <li><button class="auth-button" on:click={logout}>Odhlásiť</button></li>
      {:else}
        <li><button class="auth-button" on:click={() => goTo("login")}>Prihlásiť</button></li>
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
    background: linear-gradient(135deg, rgb(170, 241, 233) 0%, #e1f6ff 100%);
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
  }

  .nav-links a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 0;
    transition: color 0.2s ease;
    cursor: pointer;
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

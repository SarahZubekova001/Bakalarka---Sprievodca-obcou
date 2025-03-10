<script>
  import { createEventDispatcher } from "svelte";
  let email = "";
  let password = "";
  let errorMessage = "";
  const dispatch = createEventDispatcher();

  async function login() {
    errorMessage = "";
    try {
        const res = await fetch("http://localhost:8000/api/login", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, password }),
            credentials: "include", 
        });

        if (res.ok) {
            const data = await res.json();
            localStorage.setItem("access_token", data.access_token);
            dispatch("loginSuccess");
        } else {
            const data = await res.json();
            errorMessage = data.message || "Nesprávne prihlasovacie údaje.";
        }
    } catch (error) {
        errorMessage = "Chyba siete pri prihlasovaní.";
    }
}

</script>

<div class="login-container">
  <h2>Prihlásenie</h2>
  {#if errorMessage}
    <p class="error">{errorMessage}</p>
  {/if}
  
  <form on:submit|preventDefault={login}>
    <label>E-mail:</label>
    <input type="email" bind:value={email} required />

    <label>Heslo:</label>
    <input type="password" bind:value={password} required />

    <button type="submit">Prihlásiť</button>
  </form>
</div>

<style>
  .login-container {
    max-width: 400px;
    margin: 80px auto;
    padding: 20px;
    border-radius: 8px;
    background: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
  .error { color: red; margin-top: 10px; }
  label { display: block; margin: 8px 0 4px; }
  input {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
  }
  button {
    padding: 10px;
    width: 100%;
    cursor: pointer;
  }
</style>

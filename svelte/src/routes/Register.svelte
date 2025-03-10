<script>
  import { createEventDispatcher } from "svelte";

  let name = "";
  let email = "";
  let password = "";
  let passwordConfirm = "";
  let errorMessage = "";
  const dispatch = createEventDispatcher();

  async function register() {
    errorMessage = "";
    
    if (password !== passwordConfirm) {
      errorMessage = "Heslá sa nezhodujú.";
      return;
    }

    try {
      const res = await fetch("http://localhost:8000/api/register", {
        method: "POST",
        headers: { 
          "Content-Type": "application/json",
          "Accept": "application/json"
        },
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation: passwordConfirm
        }),
        credentials: "include", 
      });

      const data = await res.json();

      if (res.ok) {
        console.log("Registrácia úspešná:", data);
        localStorage.setItem("access_token", data.access_token); 
        dispatch("registerSuccess");
      } else {
        console.error("Chyba pri registrácii:", data);
        errorMessage = data.message || "Registrácia zlyhala.";
      }
    } catch (error) {
      console.error("Chyba siete pri registrácii:", error);
      errorMessage = "Chyba siete pri registrácii.";
    }
  }
</script>


<div class="register-container">
  <h2>Registrácia</h2>
  {#if errorMessage}
    <p class="error">{errorMessage}</p>
  {/if}
  
  <form on:submit|preventDefault={register}>
    <label>Meno:</label>
    <input type="text" bind:value={name} required />

    <label>E-mail:</label>
    <input type="email" bind:value={email} required />

    <label>Heslo:</label>
    <input type="password" bind:value={password} required />

    <label>Potvrdenie hesla:</label>
    <input type="password" bind:value={passwordConfirm} required />

    <button type="submit">Registrovať</button>
  </form>
</div>

<style>
  .register-container {
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

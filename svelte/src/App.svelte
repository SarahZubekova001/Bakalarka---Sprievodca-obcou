<script>
  import Navbar from "./routes/Navbar.svelte";
  import Seasons from "./routes/Seasons.svelte";
  import AddSeason from "./routes/NewSeason.svelte";
  import EditSeason from "./routes/EditSeason.svelte";
  import Login from "./routes/Login.svelte";
  import { onMount } from "svelte";

  let page = "seasons";
  let selectedSeasonId = null;
  let isAuthenticated = false;

  function goTo(newPage, id = null) {
    page = newPage;
    selectedSeasonId = id;
  }

  async function checkAuth() {
    const token = localStorage.getItem("access_token");

    if (!token) {
        console.log("Nie si prihlásený");
        isAuthenticated = false;
        return;
    }

    const res = await fetch("http://localhost:8000/api/user", {
        headers: { Authorization: `Bearer ${token}` },
        credentials: "include",
    });

    if (res.ok) {
        isAuthenticated = true;
    } else if (res.status === 401) {
        console.log("Token expirovaný, skúšam obnoviť...");
        await refreshAccessToken();
    } else {
        isAuthenticated = false;
    }
  }

  async function refreshAccessToken() {
      try {
          const res = await fetch("http://localhost:8000/api/refresh", {
              method: "POST",
              credentials: "include",
          });

          if (res.ok) {
              const data = await res.json();
              localStorage.setItem("access_token", data.access_token);
              isAuthenticated = true;
          } else {
              console.log("Refresh token je neplatný");
              isAuthenticated = false;
          }
      } catch (error) {
          console.error("Chyba pri obnove tokenu:", error);
          isAuthenticated = false;
      }
  }

  async function logout() {
    await fetch("http://localhost:8000/api/logout", {
        method: "POST",
        credentials: "include",
    });

    localStorage.removeItem("access_token");
    isAuthenticated = false;
    goTo("login");
  }



  onMount(checkAuth);
</script>

<Navbar {isAuthenticated} {goTo} {logout} />

{#if page === "seasons"}
  <Seasons {goTo} />
{:else if page === "add-season"}
  <AddSeason {goTo} />
{:else if page === "edit-season"}
  <EditSeason {selectedSeasonId} {goTo} />
{:else if page === "login"}
  <Login
    on:loginSuccess={() => {
      isAuthenticated = true;
      goTo("seasons");
    }}
  />
{/if}

<style>
  body {
    margin: 0;
    padding-top: 60px;
    font-family: sans-serif;
  }
</style>

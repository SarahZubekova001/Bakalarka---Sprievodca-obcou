<script>
  import Navbar from "./routes/Navbar.svelte";
  import Seasons from "./routes/Seasons.svelte";
  import AddSeason from "./routes/NewSeason.svelte";
  import EditSeason from "./routes/EditSeason.svelte";
  import Login from "./routes/Login.svelte";

  import Restaurants from "./routes/Restaurants.svelte";
  import AddRestaurant from "./routes/NewRestaurant.svelte";
  import EditRestaurant from "./routes/EditRestaurant.svelte";
  import DetailRestaurant from "./routes/DetailRestaurant.svelte";

  import { onMount } from "svelte";

  let page = "seasons";
  let currentId = null;
  let isAuthenticated = false;

  function goTo(newPage, id = null) {
    history.pushState({ page: newPage, id }, "", `#${newPage}${id ? `/${id}` : ""}`);
    page = newPage;
    currentId = id;
  }

  function handlePopState(event) {
    if (event.state) {
      page = event.state.page;
      currentId = event.state.id;
    } else {
      page = "seasons";
    }
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

  onMount(() => {
    checkAuth();
    window.addEventListener("popstate", handlePopState);
  });

</script>

<Navbar {isAuthenticated} {goTo} {logout} />

{#if page === "seasons"}
  <Seasons {goTo} {isAuthenticated}/>
{:else if page === "add-season"}
  <AddSeason {goTo} />
{:else if page === "edit-season"}
  <EditSeason selectedSeasonId={currentId} {goTo} />
{:else if page === "add-restaurant"}
   <AddRestaurant {goTo} />
{:else if page === "edit-restaurant"}
  <EditRestaurant restaurantId={currentId} {goTo}/>
{:else if page === "restaurant-detail"}
  <DetailRestaurant restaurantId={currentId} />
 {:else if page === "restaurants"}
  <Restaurants {goTo} {isAuthenticated}/>
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

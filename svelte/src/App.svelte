<script>
  import Navbar from "./routes/Navbar.svelte";
  import Seasons from "./routes/Seasons.svelte";
  import AddSeason from "./routes/NewSeason.svelte";
  import EditSeason from "./routes/EditSeason.svelte";
  import Login from "./routes/Login.svelte";
  import Register from "./routes/Register.svelte";
  import ManageUsers from "./routes/ManageUsers.svelte";

  import Restaurants from "./routes/Restaurants.svelte";
  import AddRestaurant from "./routes/NewRestaurant.svelte";
  import EditRestaurant from "./routes/EditRestaurant.svelte";
  import DetailRestaurant from "./routes/DetailRestaurant.svelte";

  import Categories from "./routes/Categories.svelte";
  import AddCategory from "./routes/NewCategory.svelte";
  import EditCategory from "./routes/EditCategory.svelte";

  import Posts from "./routes/Posts.svelte";
  import AddPost from "./routes/NewPost.svelte";
  import EditPost from "./routes/EditPost.svelte";
  import DetailPosts from "./routes/DetailPosts.svelte";
  

  import { onMount } from "svelte";

  let page = "seasons"; 
  let currentId = null; 
  let currentSeasonId = null; 
  let currentCategoryId = null; 
  let isAuthenticated = false;
  let userRole = null;
  let userName = null;
  let userEmail = ""; 

  function updatePageFromUrl() {
    const hash = window.location.hash.slice(1); 
    if (!hash) {
      page = "seasons";
      currentId = null;
      currentSeasonId = null;
      currentCategoryId = null;
      return;
    }

    const parts = hash.split("/"); 

    page = parts[0] || "seasons";

    if (page === "categories" && parts.length >= 2) {
      currentSeasonId = parts[1] === "null" ? null : parts[1];
    }
    else if (page === "posts" && parts.length >= 3) {
      currentSeasonId = parts[1] === "null" ? null : parts[1];
      currentCategoryId = parts[2] === "null" ? null : parts[2];
    }
    else {
      currentId = parts.length > 1 ? parts[1] : null;
      currentSeasonId = null;
      currentCategoryId = null;
    }
  }

  function goTo(newPage, id = null) {
    console.log("Navigácia na:", newPage, "s ID:", id);

    if (newPage === "categories") {
      if (id?.seasonId !== undefined) {
        currentSeasonId = id.seasonId;
      }
      const url = `#categories/${currentSeasonId ?? "null"}`;

      history.pushState(
        { page: newPage, seasonId: currentSeasonId },
        "",
        url
      );

      console.log("Nová sezóna ID:", currentSeasonId);
    }
    else if (newPage === "posts") {
      if (id?.seasonId !== undefined) {
        currentSeasonId = id.seasonId;
      }
      if (id?.categoryId !== undefined) {
        currentCategoryId = id.categoryId;
      }
      const url = `#posts/${currentSeasonId ?? "null"}/${currentCategoryId ?? "null"}`;

      history.pushState(
        { page: newPage, seasonId: currentSeasonId, categoryId: currentCategoryId },
        "",
        url
      );

      console.log("Nová sezóna ID:", currentSeasonId, "Nová kategória ID:", currentCategoryId);
    }
    else {
      currentId = id;
      history.pushState({ page: newPage, id }, "", `#${newPage}${id ? `/${id}` : ""}`);
    }

    page = newPage;
  }


  function handlePopState(event) {
    if (event.state) {
      if (event.state.page === "posts") {
        page = event.state.page;
        currentSeasonId = event.state.seasonId;
        currentCategoryId = event.state.categoryId;
      } else {
        page = event.state.page;
        currentId = event.state.id;
      }
    } else {
      updatePageFromUrl();
    }
  }

  async function checkAuth() {
    const token = localStorage.getItem("access_token");
    if (!token) {
        console.log("Nie si prihlásený");
        isAuthenticated = false;
        userRole = null;
        userName = null;
        userEmail = null; 
        return;
    }

    const res = await fetch("http://localhost:8000/api/user", {
        headers: { Authorization: `Bearer ${token}` },
        credentials: "include"
    });

    if (res.ok) {
        const data = await res.json();
        isAuthenticated = true;
        userRole = data.role;
        userName = data.name;
        userEmail = data.email; 
    } else if (res.status === 401) {
        console.log("Token expirovaný, skúšam obnoviť...");
        await refreshAccessToken();
    } else {
        isAuthenticated = false;
        userRole = null;
        userName = null;
        userEmail = null; 
    }
  }

  async function refreshAccessToken() {
    try {
      const res = await fetch("http://localhost:8000/api/refresh", {
        method: "POST",
        credentials: "include"
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
      credentials: "include"
    });
    localStorage.removeItem("access_token");
    isAuthenticated = false;
    goTo("login");
  }

  onMount(() => {
    updatePageFromUrl();
    checkAuth();
    window.addEventListener("popstate", handlePopState);
  });
</script>

<Navbar {isAuthenticated} {userRole} {userName} {goTo} {logout} />

{#if page === "seasons"}
  <Seasons {goTo} {isAuthenticated} {userRole}/>
{:else if page === "add-season"}
  <AddSeason {goTo} />
{:else if page === "edit-season"}
  <EditSeason selectedSeasonId={currentId} {goTo} />

{:else if page === "add-restaurant"}
  <AddRestaurant {goTo} />
{:else if page === "edit-restaurant"}
  <EditRestaurant restaurantId={currentId} {goTo}/>
{:else if page === "restaurant-detail"}
  <DetailRestaurant restaurantId={currentId} {isAuthenticated} userEmail={userEmail}/>
{:else if page === "restaurants"}
  <Restaurants {goTo} {isAuthenticated} {userRole}/>

{:else if page === "categories"}
  <Categories {goTo} {isAuthenticated} {userRole} seasonId={currentSeasonId}/>
{:else if page === "add-category"}
  <AddCategory {goTo} />
{:else if page === "edit-category"}
  <EditCategory selectedCategoryId={currentId} {goTo}/>

{:else if page === "posts"}
  <Posts {goTo} {isAuthenticated} {userRole} seasonId={currentSeasonId} categoryId={currentCategoryId} />
{:else if page === "add-post"}
  <AddPost {goTo} />
{:else if page === "edit-post"}
  <EditPost postId={currentId} {goTo}/>
{:else if page === "post-detail"}
  <DetailPosts postId={currentId} {isAuthenticated} userEmail={userEmail}/>

{:else if page === "register"}
  <Register on:registerSuccess={() => {
    isAuthenticated = true;
    goTo("seasons");
  }}/>

{:else if page === "manage-accounts"}
  <ManageUsers />

{:else if page === "login"}
  <Login on:loginSuccess={async() => {
    await checkAuth();
    isAuthenticated = true;
    goTo("seasons");
  }}/>
{/if}



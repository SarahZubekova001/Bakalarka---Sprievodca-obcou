<script>
  import { onMount } from "svelte";

  let admins = [];
  let users = [];
  let errorMessage = "";
  let successMessage = "";

  async function fetchUsers() {
    errorMessage = "";
    try {
      const res = await fetch("http://localhost:8000/api/users", {
        headers: { Authorization: `Bearer ${localStorage.getItem("access_token")}` },
        credentials: "include"
      });

      if (!res.ok) throw new Error("Nepodarilo sa načítať používateľov.");
      const data = await res.json();
      
      admins = data.filter(user => user.role === "admin");
      users = data.filter(user => user.role === "user");

    } catch (err) {
      errorMessage = err.message;
    }
  }

  async function toggleRole(id) {
    errorMessage = "";
    successMessage = "";
    try {
      const res = await fetch(`http://localhost:8000/api/users/${id}/toggle-role`, {
        method: "PATCH",
        headers: { Authorization: `Bearer ${localStorage.getItem("access_token")}` },
        credentials: "include"
      });

      if (!res.ok) throw new Error("Nepodarilo sa zmeniť rolu.");
      const data = await res.json();
      
      // Aktualizácia zoznamu
      fetchUsers();
      successMessage = "Rola bola úspešne zmenená.";
    } catch (err) {
      errorMessage = err.message;
    }
  }

  async function deleteUser(id) {
    if (!confirm("Naozaj chceš odstrániť tohto používateľa?")) return;

    errorMessage = "";
    successMessage = "";
    try {
      const res = await fetch(`http://localhost:8000/api/users/${id}`, {
        method: "DELETE",
        headers: { Authorization: `Bearer ${localStorage.getItem("access_token")}` },
        credentials: "include"
      });

      if (!res.ok) throw new Error("Nepodarilo sa odstrániť používateľa.");
      fetchUsers();
      successMessage = "Používateľ bol úspešne odstránený.";
    } catch (err) {
      errorMessage = err.message;
    }
  }

  onMount(fetchUsers);
</script>

<div class="container">
  <h2>Správa používateľov</h2>

  {#if errorMessage}
    <p class="error">{errorMessage}</p>
  {/if}

  {#if successMessage}
    <p class="success">{successMessage}</p>
  {/if}

  <!-- Adminovia -->
  <section class="user-section">
    <h3> Administrátori</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Meno</th>
          <th>Email</th>
          <th>Akcie</th>
        </tr>
      </thead>
      <tbody>
        {#each admins as user}
          <tr>
            <td>{user.id}</td>
            <td>{user.name}</td>
            <td>{user.email}</td>
            <td>
              <button class="toggle" on:click={() => toggleRole(user.id)}>⬇️ Odobrať admina</button>
              <button class="delete" on:click={() => deleteUser(user.id)}>🗑️ Odstrániť</button>
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </section>

  <!-- Používatelia -->
  <section class="user-section">
    <h3> Používatelia</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Meno</th>
          <th>Email</th>
          <th>Akcie</th>
        </tr>
      </thead>
      <tbody>
        {#each users as user}
          <tr>
            <td>{user.id}</td>
            <td>{user.name}</td>
            <td>{user.email}</td>
            <td>
              <button class="toggle" on:click={() => toggleRole(user.id)}>⬆️ Urobiť adminom</button>
              <button class="delete" on:click={() => deleteUser(user.id)}>🗑️ Odstrániť</button>
            </td>
          </tr>
        {/each}
      </tbody>
    </table>
  </section>
</div>

<style>
  .container {
  width: 100vw; /* Plná šírka obrazovky */
  max-width: 100%;
  margin: 40px auto;
  padding: 20px;
  background: #fff;
}

.user-section {
  background: #f9f9f9;
  padding: 20px;
  border-radius: 6px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  width: 95%; /* Takmer celá šírka, ale s medzerou od okrajov */
  margin: 20px auto;
}

h3 {
  color: #444;
  text-align: left;
  margin-bottom: 10px;
  font-size: 1.6rem;
  display: flex;
  align-items: center;
  gap: 8px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
  background: #fff;
  border-radius: 6px;
  overflow: hidden;
}

th, td {
  padding: 14px;
  border-bottom: 1px solid #ddd;
  text-align: left;
}

th {
  background: #f4f4f4;
  font-weight: bold;
}

button {
  padding: 8px 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  margin-right: 5px;
  transition: all 0.3s ease;
}

.toggle {
  background: #3498db;
  color: white;
}

.toggle:hover {
  background: #2980b9;
}

.delete {
  background: red;
  color: white;
}

.delete:hover {
  background: darkred;
}

@media (max-width: 768px) {
  table {
    font-size: 0.9rem;
  }

  th, td {
    padding: 10px;
  }

  button {
    padding: 6px 10px;
    font-size: 0.8rem;
  }
}

</style>


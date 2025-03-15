<script>
  import { onMount, createEventDispatcher } from "svelte";
  export let goTo; 

  const dispatch = createEventDispatcher();

  let town_name = "";
  let description = "";
  let logoFile = null;      
  let galleryFiles = [];    
   let existingImages = []; 
  let errorMessage = "";
  let isLoading = true;

  onMount(async () => {
    try {
      const res = await fetch("http://localhost:8000/api/maininfo");
      if (!res.ok) throw new Error("Nepodarilo sa načítať údaje.");
      const data = await res.json();
      
      town_name = data.town_name;
      description = data.description;
      existingImages = data.gallery?.images || [];
    } catch (err) {
      errorMessage = err.message;
    } finally {
      isLoading = false;
    }
  });

  function handleLogoFileChange(e) {
    logoFile = e.target.files[0];
  }

  function handleGalleryFilesChange(e) {
    galleryFiles = Array.from(e.target.files);
  }

  async function submitForm() {
    try {
      errorMessage = "";
      const formData = new FormData();
      formData.append("town_name", town_name);
      formData.append("description", description);

      if (logoFile) {
        formData.append("logo_file", logoFile);
      }
      
      if (galleryFiles.length > 0) {
        for (let file of galleryFiles) {
          formData.append("gallery_files[]", file);
        }
      }

      const res = await fetch("http://localhost:8000/api/maininfo", {
        method: "POST",
        body: formData
      });
      if (!res.ok) throw new Error("Nepodarilo sa uložiť údaje.");
      const updatedData = await res.json();
      
      goTo("maininfo");
      window.location.reload();

    } catch (err) {
      errorMessage = err.message;
    }
  }

  async function deleteExistingImage(imageId) {
    if (!confirm("Naozaj chcete vymazať tento obrázok?")) return;
    try {
      const res = await fetch(`http://localhost:8000/api/images/${imageId}`, {
        method: "DELETE"
      });
      if (!res.ok) {
        const errorData = await res.json();
        throw new Error(errorData.message || "Chyba pri mazaní obrázka.");
      }
      existingImages = existingImages.filter(img => img.id !== imageId);
    } catch (err) {
      errorMessage = err.message;
    }
  }
</script>

{#if isLoading}
  <p>Načítavam údaje...</p>
{:else}
  {#if errorMessage}
    <p style="color: red;">{errorMessage}</p>
  {/if}
  <form on:submit|preventDefault={submitForm} enctype="multipart/form-data">
    <div>
      <label for="town_name">Mesto / Názov:</label>
      <input id="town_name" type="text" bind:value={town_name} required />
    </div>
    <div>
      <label for="description">Popis:</label>
      <textarea id="description" bind:value={description}></textarea>
    </div>
    {#if existingImages.length > 0}
      <div class="existing-gallery">
        {#each existingImages as img}
          <div class="image-wrapper">
            <img 
              src={`http://localhost:8000/storage/${img.path}`}
              alt="Obrázok" 
              class="gallery-img"
            />
            <button type="button" class="delete-img-btn" on:click={() => deleteExistingImage(img.id)}>X</button>
          </div>
        {/each}
      </div>
    {/if}

    <div>
      <label for="logo_file">Logo súbor:</label>
      <input id="logo_file" type="file" accept="image/*" on:change={handleLogoFileChange} />
    </div>
    <div>
      <label for="gallery_files">Galéria obrázkov:</label>
      <input id="gallery_files" type="file" accept="image/*" multiple on:change={handleGalleryFilesChange} />
    </div>
    <button type="submit">Uložiť zmeny</button>
  </form>
{/if}

<style>
  form {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: sans-serif;
  }
  label {
    display: block;
    margin-bottom: 4px;
    font-weight: bold;
  }
  input, textarea {
    width: 100%;
    margin-bottom: 12px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  button {
    padding: 10px 20px;
    border: none;
    background-color: #28a745;
    color: #fff;
    border-radius: 4px;
    cursor: pointer;
  }
  button:hover {
    background-color: #218838;
  }

  .existing-gallery {
    display: flex;
    gap: 10px;
    margin: 10px 0;
    flex-wrap: wrap;
  }
  .image-wrapper {
    position: relative;
  }
  .gallery-img {
    width: 120px;
    height: 90px;
    object-fit: cover;
    border-radius: 6px;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
  }
  .delete-img-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    background: rgba(220,53,69,0.8);
    color: #fff;
    border: none;
    border-radius: 4px;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>

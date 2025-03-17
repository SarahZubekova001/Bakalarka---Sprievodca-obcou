<script>
  import { onMount } from "svelte";

  export let goTo;      
  export let userRole;  

  let events = [];
  let errorMessage = "";
  let isLoading = true;

  let currentYear = 2025;
  let currentMonth = 2; 

 
  let eventsByDate = {};

  onMount(fetchEvents);

  async function fetchEvents() {
    try {
      const res = await fetch("http://localhost:8000/api/events");
      if (!res.ok) throw new Error("Nepodarilo sa načítať udalosti.");
      events = await res.json();

      eventsByDate = {};
      for (let e of events) {
        const dateKey = e.date; // e.g. "2025-03-28"
        if (!eventsByDate[dateKey]) {
          eventsByDate[dateKey] = [];
        }
        eventsByDate[dateKey].push(e);
      }

    } catch (err) {
      errorMessage = err.message;
      console.error(err);
    } finally {
      isLoading = false;
    }
  }

  function addEvent() {
    goTo("add-event");
  }
  function editEvent(id) {
    goTo("edit-event", id);
  }
  async function deleteEvent(id) {
    if (!confirm("Naozaj chcete vymazať toto podujatie?")) return;
    try {
      const resp = await fetch(`http://localhost:8000/api/events/${id}`, {
        method: "DELETE"
      });
      if (!resp.ok) throw new Error("Chyba pri mazaní eventu.");
      events = events.filter(e => e.id !== id);
      fetchEvents();
    } catch (err) {
      alert(err.message);
    }
  }

  function getDaysInMonth(year, monthIndex) {
    return new Date(year, monthIndex+1, 0).getDate();
  }

  let days = [];
  $: days = buildCalendarDays();

  function buildCalendarDays() {
    let totalDays = getDaysInMonth(currentYear, currentMonth);
    let arr = [];
    for (let day = 1; day <= totalDays; day++) {
      arr.push(day);
    }
    return arr;
  }

  const monthNames = [
    "Január", "Február", "Marec", "Apríl", "Máj", "Jún",
    "Júl", "August", "September", "Október", "November", "December"
  ];

  function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
  }
  function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
  }
</script>

{#if isLoading}
  <p>Načítavam udalosti...</p>
{:else if errorMessage}
  <p style="color: red;">{errorMessage}</p>
{:else}
  <div class="events-container">
    <div class="calendar-section">
      <div class="calendar-header">
        <button on:click={prevMonth}>❮</button>
        <span>{monthNames[currentMonth]} {currentYear}</span>
        <button on:click={nextMonth}>❯</button>
      </div>

      <div class="calendar-grid">
        {#each days as day}
          <div 
            class="calendar-cell {eventsByDate[`${currentYear}-${(currentMonth+1).toString().padStart(2,'0')}-${day.toString().padStart(2,'0')}`] ? 'has-event' : ''}"
          >
            {day}
          </div>
        {/each}
      </div>
    </div>

    <div class="events-list">
      <h2>Kalendár podujatí</h2>
      {#if events.length === 0}
        <p>Žiadne udalosti</p>
      {:else}
        {#each events as ev}
          <div class="event-card">
            <h3>{ev.name}</h3>
            <p><strong>Dátum:</strong> {ev.date} 
              {#if ev.time}
                o {ev.time}
              {/if}
            </p>
            {#if ev.address}
              <p>
                <strong>Miesto:</strong> 
                {ev.address.street} {ev.address.descriptive_number}, {ev.address.postal_code} {ev.address.town?.name}
              </p>
            {/if}
            <p>{ev.text}</p>
            {#if ev.image}
              <img 
                src={`http://localhost:8000/storage/${ev.image.path}`} 
                alt="Event obrázok"
                class="event-image"
              />
            {/if}
            
            {#if userRole === "admin"}
              <div class="admin-buttons">
                <button on:click={() => editEvent(ev.id)}>Upraviť</button>
                <button on:click={() => deleteEvent(ev.id)}>Vymazať</button>
              </div>
            {/if}
          </div>
        {/each}
      {/if}

      {#if userRole === "admin"}
        <button class="add-event-button" on:click={addEvent}>Pridať udalosť</button>
      {/if}
    </div>
  </div>
{/if}

<style>
  .events-container {
    display: flex;
    gap: 20px;
    margin: 2rem auto;
    max-width: 1200px;
    font-family: sans-serif;
  }

  .calendar-section {
    width: 300px;
    background: #f9f9f9;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  .calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
  }
  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
  }
  .calendar-cell {
    background: #fff;
    border-radius: 4px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  .calendar-cell.has-event {
    background: #c2e7d3;
    font-weight: bold;
    color: #333;
  }

  .events-list {
    flex: 1;
  }
  .events-list h2 {
    margin-bottom: 1rem;
  }
  .event-card {
    background: #fff;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  }
  .event-card h3 {
    margin-top: 0;
    margin-bottom: 0.5rem;
  }
  .event-image {
    max-width: 100%;
    margin-top: 0.5rem;
    border-radius: 4px;
  }
  .admin-buttons {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
  }
  .admin-buttons button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    color: #fff;
    cursor: pointer;
  }
  .admin-buttons button:first-of-type {
    background: #007bff;
  }
  .admin-buttons button:last-of-type {
    background: #dc3545;
  }

  .add-event-button {
    margin-top: 1rem;
    padding: 0.7rem 1.2rem;
    border: none;
    border-radius: 4px;
    background: #444;
    color: #fff;
    cursor: pointer;
  }
  .add-event-button:hover {
    background: #333;
  }
</style>

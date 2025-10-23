document.addEventListener('DOMContentLoaded', () => {
    const modalOverlay = document.getElementById('scheduleModalOverlay');
    const editBtn = document.getElementById('editScheduleBtn');
    const closeBtn = document.getElementById('closeBtn');
    const addEntryBtn = document.getElementById('addEntryButton');

    // --- Modal öffnen ---
    editBtn.addEventListener('click', () => {
        modalOverlay.style.display = 'flex';
        loadSchedule();
    });

    // --- Modal schließen ---
    closeBtn.addEventListener('click', () => {
        modalOverlay.style.display = 'none';
    });

    // --- Modal schließen bei Klick außerhalb ---
    window.addEventListener('click', (e) => {
        if(e.target === modalOverlay) modalOverlay.style.display = 'none';
    });

    // --- Neues Fach hinzufügen ---
    addEntryBtn.addEventListener('click', addNewEntry);
});

// --- Tabelle laden ---
function loadSchedule() {
    fetch('../plan/load_schedule.php')
        .then(res => res.json())
        .then(data => {
            let html = '<table><tr><th>Fach</th><th>Dozent</th><th>Tag</th><th>Zeit</th><th>Raum</th><th>Aktionen</th></tr>';
            if(data.entries && data.entries.length > 0) {
                data.entries.forEach(e => {
                    html += `<tr data-id="${e.schedule_id}">
                        <td>${e.name}</td>
                        <td>${e.dozent}</td>
                        <td>${e.tag}</td>
                        <td>${e.zeitslot}</td>
                        <td><input type="text" value="${e.raum}" onchange="updateRoom(${e.schedule_id}, this.value)"></td>
                        <td><button onclick="deleteEntry(${e.schedule_id})">Löschen</button></td>
                    </tr>`;
                });
            } else {
                html += '<tr><td colspan="6">Keine Einträge gefunden.</td></tr>';
            }
            html += '</table>';
            document.getElementById('schedule-modal-container').innerHTML = html;
        })
        .catch(err => console.error('Fehler beim Laden des Stundenplans:', err));
}

// --- Neues Fach hinzufügen ---
function addNewEntry() {
    const fa_id = document.getElementById('newFach').value;
    const tag = document.getElementById('newTag').value;
    const zeitslot = document.getElementById('newZeitslot').value;
    const raum = document.getElementById('newRaum').value;

    if(!fa_id || !tag || !zeitslot) {
        alert("Bitte wählen Sie Fach, Tag und Zeitslot aus.");
        return;
    }

    const formData = new FormData();
    formData.append('faecher_id', fa_id);
    formData.append('tag', tag);
    formData.append('zeitslot', zeitslot);
    formData.append('raum', raum);

    fetch('../plan/add_schedule.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                loadSchedule();
                document.getElementById('newRaum').value = '';
            } else {
                alert("Fehler: " + data.message);
            }
        })
        .catch(err => {
            console.error('Fehler beim Hinzufügen:', err);
            alert("Serverfehler. Prüfen Sie die Konsole.");
        });
}

// --- Raum ändern ---
function updateRoom(id, value) {
    const formData = new FormData();
    formData.append('schedule_id', id);
    formData.append('raum', value);

    fetch('../php/actions/update_room.php', { method:'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) loadSchedule();
            else alert("Fehler beim Aktualisieren: " + data.message);
        })
        .catch(err => {
            console.error('Fehler beim Aktualisieren des Raums:', err);
            alert("Serverfehler. Prüfen Sie die Konsole.");
        });
}

// --- Eintrag löschen ---
function deleteEntry(id) {
    if(!confirm("Eintrag wirklich löschen?")) return;

    const formData = new FormData();
    formData.append('schedule_id', id);

    fetch('../php/actions/delete_schedule_entry.php', { method:'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) loadSchedule();
            else alert("Fehler beim Löschen: " + data.message);
        })
        .catch(err => {
            console.error('Fehler beim Löschen des Eintrags:', err);
            alert("Serverfehler. Prüfen Sie die Konsole.");
        });
}

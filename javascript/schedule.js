// Wait for the DOM to be fully loaded before running scripts
document.addEventListener('DOMContentLoaded', function () {

    // --- ELEMENT REFERENCES ---
    const editScheduleBtn = document.getElementById('editScheduleBtn');
    const modalOverlay_schedule = document.getElementById('scheduleModalOverlay');
    const closeBtn = document.getElementById('closeBtn');
    const saveBtn = document.getElementById('saveBtn');
    const newFachSelect = document.getElementById('newFach');
    const addEntryButton = document.getElementById('addEntryButton');
    const scheduleModalContent = document.getElementById('schedule-modal-container');


    function openEditModal() {
        modalOverlay_schedule.classList.add('active');
        loadSchedule(); // Load or refresh the schedule table when opening
    }

    function closeEditModal() {
        modalOverlay_schedule.classList.remove('active');
    }

    // --- EVENT LISTENERS FOR MODAL ---

    editScheduleBtn.addEventListener('click', openEditModal);
    closeBtn.addEventListener('click', closeEditModal);

    // Close modal if user clicks outside the modal content
    window.addEventListener('click', function (event) {
        if (event.target === modalOverlay_schedule) {
            closeEditModal();
        }
    });


    function addNewEntry() {
        const fa_id = newFachSelect.value;
        const tag = document.getElementById('newTag').value;
        const zeitslot = document.getElementById('newZeitslot').value;
        const raum = document.getElementById('newRaum').value;

        if (!fa_id || !tag || !zeitslot) {
            alert("Bitte wählen Sie Fach, Tag und Zeitslot aus.");
            return;
        }

        // Prepare data to be sent
        const formData = new FormData();
        formData.append('fa_id', fa_id);
        formData.append('tag', tag);
        formData.append('zeitslot', zeitslot);
        formData.append('raum', raum);

        // Send data to server using Fetch API
        fetch('../php/actions/add_schedule_entry.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Eintrag erfolgreich hinzugefügt!");
                loadSchedule(); // Reload the schedule to show the new entry
                // Reset form fields for next entry
                document.getElementById('newRaum').value = '';
            } else {
                alert("Fehler beim Hinzufügen: " + (data.message || 'Unbekannter Fehler.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Ein Fehler ist aufgetreten. Bitte prüfen Sie die Konsole.");
        });
    }

    function loadSchedule() {
        fetch('../php/actions/get_schedule.php')
            .then(response => response.json())
            .then(data => {
                let tableHTML = `
                    <table>
                        <thead>
                            <tr>
                                <th>Fach</th>
                                <th>Dozent</th>
                                <th>Tag</th>
                                <th>Zeit</th>
                                <th>Raum</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                if (data.entries && data.entries.length > 0) {
                    data.entries.forEach(entry => {
                        tableHTML += `
                            <tr data-id="${entry.schedule_id}">
                                <td>${entry.name}</td>
                                <td>${entry.dozent}</td>
                                <td>${entry.tag}</td>
                                <td>${entry.zeitslot}</td>
                                <td><input type="text" value="${entry.raum}" onchange="updateRoom(${entry.schedule_id}, this.value)"></td>
                                <td><button onclick="deleteEntry(${entry.schedule_id})">Löschen</button></td>
                            </tr>
                        `;
                    });
                } else {
                     tableHTML += `
                         <tr>
                             <td colspan="6" style="text-align: center;">Keine Einträge gefunden.</td>
                         </tr>
                     `;
                }

                tableHTML += `
                        </tbody>
                    </table>
                `;

                scheduleModalContent.innerHTML = tableHTML;
            })
            .catch(error => {
                console.error('Error loading schedule:', error);
                scheduleModalContent.innerHTML = "<p>Fehler beim Laden des Stundenplans.</p>";
            });
    }


    window.updateRoom = function(id, newRoom) {
        const formData = new FormData();
        formData.append('schedule_id', id);
        formData.append('raum', newRoom);

        fetch('../php/actions/update_room.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optional: Show a temporary success message or log it
                console.log("Raum erfolgreich aktualisiert.");
            } else {
                alert("Fehler beim Aktualisieren des Raums: " + data.message);
                loadSchedule(); // Reload to revert changes on failure
            }
        })
        .catch(error => {
            console.error('Error updating room:', error);
            alert("Ein Fehler ist aufgetreten. Bitte prüfen Sie die Konsole.");
        });
    };
    
    window.deleteEntry = function(id) {
        if (confirm("Sind Sie sicher, dass Sie diesen Eintrag löschen möchten?")) {
            const formData = new FormData();
            formData.append('schedule_id', id);

            fetch('../php/actions/delete_schedule_entry.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Eintrag erfolgreich gelöscht!");
                    loadSchedule(); // Reload the schedule table
                } else {
                     alert("Fehler beim Löschen: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting entry:', error);
                 alert("Ein Fehler ist aufgetreten. Bitte prüfen Sie die Konsole.");
            });
        }
    };

    // Add event listener for the manual add button
    if (addEntryButton) {
         addEntryButton.addEventListener('click', addNewEntry);
    }

});
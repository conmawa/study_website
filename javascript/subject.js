// subject.js
document.addEventListener('DOMContentLoaded', () => {

    // --- ELEMENTE ---
    const modal = document.getElementById('subjectModalOverlay');
    const title = document.getElementById('modalTitle');
    const form = modal.querySelector('form');
    const actionField = document.getElementById('action');
    const idField = document.getElementById('faecher_id');
    const fachInput = document.getElementById('name');
    const raumInput = document.getElementById('raum');
    const dozentInput = document.getElementById('dozent');

    // --- MODAL ÖFFNEN ---
    window.openModal = function (mode, data = null) {
        modal.style.display = 'flex';
        modal.classList.add('active');

        if (mode === 'edit' && data) {
            title.textContent = 'Fach bearbeiten';
            actionField.value = 'edit';
            idField.value = data.faecher_id;
            fachInput.value = data.name;
            raumInput.value = data.raum;
            dozentInput.value = data.dozent;
        } else {
            title.textContent = 'Neues Fach hinzufügen';
            actionField.value = 'add';
            idField.value = '';
            fachInput.value = '';
            raumInput.value = '';
            dozentInput.value = '';
        }
    };

    // --- MODAL SCHLIESSEN ---
    window.closeModal = function () {
        modal.style.display = 'none';
        modal.classList.remove('active');
    };

    // --- MODAL KLICK AUF HINTERGRUND ---
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // --- FORMULAR SENDEN ---
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Grundvalidierung
        if (!fachInput.value.trim() || !raumInput.value.trim() || !dozentInput.value.trim()) {
            alert('Bitte füllen Sie alle Felder aus.');
            return;
        }

        const formData = new FormData(form);

        try {
            // Button deaktivieren während des Sendens
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Speichern...';

            // Anfrage senden (an dieselbe PHP-Seite)
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();

            // Versuch, JSON zu parsen – falls möglich
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert(data.message || 'Fach gespeichert.');
                    location.reload();
                } else {
                    alert('Fehler: ' + (data.message || 'Unbekannter Serverfehler.'));
                }
            } catch {
                // Fallback: Textantwort anzeigen
                console.log('Serverantwort:', text);
                location.reload(); // meist Reload nötig, da PHP direkt HTML rendert
            }

        } catch (error) {
            console.error('Fehler beim Speichern:', error);
            alert('Ein schwerwiegender Fehler ist aufgetreten. Bitte prüfen Sie die Konsole.');
        } finally {
            // Button wieder aktivieren
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Speichern';
        }
    });
});

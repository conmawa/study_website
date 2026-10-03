
document.addEventListener('DOMContentLoaded', () => {

    // --- SCROLL-POSITION WIEDERHERSTELLEN ---
    const scrollPos = sessionStorage.getItem('subjectScrollPos');
    if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos));
    }

    window.addEventListener('scroll', () => {
        sessionStorage.setItem('subjectScrollPos', window.scrollY);
    });

    // --- ELEMENTE ---
    const modal = document.getElementById('subjectModalOverlay');
    const title = document.getElementById('modalTitle');
    const form = modal.querySelector('form');
    const actionField = document.getElementById('action');
    const idField = document.getElementById('faecher_id');
    const fachInput = document.getElementById('name');
    const dozentInput = document.getElementById('dozent');
    const semesterInput = document.getElementById('semester');


    // --- MODAL ÖFFNEN ---
    window.openModal = function (mode, data = null) {
        modal.style.display = 'flex';
        modal.classList.add('active');

        if (mode === 'edit' && data) {
            title.textContent = 'Fach bearbeiten';
            actionField.value = 'edit';
            idField.value = data.faecher_id;
            fachInput.value = data.name;
            dozentInput.value = data.dozent;
            // KORREKTUR: Nutzt jetzt den Wert aus dem Daten-Objekt statt der Element-Referenz
            semesterInput.value = data.semester; 
        } else {
            title.textContent = 'Neues Fach hinzufügen';
            actionField.value = 'add';
            idField.value = '';
            fachInput.value = '';
            dozentInput.value = '';
            semesterInput.value = '';
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
        if (!fachInput.value.trim() || !dozentInput.value.trim() || !semesterInput.value.trim()) {
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

            try {
                const data = JSON.parse(text);
                if (data.success) {
                    // Kein alert() nötig, wenn direkt reloaded wird, 
                    // um den Flow nicht zu unterbrechen.
                    location.reload();
                } else {
                    alert('Fehler: ' + (data.message || 'Unbekannter Serverfehler.'));
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Speichern';
                }
            } catch {
                // Falls PHP direkt HTML zurückgibt (Fallback)
                console.log('Serverantwort:', text);
                location.reload(); 
            }

        } catch (error) {
            console.error('Fehler beim Speichern:', error);
            alert('Ein schwerwiegender Fehler ist aufgetreten.');
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Speichern';
        }
    });
});
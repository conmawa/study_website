document.addEventListener('DOMContentLoaded', () => {
    const scrollPos = sessionStorage.getItem('subjectScrollPos');
    if (scrollPos) window.scrollTo(0, parseInt(scrollPos));
    window.addEventListener('scroll', () => sessionStorage.setItem('subjectScrollPos', window.scrollY));

    const modal = document.getElementById('subjectModalOverlay');
    const title = document.getElementById('modalTitle');
    const form = modal?.querySelector('form');
    const actionField = document.getElementById('action');
    const idField = document.getElementById('subjectId');
    const subjectInput = document.getElementById('subjectName');
    const lecturerInput = document.getElementById('lecturer');
    const semesterInput = document.getElementById('semester');

    window.openModal = (mode, data = null) => {
        if (!modal) return;
        modal.style.display = 'flex';
        modal.classList.add('active');

        if (mode === 'edit' && data) {
            title.textContent = 'Fach bearbeiten';
            actionField.value = 'edit';
            idField.value = data.faecher_id;
            subjectInput.value = data.name;
            lecturerInput.value = data.dozent;
            semesterInput.value = data.semester;
        } else {
            title.textContent = 'Neues Fach hinzufügen';
            actionField.value = 'add';
            idField.value = '';
            subjectInput.value = '';
            lecturerInput.value = '';
            semesterInput.value = '';
        }
    };

    window.closeModal = () => {
        if (!modal) return;
        modal.style.display = 'none';
        modal.classList.remove('active');
    };

    modal?.addEventListener('click', (e) => {
        if (e.target === modal) window.closeModal();
    });

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!subjectInput.value.trim() || !lecturerInput.value.trim() || !semesterInput.value.trim()) {
            alert('Bitte füllen Sie alle Felder aus.');
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        try {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Speichern...';
            }

            const response = await fetch('', { method: 'POST', body: formData });
            const text = await response.text();

            try {
                const data = JSON.parse(text);
                if (data.success) {
                    location.reload();
                } else {
                    alert('Fehler: ' + (data.message || 'Unbekannter Serverfehler.'));
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Speichern';
                    }
                }
            } catch {
                location.reload();
            }
        } catch (error) {
            console.error('Error saving:', error);
            alert('Ein schwerwiegender Fehler ist aufgetreten.');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Speichern';
            }
        }
    });
});

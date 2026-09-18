document.addEventListener('DOMContentLoaded', () => {
    const scrollPos = sessionStorage.getItem('notenScrollPos');
    if (scrollPos) {
        window.scrollTo(0, parseInt(scrollPos));
    }

    window.addEventListener('scroll', () => {
        sessionStorage.setItem('notenScrollPos', window.scrollY);
    });

    const modal = document.getElementById('markModalOverlay');
    const form = modal.querySelector('form');
    const semesterSelect = document.getElementById('semester');
    const fachSelect = document.getElementById('fach');
    const wertInput = document.getElementById('wert');

    const subjectsData = window.subjectsData || {};

    function updateFachDropdown() {
        const selectedSemester = semesterSelect.value;
        const subjects = subjectsData[selectedSemester] || [];
        fachSelect.innerHTML = '';

        subjects.forEach(function(subject) {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            fachSelect.appendChild(option);
        });
    }

    if (semesterSelect) {
        semesterSelect.addEventListener('change', updateFachDropdown);
        updateFachDropdown();
    }

    // UNIQUE NAME FOR MARKS MODAL
    window.openMarkModal = function () {
        if (modal) {
            modal.style.display = 'flex';
            wertInput.value = '';
        }
    };

    // UNIQUE NAME FOR MARKS MODAL
    window.closeMarkModal = function () {
        if (modal) {
            modal.style.display = 'none';
        }
    };

    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) window.closeMarkModal();
        });
    }

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!fachSelect.value || !wertInput.value.trim() || !semesterSelect.value) {
                alert('Bitte füllen Sie alle Felder aus.');
                return;
            }

            const formData = new FormData(form);
            const submitBtn = form.querySelector('input[type="submit"]');

            try {
                if (submitBtn) submitBtn.disabled = true;

                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });

                const text = await response.text();

                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Fehler: ' + (data.message || 'Unbekannter Serverfehler.'));
                        if (submitBtn) submitBtn.disabled = false;
                    }
                } catch {
                    location.reload();
                }

            } catch (error) {
                console.error('Fehler beim Speichern:', error);
                alert('Ein schwerwiegender Fehler ist aufgetreten.');
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }
});
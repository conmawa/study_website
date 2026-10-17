document.addEventListener('DOMContentLoaded', () => {
    const scrollPos = sessionStorage.getItem('marksScrollPos');
    if (scrollPos) window.scrollTo(0, parseInt(scrollPos));
    window.addEventListener('scroll', () => sessionStorage.setItem('marksScrollPos', window.scrollY));

    const modal = document.getElementById('markModalOverlay');
    const form = document.getElementById('add-mark-form');
    const semesterSelect = document.getElementById('markSemester');
    const subjectSelect = document.getElementById('markSubject');
    const gradeInput = document.getElementById('grade');

    const subjectsData = window.subjectsData || {};

    function updateSubjectDropdown() {
        if (!semesterSelect || !subjectSelect) return;
        const selectedSemester = semesterSelect.value;
        const subjects = subjectsData[selectedSemester] || [];
        subjectSelect.innerHTML = '';

        subjects.forEach(subject => {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            subjectSelect.appendChild(option);
        });
    }

    semesterSelect?.addEventListener('change', updateSubjectDropdown);
    if (semesterSelect) updateSubjectDropdown();

    window.openMarkModal = () => {
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active'); // Hinzugefügt
            if (gradeInput) gradeInput.value = '';
        }
    };

    window.closeMarkModal = () => {
        if (modal) {
            modal.style.display = 'none';
            modal.classList.remove('active'); // Hinzugefügt
        }
    };


    modal?.addEventListener('click', (e) => {
        if (e.target === modal) window.closeMarkModal();
    });

    form?.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!subjectSelect.value || !gradeInput.value.trim() || !semesterSelect.value) {
            alert('Bitte füllen Sie alle Felder aus.');
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('input[type="submit"]');

        try {
            if (submitBtn) submitBtn.disabled = true;
            const response = await fetch('', { method: 'POST', body: formData });
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
            console.error('Error saving:', error);
            alert('Ein schwerwiegender Fehler ist aufgetreten.');
            if (submitBtn) submitBtn.disabled = false;
        }
    });
});

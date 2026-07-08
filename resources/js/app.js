import './bootstrap';
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    // Notifications (toasts) : affichage automatique puis disparition.
    document.querySelectorAll('.toast').forEach((el) => {
        new bootstrap.Toast(el, { delay: 5000 }).show();
    });

    // Modale de confirmation de suppression générique.
    // Un bouton .js-confirm-delete porte data-action (URL du DELETE) et data-label (texte affiché).
    const confirmModalEl = document.getElementById('confirmDeleteModal');
    if (confirmModalEl) {
        const confirmModal = new bootstrap.Modal(confirmModalEl);
        const confirmForm = confirmModalEl.querySelector('form');
        const confirmLabel = confirmModalEl.querySelector('.js-confirm-label');

        document.querySelectorAll('.js-confirm-delete').forEach((btn) => {
            btn.addEventListener('click', () => {
                confirmForm.setAttribute('action', btn.dataset.action);
                confirmLabel.textContent = btn.dataset.label || 'cet élément';
                confirmModal.show();
            });
        });
    }

    // Aperçu de l'image sélectionnée dans les formulaires d'œuvres.
    document.querySelectorAll('input[type="file"][data-preview]').forEach((input) => {
        input.addEventListener('change', () => {
            const target = document.getElementById(input.dataset.preview);
            if (target && input.files && input.files[0]) {
                target.src = URL.createObjectURL(input.files[0]);
                target.classList.remove('d-none');
            }
        });
    });
});

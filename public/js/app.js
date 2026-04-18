function toggleActionMenu(btn)
{
    const dropdown = btn.nextElementSibling;
    const isOpen   = dropdown.classList.contains('open');

    document.querySelectorAll('.action-menu__dropdown.open')
        .forEach(d => d.classList.remove('open'));

    if (!isOpen) dropdown.classList.add('open');
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.action-menu')) {
        document.querySelectorAll('.action-menu__dropdown.open')
            .forEach(d => d.classList.remove('open'));
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal').forEach(m => {
            m.style.display = 'none';
        });

        document.body.style.overflow = '';
    }
});

// Modal
function openModal(id) 
{
    const modal = document.getElementById(id);
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}

function closeModal(id) 
{
    const modal = document.getElementById(id);
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
}

document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-modal]');

    if (btn) {
        const modalId  = btn.dataset.modal;
        const fetchUrl = btn.dataset.fetchUrl;
        const content  = document.querySelector(`#${modalId} .modal-content`);

        content.innerHTML = '<p class="modal-loading">Carregando...</p>';
        openModal(modalId);

        fetch(fetchUrl)
            .then(res => {
                if (!res.ok) throw new Error('Erro ao carregar conteúdo');
                return res.text();
            })
            .then(html => {
                content.innerHTML = html;
                const closeBtn = content.querySelector('[data-close-modal]')
                if (closeBtn) closeBtn.dataset.closeModal = modalId;
            })
            .catch(() => content.innerHTML =
                '<p class="modal-error">Erro ao carregar.</p>'
            );
    }

    const overlay = e.target.closest('[data-close-modal]');
    if (overlay) closeModal(overlay.dataset.closeModal);
});
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

        if (!fetchUrl) return;

        const content  = document.querySelector(`#${modalId} .modal-content`);

        content.innerHTML = '<p class="modal-loading">Carregando...</p>';
        openModal(modalId);

        fetch(fetchUrl)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.text();
            })
            .then(html => {
                content.innerHTML = html;
                const closeBtn = content.querySelector('[data-close-modal]')
                if (closeBtn) closeBtn.dataset.closeModal = modalId;
            })
            .catch((err) => {
                console.error(`modal-trigger: erro ao carregar "${fetchUrl}"`, err);
                content.innerHTML = `
                    <div class="modal-error">
                        <p>Não foi possível carregar o conteúdo.</p>
                        <button onclick="closeModal('${modalId}')">Fechar</button>
                    </div>
                `;
            });
    }

    const overlay = e.target.closest('[data-close-modal]');
    if (overlay) closeModal(overlay.dataset.closeModal);
});
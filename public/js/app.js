function toggleActionMenu(btn) {
    const dropdown = btn.nextElementSibling;
    const isOpen = dropdown.classList.contains('open');
    document.querySelectorAll('.action-menu__dropdown.open').forEach(d => d.classList.remove('open'));
    if (!isOpen) dropdown.classList.add('open');
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.action-menu')) {
        document.querySelectorAll('.action-menu__dropdown.open').forEach(d => d.classList.remove('open'));
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
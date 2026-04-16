const modal = document.getElementById("criarTurmaModal");
const modalContent = document.querySelector(".modal-content");

document.getElementById("criarTurmaBtn").onclick = () => {
    fetch('/turmas/create')
        .then(res => res.text())
        .then(html => {
            modalContent.innerHTML = html;
            modal.style.display = "block";
        });
}

window.onclick = (e) => {
    if (e.target === modal) modal.style.display = "none";
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') modal.style.display = "none";
});
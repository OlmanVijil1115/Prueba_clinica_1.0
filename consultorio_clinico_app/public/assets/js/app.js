// Pequeñas animaciones y UX
document.addEventListener('click', (e) => {
  const el = e.target.closest('.btn');
  if (!el) return;
  el.style.transform = 'translateY(1px) scale(0.99)';
  setTimeout(() => { el.style.transform = ''; }, 120);
});
// Ocultar toasts después de unos segundos
window.addEventListener('load', () => {
  document.querySelectorAll('.toast').forEach(t => setTimeout(() => t.classList.remove('show'), 3000));
});

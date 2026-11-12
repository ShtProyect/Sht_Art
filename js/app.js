const root = document.documentElement;
const themeButton = document.querySelector('.theme-toggle');
const savedTheme = localStorage.getItem('sht-theme');


if (savedTheme) { root.dataset.theme = savedTheme; updateThemeButton(); }
function updateThemeButton() { const light = root.dataset.theme === 'light'; themeButton?.setAttribute('aria-label', light ? 'Cambiar a tema oscuro' : 'Cambiar a tema claro'); if (themeButton) { themeButton.querySelector('.theme-icon').textContent = light ? '☾' : '☼'; themeButton.querySelector('.theme-label').textContent = light ? 'Modo oscuro' : 'Modo claro'; } }
themeButton?.addEventListener('click', () => { root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('sht-theme', root.dataset.theme); updateThemeButton(); });
const menu = document.querySelector('.menu-toggle'), nav = document.querySelector('.main-nav');
menu?.addEventListener('click', () => { const open = menu.getAttribute('aria-expanded') === 'true'; menu.setAttribute('aria-expanded', String(!open)); nav.classList.toggle('is-open', !open); });
nav?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => { nav.classList.remove('is-open'); menu?.setAttribute('aria-expanded', 'false'); }));
const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!reduced && 'IntersectionObserver' in window) { const observer = new IntersectionObserver(entries => entries.forEach(entry => { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } }), { threshold: .12 }); document.querySelectorAll('.reveal').forEach(el => observer.observe(el)); } else { document.querySelectorAll('.reveal').forEach(el => el.classList.add('is-visible')); }
const box = document.querySelector('.lightbox');
document.querySelectorAll('.work-image').forEach(button => button.addEventListener('click', () => { const img = box.querySelector('img'); img.src = button.dataset.image; img.alt = button.dataset.title; box.querySelector('h3').textContent = button.dataset.title; box.querySelector('p').textContent = button.dataset.description; box.hidden = false; document.body.classList.add('no-scroll'); box.querySelector('.lightbox-close').focus(); }));
function closeBox() { box.hidden = true; document.body.classList.remove('no-scroll'); }
box?.querySelector('.lightbox-close').addEventListener('click', closeBox); box?.addEventListener('click', e => { if (e.target === box) closeBox(); }); document.addEventListener('keydown', e => { if (e.key === 'Escape' && box && !box.hidden) closeBox(); });

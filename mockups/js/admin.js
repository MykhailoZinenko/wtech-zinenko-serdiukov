'use strict';

/* ---- Sidebar toggle (mobile) ---- */
(function () {
  const sidebar = document.querySelector('.sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  const toggle  = document.querySelector('.topbar-toggle');

  if (!sidebar) return;

  function open()  { sidebar.classList.add('open'); overlay && overlay.classList.add('open'); document.body.style.overflow = 'hidden'; }
  function close() { sidebar.classList.remove('open'); overlay && overlay.classList.remove('open'); document.body.style.overflow = ''; }

  toggle  && toggle.addEventListener('click', open);
  overlay && overlay.addEventListener('click', close);
})();

/* ---- Image remove buttons ---- */
document.querySelectorAll('.img-existing__rm').forEach(btn => {
  btn.addEventListener('click', function () {
    this.closest('.img-existing')?.remove();
  });
});

/* ---- Confirm delete ---- */
document.querySelectorAll('[data-confirm]').forEach(el => {
  el.addEventListener('click', function (e) {
    if (!confirm(this.dataset.confirm)) e.preventDefault();
  });
});

/* ---- Status badge preview (order-detail) ---- */
const statusSelect = document.getElementById('order-status-select');
const statusBadge  = document.getElementById('current-status-badge');
const statusMap = {
  pending:    ['badge-adm status-pending',  'Pending'],
  processing: ['badge-adm status-processing','Processing'],
  shipped:    ['badge-adm status-shipped',   'Shipped'],
  delivered:  ['badge-adm status-delivered', 'Delivered'],
  cancelled:  ['badge-adm status-cancelled', 'Cancelled'],
};
if (statusSelect && statusBadge) {
  statusSelect.addEventListener('change', function () {
    const [cls, label] = statusMap[this.value] || ['badge-adm badge-grey', this.value];
    statusBadge.className = cls;
    statusBadge.textContent = label;
  });
}

/* ---- Toast notification (demo) ---- */
function showToast(msg, type = 'success') {
  const t = document.createElement('div');
  t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;padding:12px 18px;
    border-radius:6px;font-family:'Cinzel',serif;font-size:.72rem;letter-spacing:.07em;
    text-transform:uppercase;font-weight:600;color:#fff;pointer-events:none;
    background:${type === 'success' ? '#2d7a45' : '#8b1a1a'};
    box-shadow:0 4px 16px rgba(0,0,0,.5);
    animation:toastIn .25s ease;`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3000);
}

/* Demo: forms show a toast on submit */
document.querySelectorAll('form[data-toast]').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    showToast(this.dataset.toast);
  });
});

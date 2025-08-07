// js/public/bootstrap-init.js

if (typeof bootstrap === 'undefined') {
  console.error("Bootstrap is not loaded.");
} else {
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  const modalList = [].slice.call(document.querySelectorAll('.modal'));
  modalList.map(el => new bootstrap.Modal(el));
}

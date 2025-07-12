// amis.js

function showSection(sectionId) {
  // Gestion active bouton etc...

  const mainContent = document.querySelector('main');

  if (sectionId === 'home' || sectionId === 'requests') {
    mainContent.innerHTML = `
      <div class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `;

    fetch('../../api/amis/amis.php?type=' + sectionId)  // <-- Chemin relatif vers mon PHP ici
      .then(response => response.text())
      .then(data => {
        mainContent.innerHTML = `
          <h3 class="fw-bold mb-4">${sectionId === 'home' ? 'My Friends' : 'Friend Requests'}</h3>
          <ul class="list-group">${data}</ul>
        `;
      })
      .catch(() => {
        mainContent.innerHTML = `<div class="alert alert-danger">An error occurred while loading data.</div>`;
      });

  } else {
    mainContent.innerHTML = `
      <div class="text-center my-5">
        <i class="bi bi-tools display-1 text-secondary mb-4"></i>
        <h3 class="fw-bold text-secondary">Feature under development.</h3>
      </div>
    `;
  }
}

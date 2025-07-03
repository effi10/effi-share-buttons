document.addEventListener('DOMContentLoaded', function () {
    // Sélectionne tous les boutons de partage
    const shareButtons = document.querySelectorAll('.esb-share-button');

    // Ajoute un écouteur d'événement sur chaque bouton
    shareButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            // Récupère l'URL stockée dans l'attribut 'data-url'
            const urlToOpen = this.dataset.url;

            // Ouvre l'URL dans un nouvel onglet de manière sécurisée
            if (urlToOpen) {
                window.open(urlToOpen, '_blank', 'noopener,noreferrer');
            }
        });
    });
});
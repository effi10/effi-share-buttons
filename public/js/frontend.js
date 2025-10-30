/**
 * Nettoie et "slugifie" un texte pour une URL en JavaScript.
 * Version améliorée qui conserve les URLs.
 * @param {string} text Le texte à nettoyer.
 * @returns {string} Le texte nettoyé.
 */
function esbSlugifyTextForUrl(text) {
    // Étape 1: Normalise et supprime les accents des lettres
    const textWithoutAccents = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    // Étape 2: Supprime les caractères non désirés, MAIS CONSERVE ceux utiles pour les URLs (:, /, ., -)
    const cleanedText = textWithoutAccents.replace(/[^a-zA-Z0-9\s:/\.-]/g, '');

    // Étape 3: Remplace les espaces (qui ne font pas partie d'une URL) par des '+'
    return cleanedText.trim().replace(/\s+/g, '+');
}

document.addEventListener('DOMContentLoaded', function () {
    const URL_MAX_LENGTH = 2028;
    const shareButtons = document.querySelectorAll('.esb-share-button');

    shareButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            
            let urlToOpen = '';
            const action = this.dataset.action;

            // CAS SPÉCIAL : ChatGPT
            if (action === 'share-content' && this.dataset.service === 'chatgpt') {
                if (typeof esb_data === 'undefined' || typeof esb_data.post_content === 'undefined') {
                    alert('Error: Post content not found.');
                    return;
                }

                const promptTemplate = this.dataset.prompt;
                const postContent = esb_data.post_content.replace(/\s+/g, ' ').trim();
                const finalText = promptTemplate.replace('{CONTENT}', postContent);
                
                // On utilise notre nouvelle fonction de nettoyage
                const slugifiedText = esbSlugifyTextForUrl(finalText);
                
                urlToOpen = 'https://chatgpt.com/?hints=search&q=' + slugifiedText;

            } else {
                // CAS NORMAL : Pour tous les autres boutons
                urlToOpen = this.dataset.url;
            }

            if (urlToOpen && urlToOpen.length > URL_MAX_LENGTH) {
                urlToOpen = urlToOpen.substring(0, URL_MAX_LENGTH);
            }

            if (urlToOpen) {
                window.open(urlToOpen, '_blank', 'noopener,noreferrer');
            }
        });
    });

});

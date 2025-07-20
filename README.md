# effi Share Buttons

* **Contributeurs :** Cédric GIRARD
* **Version :** 1.6.0
* **Requiert WordPress :** 5.8 ou supérieur
* **Testé jusqu'à :** 6.8
* **Licence :** GPLv2 ou ultérieure
* **URI de la licence :** https://www.gnu.org/licenses/gpl-2.0.html

Un plugin WordPress pour ajouter des boutons de partage hautement personnalisables, orientés vers les réseaux sociaux traditionnels et les plateformes d'intelligence artificielle.
> Largement inspiré du travail de Metehan Yesilyurt (base Github : https://github.com/metehan777/ai-llm-share-wp-plugin ; Article à lire impérativement : https://metehan.ai/blog/citemet-ai-share-buttons-growth-hack-for-llms/)

![image](https://github.com/user-attachments/assets/903c3afa-630b-44aa-bf86-a4d12b2f5704)


## Description

**effi Share Buttons** est un plugin moderne et léger qui vous permet d'intégrer facilement des boutons de partage sur votre site WordPress. Conçu pour être flexible, il vous donne un contrôle total sur l'apparence, l'emplacement et le comportement des boutons.

En plus des partages classiques sur les réseaux sociaux comme X (Twitter) et LinkedIn, ce plugin innove en proposant des partages directs vers des plateformes d'IA comme ChatGPT, Perplexity et Grok, permettant à vos lecteurs d'interagir avec votre contenu de manière inédite.



## Fonctionnalités

* **Plateformes multiples** : Partagez votre contenu vers X (Twitter), LinkedIn, WhatsApp, et des IA comme ChatGPT, Perplexity, Google AI et Grok.
* **Affichage flexible** :
    * Affichez les boutons automatiquement avant le contenu, après, ou les deux.
    * Utilisez le bloc Gutenberg "effi Share Buttons" pour placer manuellement les boutons où vous le souhaitez.
* **Contrôle total du ciblage** : Choisissez précisément les types de contenu (articles, pages, produits...) sur lesquels les boutons doivent s'afficher automatiquement.
* **Personnalisation des prompts** : Modifiez le texte de partage pour chaque service en utilisant les placeholders `{TITLE}` et `{URL}`. et même `{CONTENT}` concernant ChatGPT (la version gratuite ne permettant pas de lire une URL, vous avez la liberté de choisir !)
* **Stylisation avancée** :
    * Ajustez la taille, l'alignement et la forme (arrondie ou carrée) des boutons.
    * Définissez des couleurs de fond et de texte personnalisées pour chaque service.
    * Ajouter en option un titre entièrement personnalisable avant les boutons
* **Accessible** : Utilise des balises `<button>` sémantiques pour une meilleure accessibilité sans impacter négativement le SEO.
* **Léger et performant** : Le plugin charge les scripts et les styles de manière conditionnelle et optimisée.



## Réglages possibles

Tous les réglages se trouvent dans le menu **Réglages > effi Share Buttons** de votre administration WordPress.

### Réglages Généraux

* **Button Position** : Choisissez l'emplacement d'affichage automatique (`After Content`, `Before Content`, `Before and After Content`) ou désactivez-le pour n'utiliser que le bloc Gutenberg (`Only with the Gutenberg Block`).
* **Automatic display on** : Cochez les types de publication où l'affichage automatique doit s'appliquer.
* **Active Buttons** : Activez ou désactivez les boutons pour chaque service individuellement.

### Share Texts & Prompts

* **Prompts personnalisés** : Pour chaque service, définissez le texte qui sera pré-rempli lors du partage.
* **X (Twitter) Handle** : Ajoutez votre pseudonyme X, qui sera inclus dans les tweets partagés.

### Button Styling

* **Button Alignment** : Alignez les boutons à gauche, au centre ou à droite de leur conteneur.
* **Button Size** : Choisissez entre trois tailles : `Small`, `Medium`, `Large`.
* **Border Style** : Optez pour des boutons `Rounded` (arrondis) ou `Square` (carrés).
* **Service Colors** : Pour chaque bouton, choisissez la couleur du texte et de l'arrière-plan.



## Installation

1.  Téléchargez la dernière version du plugin depuis le dépôt GitHub (`Code` > `Download ZIP`).
2.  Dans votre administration WordPress, allez dans **Extensions > Ajouter**.
3.  Cliquez sur le bouton **Téléverser une extension** en haut de la page.
4.  Choisissez le fichier `.zip` que vous venez de télécharger et cliquez sur **Installer maintenant**.
5.  Une fois l'installation terminée, cliquez sur **Activer l'extension**.
6.  Configurez le plugin en allant dans **Réglages > effi Share Buttons**.



## Changelog

### 1.6.0

* ** Correctif** : Vérification de l'ID du post en cours pour éviter l'apparition des boutons dans des boucles de posts insérées dans d'autres single posts
* **Fonctionnalité** : Ajout d'un bouton pour Claude IA

### 1.5.0

* **Amélioration** : Possibilité (en option) d'ajouter un titre au-dessus des boutons, avec personnalisation du texte, de la couleur, de la taille et de l'emphase (gras, italique)

### 1.4.0

* **Amélioration** : Possibilité de transmettre le contenu concernant ChatGPT, car il n'accepte pas les URL en mode gratuit (au choix de l'administrateur du site) ; ce dernier est apuré et réduit au maximum, du fait de la limite des longueurs possibles d'URL
* **Amélioration** : Tronquage des URL à 2028 caractères (limite technique acceptable par les navigateurs)
* **Fonctionnalité** : Ajout d'un bouton pour le partage sur Facebook (évite de recourir à d'autres plugins pour le partage social)

### 1.3.0

* **Fonctionnalité** : Ajout d'une option pour sélectionner les types de publication pour l'affichage automatique.

### 1.2.0

* **Amélioration** : Remplacement des liens `<a>` par des balises `<button>` pour une meilleure accessibilité et un meilleur SEO (évite les fuites de PageRank).
* **Fonctionnalité** : Ajout d'une option pour définir l'alignement des boutons (gauche, centre, droite).
* **Correctif** : La couleur des boutons est maintenant préservée au survol (`:hover`), ignorant les styles du thème.

### 1.1.0

* **Correctif majeur** : Résolution d'un bug critique qui empêchait le bloc de s'afficher en front-office en liant explicitement la fonction de rendu en PHP (`render_callback`).
* **Amélioration** : Restructuration de la logique d'initialisation du plugin pour une meilleure stabilité et conformité avec les standards WordPress.

### 1.0.0

* Version initiale du plugin.

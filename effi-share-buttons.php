<?php
/**
 * Plugin Name:       effi Share Buttons
 * Description:       Adds highly customizable share buttons for social networks and AI platforms.
 * Version:           1.6.1
 * Author:            Cédric GIRARD
 * Text Domain:       effi-share-buttons
 */

// S'assure que le fichier n'est pas accédé directement
if (!defined('WPINC')) {
    die;
}

// ---- Fichiers requis (inchangé) ----
// On s'assure que toutes nos fonctions sont disponibles.
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'public/display-buttons.php';
require_once plugin_dir_path(__FILE__) . 'public/dynamic-styles.php';

// ---- Fonctions utilitaires ----
function esb_get_available_services() {
    return [
        'chatgpt' => 'ChatGPT',
        'perplexity' => 'Perplexity',
        'grok' => 'Grok',
        'claude' => 'Claude',
        'google_ai' => 'Google AI',
        'whatsapp' => 'WhatsApp',
        'linkedin' => 'LinkedIn',
        'x' => 'X (Twitter)',
        'facebook' => 'Facebook', 
    ];
}

/**
 * Étape 1 : Enregistrer le bloc Gutenberg.
 * Cette fonction s'assure que WordPress connaît votre bloc, que ce soit
 * dans l'administration ou sur le site public.
 */
function esb_register_block() {
    register_block_type(
        plugin_dir_path(__FILE__) . 'block', // Chemin vers le dossier du bloc
        [
            'render_callback' => 'esb_get_buttons_html' // C'est LA modification cruciale
        ]
    );
}
// On l'accroche au crochet 'init', ce qui est la pratique standard.
add_action('init', 'esb_register_block');

/**
 * Étape 2 : Gérer l'affichage automatique des boutons.
 * Cette fonction ajoute les boutons avant ou après le contenu,
 * en respectant le choix fait dans les réglages.
 */
function esb_setup_automatic_display() {
    // On ne fait rien si on est dans l'éditeur ou une page d'administration.
    if (is_admin()) {
        return;
    }

    $options = get_option('esb_settings');
    $position = isset($options['position']) ? $options['position'] : 'after_content';

    // Si l'option est de n'afficher QUE le bloc, on n'ajoute aucun filtre.
    // C'est un comportement voulu et maintenant très clair.
    if ($position === 'block_only') {
        return;
    }

    switch ($position) {
        case 'before_content':
            add_filter('the_content', 'esb_add_buttons_before_content');
            break;
        case 'after_content':
            add_filter('the_content', 'esb_add_buttons_after_content');
            break;
        case 'both':
            add_filter('the_content', 'esb_add_buttons_before_and_after');
            break;
    }
}
// On accroche aussi cette fonction à 'init'. WordPress gérera l'ordre.
add_action('init', 'esb_setup_automatic_display');


function esb_enqueue_frontend_assets() {
    // Charger les styles dynamiques (déplacé depuis l'ancienne fonction)
    esb_enqueue_dynamic_styles();

    // Charger le script pour la gestion des clics sur les boutons
    wp_enqueue_script(
        'esb-frontend-script',
        plugin_dir_url(__FILE__) . 'public/js/frontend.js',
        [], // Dépendances
        '1.0.0', // Version
        true // Charger dans le pied de page
    );
	
	// Récupérer le contenu brut de l'article
    $post_id = get_the_ID();
    $raw_content = get_post_field('post_content', $post_id);
    
    // Nettoyer le contenu : supprimer les balises HTML et les shortcodes
    $clean_content = wp_strip_all_tags(strip_shortcodes($raw_content));
    
    // Préparer les données à envoyer au script
    $data_for_script = [
        'post_content' => $clean_content,
    ];

    // Envoyer les données au script 'esb-frontend-script'
    // Elles seront accessibles via l'objet JavaScript `esb_data`
    wp_localize_script('esb-frontend-script', 'esb_data', $data_for_script);
}

// ---- Chargement des styles (inchangé) ----
// Cette partie était déjà correcte. Elle ne s'exécute que sur le front-office.

add_action('wp_enqueue_scripts', 'esb_enqueue_frontend_assets');

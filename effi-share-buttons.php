<?php
/**
 * Plugin Name:       effi Share Buttons
 * Description:       Adds highly customizable share buttons for social networks and AI platforms.
 * Version:           1.0
 * Author:            Cédric GIRARD
 * Text Domain:       effi-share-buttons
 */

// S'assure que le fichier n'est pas accédé directement
if (!defined('WPINC')) {
    die;
}

// Fonction utilitaire pour la liste des services
function esb_get_available_services() {
    return [
        'chatgpt' => 'ChatGPT',
        'perplexity' => 'Perplexity',
        'grok' => 'Grok',
        'google_ai' => 'Google AI',
        'whatsapp' => 'WhatsApp',
        'linkedin' => 'LinkedIn',
        'x' => 'X (Twitter)',
    ];
}

// --- VÉRIFICATION N°1 : Les fichiers sont-ils bien inclus ? ---
// Ces lignes sont essentielles pour que les fonctions existent quand WordPress les appelle.
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'public/display-buttons.php';
require_once plugin_dir_path(__FILE__) . 'public/dynamic-styles.php';


// --- VÉRIFICATION N°2 : La fonction d'initialisation est-elle correcte ? ---
/**
 * Initialise le plugin, enregistre le bloc et les filtres.
 */
function esb_init() {
    
    // Enregistrement du bloc Gutenberg.
    register_block_type(plugin_dir_path( __FILE__ ) . '/block');

    // On ne charge les filtres que sur la partie publique du site
    if (is_admin()) {
        return;
    }

    $options = get_option('esb_settings');
    $position = isset($options['position']) ? $options['position'] : 'after_content';

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

// --- VÉRIFICATION N°3 : L'action est-elle bien "accrochée" à WordPress ? ---
// Sans cette ligne, la fonction esb_init() n'est JAMAIS exécutée par WordPress.
add_action('init', 'esb_init');


// Action pour charger les styles dynamiques sur la partie publique
add_action('wp_enqueue_scripts', 'esb_enqueue_dynamic_styles');
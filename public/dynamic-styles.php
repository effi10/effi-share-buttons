<?php
// public/dynamic-styles.php

if (!defined('WPINC')) {
    die;
}

function esb_enqueue_dynamic_styles() {
    // On ne charge les styles que si on n'est pas dans l'admin
    if (is_admin()) {
        return;
    }

    // Récupérer les options de la base de données
    $options = get_option('esb_settings', []);

    // Définir des valeurs par défaut robustes pour TOUS les réglages de style
    $defaults = [
        'button_size'   => 'medium',
        'border_radius' => 'rounded',
        'active_buttons' => [],
        'colors' => [
            'chatgpt'    => ['bg' => '#ff0000', 'text' => '#ffffff'],
            'perplexity' => ['bg' => '#6f42c1', 'text' => '#ffffff'],
            'grok'       => ['bg' => '#1c1c1e', 'text' => '#ffffff'],
            'google_ai'  => ['bg' => '#4285F4', 'text' => '#ffffff'],
            'whatsapp'   => ['bg' => '#25D366', 'text' => '#ffffff'],
            'linkedin'   => ['bg' => '#0077b5', 'text' => '#ffffff'],
            'x'          => ['bg' => '#000000', 'text' => '#ffffff'],
			'facebook'   => ['bg' => '#1877F2', 'text' => '#ffffff'],
            'claude'     => ['bg' => '#D96B3A', 'text' => '#ffffff'], // Couleur orange pour Claude (Anthropic)
        ],
    ];

    // Fusionner les options de la BDD avec les valeurs par défaut
    // Si une option n'est pas définie, la valeur par défaut sera utilisée.
    $options = array_replace_recursive($defaults, $options);

    $alignment_map = [
        'left' => 'flex-start',
        'center' => 'center',
        'right' => 'flex-end',
    ];
    $alignment_value = isset($options['alignment']) ? $options['alignment'] : 'left';
    $justify_content = $alignment_map[$alignment_value];

    $size_map = [
        'small'  => ['padding' => '6px 12px', 'font-size' => '12px'],
        'medium' => ['padding' => '8px 16px', 'font-size' => '14px'],
        'large'  => ['padding' => '12px 22px', 'font-size' => '16px'],
    ];
    $radius_map = [
        'rounded' => '25px',
        'square'  => '4px'
    ];
    
    // On peut maintenant utiliser les options en toute sécurité
    $button_size   = $options['button_size'];
    $border_radius = $options['border_radius'];

    $padding   = $size_map[$button_size]['padding'];
    $font_size = $size_map[$button_size]['font-size'];
    $radius    = $radius_map[$border_radius];

	$alignment_map = [
        'left' => 'flex-start',
        'center' => 'center',
        'right' => 'flex-end',
    ];
    $alignment_value = $options['alignment'] ?? 'left';
    $flex_alignment = $alignment_map[$alignment_value];

    $css = "
    .esb-wrapper {
        display: flex;
        flex-direction: column;
        align-items: {$flex_alignment};
        gap: 15px; /* Espace entre le titre et les boutons */
        margin: 20px 0;
    }
    .esb-title {
        margin: 0;
        padding: 0;
        line-height: 1.2;
    }
    .esb-container {
        margin: 0;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        justify-content: {$flex_alignment}; /* On garde au cas où le flex-wrap crée plusieurs lignes */
    }
    .esb-button {
        display: inline-block;
        padding: {$padding};
        font-size: {$font_size};
        border-radius: {$radius};
        font-weight: bold;
        text-decoration: none !important;
        transition: opacity 0.2s ease;
        line-height: 1.2;
        border: none; /* AJOUT: pour réinitialiser le style des boutons */
        cursor: pointer; /* AJOUT: pour montrer que c'est cliquable */
    }
    .esb-button:hover {
        opacity: 0.85;
    }
    ";
    
    // Générer le CSS pour chaque bouton actif
    foreach ($options['active_buttons'] as $id => $is_active) {
        if ($is_active) {
            $colors = $options['colors'][$id];
            $bg     = esc_attr($colors['bg']);
            $text   = esc_attr($colors['text']);
            $css .= "
            .esb-button-{$id},
            .esb-button-{$id}:hover {
                background-color: {$bg} !important;
                color: {$text} !important;
            }
            ";
        }
    }

    // N'injecter le CSS que s'il n'est pas vide
    if (!empty(trim($css))) {
        wp_register_style('esb-dynamic-styles', false);
        wp_enqueue_style('esb-dynamic-styles');
        wp_add_inline_style('esb-dynamic-styles', $css);
    }
}
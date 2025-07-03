<?php
// public/dynamic-styles.php

if (!defined('WPINC')) {
    die;
}

function esb_enqueue_dynamic_styles() {
    $options = get_option('esb_settings', []);
    
    // Valeurs par défaut pour le style
    $size_map = [
        'small' => ['padding' => '6px 12px', 'font-size' => '12px'],
        'medium' => ['padding' => '8px 16px', 'font-size' => '14px'],
        'large' => ['padding' => '12px 22px', 'font-size' => '16px'],
    ];
    $radius_map = [
        'rounded' => '25px',
        'square' => '4px'
    ];
    
    $button_size = isset($options['button_size']) ? $options['button_size'] : 'medium';
    $border_radius = isset($options['border_radius']) ? $options['border_radius'] : 'rounded';

    $padding = $size_map[$button_size]['padding'];
    $font_size = $size_map[$button_size]['font-size'];
    $radius = $radius_map[$border_radius];

    $css = "
    .ssb-container {
        margin: 20px 0;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }
    .ssb-button {
        display: inline-block;
        padding: {$padding};
        font-size: {$font_size};
        border-radius: {$radius};
        font-weight: bold;
        text-decoration: none;
        transition: opacity 0.2s ease;
		text-decoration: none !important;
    }
    .ssb-button:hover {
        opacity: 0.85;
    }
    ";
    
    // Générer le CSS pour chaque bouton actif
    $services = esb_get_available_services();
    $active_buttons = isset($options['active_buttons']) ? $options['active_buttons'] : [];

    foreach ($services as $id => $label) {
        if (isset($active_buttons[$id])) {
            $colors = $options['colors'][$id];
            $bg = esc_attr($colors['bg']);
            $text = esc_attr($colors['text']);
            $css .= "
            .ssb-button-{$id} {
                background-color: {$bg};
                color: {$text};
            }
            ";
        }
    }

    // Injecter le CSS
    wp_register_style('ssb-dynamic-styles', false);
    wp_enqueue_style('ssb-dynamic-styles');
    wp_add_inline_style('ssb-dynamic-styles', $css);
}
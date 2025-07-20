<?php
// public/display-buttons.php

if (!defined('WPINC')) {
    die;
}

/**
 * Fonction principale pour obtenir le HTML des boutons.
 *
 * @param array    $attributes Les attributs du bloc.
 * @param string   $content    Le contenu interne du bloc.
 * @param WP_Block $block      L'instance du bloc pour accéder au contexte.
 * @return string Le HTML des boutons.
 */
function esb_get_buttons_html($attributes = [], $content = '', $block = null) {
	
    $post_id = 0;
    if (isset($block->context['postId'])) {
        // Méthode N°1 : La plus fiable pour un bloc dynamique.
        $post_id = $block->context['postId'];
    } elseif (get_the_ID()) {
        // Méthode N°2 : Fallback pour les appels via le filtre the_content.
        $post_id = get_the_ID();
    }

    // Si on n'a toujours pas d'ID, on ne peut rien faire.
    if (!$post_id) {
        return '';
    }

    $options = get_option('esb_settings', []);
    $active_buttons = isset($options['active_buttons']) ? $options['active_buttons'] : [];

    if (empty($active_buttons)) {
        return ''; // Ne rien afficher si aucun bouton n'est actif.
    }

	$title_html = '';
    if (!empty($options['title_enabled'])) {
        $text = !empty($options['title_text']) ? $options['title_text'] : 'Summarize or share this post:';
        $tag = !empty($options['title_tag']) ? $options['title_tag'] : 'h3';
        
        $inline_style = '';
        if (strtolower($tag) === 'span') {
            if (!empty($options['title_span_color'])) { $inline_style .= 'color:' . esc_attr($options['title_span_color']) . ';'; }
            if (!empty($options['title_span_size'])) { $inline_style .= 'font-size:' . esc_attr($options['title_span_size']) . 'px;'; }
            if (!empty($options['title_span_bold'])) { $inline_style .= 'font-weight:bold;'; }
            if (!empty($options['title_span_italic'])) { $inline_style .= 'font-style:italic;'; }
        }
        
        // `tag_escape` est utilisé pour sécuriser le nom de la balise
        $title_html = sprintf(
            '<%1$s class="esb-title" style="%2$s">%3$s</%1$s>',
            tag_escape($tag),
            esc_attr($inline_style),
            esc_html($text)
        );
    }

    $post_url = get_permalink($post_id);
    $post_title = get_the_title($post_id);
    $encoded_url = urlencode($post_url);
    $encoded_title = urlencode($post_title);
    
    $buttons_html = '<div class="esb-container">';
    $services = esb_get_available_services();

    foreach ($services as $id => $label) {
        if (!isset($active_buttons[$id])) {
            continue;
        }

        $prompt_template = isset($options['prompts'][$id]) ? $options['prompts'][$id] : '';
        $final_text = str_replace(['{URL}', '{TITLE}'], [$post_url, $post_title], $prompt_template);
        $encoded_text = urlencode($final_text);

        $url = '#';
        switch ($id) {
            case 'chatgpt':
                // Pour ChatGPT, on ne génère pas d'URL ici.
                // On prépare le bouton pour qu'il soit géré par le JavaScript.
                $buttons_html .= sprintf(
                    '<button type="button" class="esb-button esb-share-button esb-button-chatgpt" data-action="share-content" data-service="chatgpt" data-prompt="%s">%s</button>',
                    esc_attr($final_text), // Le prompt est stocké ici
                    esc_html($label)
                );
                // On utilise 'continue 2' pour sauter directement à la prochaine itération de la boucle foreach
                continue 2;
            case 'perplexity':
                $url = 'https://www.perplexity.ai/search?q=' . $encoded_text;
                break;
            case 'grok':
                $url = 'https://x.com/i/grok?text=' . $encoded_text;
                break;
            case 'google_ai':
                $url = 'https://www.google.com/search?udm=50&aep=11&q=' . $encoded_text;
                break;
            case 'whatsapp':
                $url = 'https://wa.me/?text=' . $encoded_text;
                break;
            case 'linkedin':
                $url = "https://www.linkedin.com/sharing/share-offsite/?url={$encoded_url}";
                break;
            case 'x':
                $x_handle = isset($options['x_handle']) ? ' ' . $options['x_handle'] : '';
                $x_text = urlencode(str_replace('{URL}', '', $final_text) . $x_handle);
                $url = "https://x.com/intent/tweet?text={$x_text}&url={$encoded_url}";
                break;
			case 'facebook':
                $url = "https://www.facebook.com/sharer/sharer.php?u={$encoded_url}";
                break;
            case 'claude':
                $url = 'https://claude.ai/new?q=' . $encoded_text;
                break;
        }

        // Affichage du bouton 
        $buttons_html .= sprintf(
            '<button type="button" class="esb-button esb-share-button esb-button-%s" data-url="%s">%s</button>',
            esc_attr($id),
            esc_url($url),
            esc_html($label)
        );
    }

    $buttons_html .= '</div>';
    
	// On retourne le titre et les boutons dans un conteneur global
    return sprintf('<div class="esb-wrapper">%s%s</div>', $title_html, $buttons_html);

}

/* --- Fonctions de Hook pour l'affichage automatique (Inchangées) --- */

function esb_add_buttons_before_content($content) {
    $options = get_option('esb_settings', []);
    // On récupère les clés du tableau (ex: ['post', 'page']) ou on utilise ['post'] par défaut.
    $selected_post_types = isset($options['post_types']) ? array_keys($options['post_types']) : ['post'];

    if (is_singular($selected_post_types) && get_the_ID() === get_queried_object_id()) {
        return esb_get_buttons_html() . $content;
    }
    return $content;
}

function esb_add_buttons_after_content($content) {
    $options = get_option('esb_settings', []);
    $selected_post_types = isset($options['post_types']) ? array_keys($options['post_types']) : ['post'];
    
    if (is_singular($selected_post_types) && get_the_ID() === get_queried_object_id()) {
        return $content . esb_get_buttons_html();
    }
    return $content;
}

function esb_add_buttons_before_and_after($content) {
    $options = get_option('esb_settings', []);
    $selected_post_types = isset($options['post_types']) ? array_keys($options['post_types']) : ['post'];

    if (is_singular($selected_post_types) && get_the_ID() === get_queried_object_id()) {
        $buttons = esb_get_buttons_html();
        return $buttons . $content . $buttons;
    }
    return $content;
}
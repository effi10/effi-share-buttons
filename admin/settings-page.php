<?php
// admin/settings-page.php

if (!defined('WPINC')) {
    die;
}

// Créer la page de réglages dans le menu d'administration
function esb_add_admin_menu() {
    add_options_page(
        'effi Share Buttons Settings',
        'effi Share Buttons',
        'manage_options',
        'super_share_buttons',
        'esb_settings_page_html'
    );
}
add_action('admin_menu', 'esb_add_admin_menu');

// Afficher le HTML de la page de réglages
function esb_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('esb_settings_group');
            do_settings_sections('super_share_buttons');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Enregistrer les réglages, sections et champs
function esb_settings_init() {
    // Enregistre un seul groupe d'options pour optimiser la base de données
    register_setting('esb_settings_group', 'esb_settings');

    // Section 1 : Réglages Généraux
    add_settings_section('esb_general_section', 'General Settings', null, 'super_share_buttons');

    add_settings_field('esb_position_field', 'Button Position', 'esb_position_field_cb', 'super_share_buttons', 'esb_general_section');
	add_settings_field('esb_post_types_field', 'Automatic display on', 'esb_post_types_field_cb', 'super_share_buttons', 'esb_general_section');
	
    add_settings_field('esb_active_buttons_field', 'Active Buttons', 'esb_active_buttons_field_cb', 'super_share_buttons', 'esb_general_section');

    // Section 2 : Textes et Prompts
    add_settings_section('esb_prompts_section', 'Share Texts & Prompts', null, 'super_share_buttons');
    
    $services = esb_get_available_services();
    foreach ($services as $id => $label) {
        add_settings_field(
            "esb_{$id}_prompt_field",
            "{$label} Prompt",
            'esb_prompt_field_cb',
            'super_share_buttons',
            'esb_prompts_section',
            ['id' => $id, 'label' => $label]
        );
    }
    add_settings_field('esb_x_handle_field', 'X (Twitter) Handle', 'esb_x_handle_field_cb', 'super_share_buttons', 'esb_prompts_section');

    // Section 3 : Style des boutons
    add_settings_section('esb_styling_section', 'Button Styling', null, 'super_share_buttons');
    
	add_settings_field('esb_alignment_field', 'Button Alignment', 'esb_alignment_field_cb', 'super_share_buttons', 'esb_styling_section');
    add_settings_field('esb_button_size_field', 'Button Size', 'esb_button_size_field_cb', 'super_share_buttons', 'esb_styling_section');
    add_settings_field('esb_border_radius_field', 'Border Style', 'esb_border_radius_field_cb', 'super_share_buttons', 'esb_styling_section');

    foreach ($services as $id => $label) {
        add_settings_field(
            "esb_{$id}_colors_field",
            "{$label} Colors",
            'esb_colors_field_cb',
            'super_share_buttons',
            'esb_styling_section',
            ['id' => $id, 'label' => $label]
        );
    }
}
add_action('admin_init', 'esb_settings_init');


/* --- CALLBACKS POUR LES CHAMPS --- */

$options = get_option('esb_settings');

function esb_post_types_field_cb() {
    global $options;
    $post_types = get_post_types(['public' => true], 'objects');
    $selected_post_types = isset($options['post_types']) ? $options['post_types'] : ['post' => '1']; // Par défaut, on coche 'post'

    echo '<fieldset>';
    foreach ($post_types as $post_type) {
        if ($post_type->name === 'attachment') {
            continue; // On ignore les pièces jointes
        }
        $is_checked = isset($selected_post_types[$post_type->name]);
        ?>
        <label style="margin-right: 15px;">
            <input 
                type="checkbox" 
                name="esb_settings[post_types][<?php echo esc_attr($post_type->name); ?>]" 
                value="1" 
                <?php checked($is_checked); ?>
            >
            <?php echo esc_html($post_type->labels->singular_name); ?>
        </label>
        <?php
    }
    echo '</fieldset>';
    echo '<p class="description">Select the content types where buttons should be automatically displayed.</p>';
}

function esb_alignment_field_cb() {
    global $options;
    $alignment = isset($options['alignment']) ? $options['alignment'] : 'left';
    ?>
    <select name="esb_settings[alignment]">
        <option value="left" <?php selected($alignment, 'left'); ?>>Left</option>
        <option value="center" <?php selected($alignment, 'center'); ?>>Center</option>
        <option value="right" <?php selected($alignment, 'right'); ?>>Right</option>
    </select>
    <p class="description">Choose the alignment for the button container.</p>
    <?php
}

function esb_position_field_cb() {
    global $options;
    $position = isset($options['position']) ? $options['position'] : 'after_content';
    ?>
    <select name="esb_settings[position]">
        <option value="after_content" <?php selected($position, 'after_content'); ?>>After Content</option>
        <option value="before_content" <?php selected($position, 'before_content'); ?>>Before Content</option>
        <option value="both" <?php selected($position, 'both'); ?>>Before and After Content</option>
        <option value="block_only" <?php selected($position, 'block_only'); ?>>Only with the Gutenberg Block</option>
    </select>
    <p class="description">Choose where to display the share buttons automatically.</p>
    <?php
}

function esb_active_buttons_field_cb() {
    global $options;
    $services = esb_get_available_services();
    foreach ($services as $id => $label) {
        $checked = isset($options['active_buttons'][$id]) ? 'checked' : '';
        echo "<label style='margin-right: 15px;'><input type='checkbox' name='esb_settings[active_buttons][{$id}]' value='1' {$checked}> {$label}</label>";
    }
	echo "<p class='description'>* Google AI doesn't work in France</p>";
}

function esb_prompt_field_cb($args) {
    global $options;
    $id = $args['id'];
    $defaults = [
        'chatgpt' => 'Visit this URL and summarize this post for me: {URL}',
        'perplexity' => 'Summarize this post: {URL}',
        'grok' => 'Summarize this URL: {URL}',
        'google_ai' => 'Summarize this post: {URL}',
        'whatsapp' => '{TITLE} - {URL}',
        'linkedin' => '', // Not needed, uses URL directly
        'x' => '{TITLE}'
    ];
    $value = isset($options['prompts'][$id]) ? esc_attr($options['prompts'][$id]) : $defaults[$id];
    
    if ($id === 'linkedin') {
        echo '<p class="description">LinkedIn sharing does not support custom text via URL.</p>';
        return;
    }

    echo "<input type='text' name='esb_settings[prompts][{$id}]' value='{$value}' class='regular-text'>";
    
	echo '<p class="description">Use {URL} for the post link and {TITLE} for the post title.</p>';		
	
}

function esb_x_handle_field_cb() {
    global $options;
    $handle = isset($options['x_handle']) ? esc_attr($options['x_handle']) : '';
    echo "<input type='text' name='esb_settings[x_handle]' value='{$handle}' placeholder='@yourhandle'>";
    echo '<p class="description">Enter your X (Twitter) handle with the @ symbol.</p>';
}

function esb_button_size_field_cb() {
    global $options;
    $size = isset($options['button_size']) ? $options['button_size'] : 'medium';
    ?>
    <select name="esb_settings[button_size]">
        <option value="small" <?php selected($size, 'small'); ?>>Small</option>
        <option value="medium" <?php selected($size, 'medium'); ?>>Medium</option>
        <option value="large" <?php selected($size, 'large'); ?>>Large</option>
    </select>
    <?php
}

function esb_border_radius_field_cb() {
    global $options;
    $radius = isset($options['border_radius']) ? $options['border_radius'] : 'rounded';
    ?>
    <select name="esb_settings[border_radius]">
        <option value="rounded" <?php selected($radius, 'rounded'); ?>>Rounded</option>
        <option value="square" <?php selected($radius, 'square'); ?>>Square</option>
    </select>
    <?php
}

function esb_colors_field_cb($args) {
    global $options;
    $id = $args['id'];
    $defaults = [
        'chatgpt' => ['bg' => '#10a37f', 'text' => '#ffffff'],
        'perplexity' => ['bg' => '#6f42c1', 'text' => '#ffffff'],
        'grok' => ['bg' => '#1c1c1e', 'text' => '#ffffff'],
        'google_ai' => ['bg' => '#4285F4', 'text' => '#ffffff'],
        'whatsapp' => ['bg' => '#25D366', 'text' => '#ffffff'],
        'linkedin' => ['bg' => '#0077b5', 'text' => '#ffffff'],
        'x' => ['bg' => '#000000', 'text' => '#ffffff'],
    ];
    
    $bg_color = isset($options['colors'][$id]['bg']) ? esc_attr($options['colors'][$id]['bg']) : $defaults[$id]['bg'];
    $text_color = isset($options['colors'][$id]['text']) ? esc_attr($options['colors'][$id]['text']) : $defaults[$id]['text'];

    echo "Background: <input type='color' name='esb_settings[colors][{$id}][bg]' value='{$bg_color}'> ";
    echo "Text: <input type='color' name='esb_settings[colors][{$id}][text]' value='{$text_color}'>";
}
<?php


if (!defined('ABSPATH')) {
    exit;
}


// enregistrer les réglages
function wp_ideogram_settings_init() {
    register_setting('wp_ideogram_options_group', 'wp_ideogram_api_key', 'sanitize_text_field');
    register_setting('wp_ideogram_options_group', 'wp_ideogram_additional_text', 'sanitize_text_field');
    register_setting('wp_ideogram_options_group', 'wp_ideogram_aspect_ratio', 'sanitize_text_field');

    add_settings_section(
        'wp_ideogram_api_section',
        '',
        'wp_ideogram_api_section_callback',
        'wp-ideogram'
    );

    add_settings_field(
        'wp_ideogram_api_key',
        'Clé API',
        'wp_ideogram_api_key_callback',
        'wp-ideogram',
        'wp_ideogram_api_section'
    );

    add_settings_field(
        'wp_ideogram_additional_text',
        'Texte supplémentaire pour le prompt',
        'wp_ideogram_additional_text_callback',
        'wp-ideogram',
        'wp_ideogram_api_section'
    );

    add_settings_field(
        'wp_ideogram_aspect_ratio',
        'Format d\'image',
        'wp_ideogram_aspect_ratio_callback',
        'wp-ideogram',
        'wp_ideogram_api_section'
    );
}
add_action('admin_init', 'wp_ideogram_settings_init');

function wp_ideogram_api_section_callback() {
    echo 'Entrez vos paramètres pour Ideogram API.';
}

function wp_ideogram_api_key_callback() {
    $api_key = get_option('wp_ideogram_api_key');
    echo '<input type="text" name="wp_ideogram_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
}

function wp_ideogram_additional_text_callback() {
    $additional_text = get_option('wp_ideogram_additional_text');
    echo '<textarea name="wp_ideogram_additional_text" class="large-text">' . esc_textarea($additional_text) . '</textarea>';
}

function wp_ideogram_aspect_ratio_callback() {
    $aspect_ratio = get_option('wp_ideogram_aspect_ratio');
    $options = array(
        'ASPECT_10_16',
        'ASPECT_16_10',
        'ASPECT_9_16',
        'ASPECT_16_9',
        'ASPECT_3_2',
        'ASPECT_2_3',
        'ASPECT_4_3',
        'ASPECT_3_4',
        'ASPECT_1_1',
        'ASPECT_1_3',
        'ASPECT_3_1'
    );

    echo '<select name="wp_ideogram_aspect_ratio" class="regular-text">';
    foreach ($options as $option) {
        $selected = selected($aspect_ratio, $option, false);
        echo '<option value="' . esc_attr($option) . '"' . $selected . '>' . esc_html($option) . '</option>';
    }
    echo '</select>';
}
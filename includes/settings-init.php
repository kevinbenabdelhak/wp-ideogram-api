<?php

if (!defined('ABSPATH')) {
    exit;
}

// Enregistrer les réglages
function wp_ideogram_settings_init() {
    register_setting('wp_ideogram_options_group', 'wp_ideogram_api_key', 'sanitize_text_field');
    register_setting('wp_ideogram_options_group', 'wp_ideogram_additional_text', 'sanitize_text_field');
    register_setting('wp_ideogram_options_group', 'wp_ideogram_character_prompt', 'sanitize_text_field'); // Nouveau champ pour le prompt de personnage
    register_setting('wp_ideogram_options_group', 'wp_ideogram_aspect_ratio', 'sanitize_text_field');
    register_setting('wp_ideogram_options_group', 'wp_ideogram_quality_level', 'wp_ideogram_sanitize_quality_level');

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

    // Nouveau champ pour le prompt de personnage
    add_settings_field(
        'wp_ideogram_character_prompt',
        'Prompt pour les images de personnage',
        'wp_ideogram_character_prompt_callback',
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

    add_settings_field(
        'wp_ideogram_quality_level',
        'Niveau de qualité de l\'image (0-10)',
        'wp_ideogram_quality_level_callback',
        'wp-ideogram',
        'wp_ideogram_api_section'
    );
}
add_action('admin_init', 'wp_ideogram_settings_init');

function wp_ideogram_sanitize_quality_level($input) {
    return 10 - intval($input);
}

function wp_ideogram_quality_level_callback() {
    $compression_level = get_option('wp_ideogram_quality_level', 5);
    $quality_level = 10 - $compression_level;
    echo '<input type="range" id="quality_level" name="wp_ideogram_quality_level" min="0" max="10" value="' . esc_attr($quality_level) . '">';
    echo '<span id="quality_value">' . esc_attr($quality_level) . '</span>';
    echo '<p class="description">Utilisez le curseur ci-dessus pour régler le niveau de qualité de l\'image. Une valeur plus élevée correspond à une meilleure qualité, mais peut augmenter la taille du fichier.</p>';
    ?>
    <script type="text/javascript">
        document.getElementById('quality_level').addEventListener('input', function() {
            document.getElementById('quality_value').textContent = this.value;
        });
    </script>
    <?php
}

function wp_ideogram_api_section_callback() {
    echo 'Entrez vos paramètres pour Ideogram API.';
}

function wp_ideogram_api_key_callback() {
    $api_key = get_option('wp_ideogram_api_key');
    echo '<input type="text" name="wp_ideogram_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
    echo '<p class="description">Enregistrez la clé API de votre compte Ideogram</p>';
}

function wp_ideogram_additional_text_callback() {
    $additional_text = get_option('wp_ideogram_additional_text');
    echo '<textarea name="wp_ideogram_additional_text" class="large-text">' . esc_textarea($additional_text) . '</textarea>';
    echo '<p>Prompt personnalisé : Indiquez le style, le fond, les couleurs, etc.</p>';
}

// Nouvelle fonction de callback pour le prompt de personnage
function wp_ideogram_character_prompt_callback() {
    $character_prompt = get_option('wp_ideogram_character_prompt', 'A photo of the character in a different situation, cartoon style, vibrant colors');
    echo '<textarea name="wp_ideogram_character_prompt" class="large-text">' . esc_textarea($character_prompt) . '</textarea>';
    echo '<p>Le prompt qui sera utilisé par l\'API Ideogram pour générer une nouvelle image basée sur le personnage de l\'image de référence.</p>';
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
    echo '<p>Sélectionner le format des images</p>';
}
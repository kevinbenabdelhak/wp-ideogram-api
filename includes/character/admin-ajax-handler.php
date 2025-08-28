<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_generate_character_image_ajax', 'wp_ideogram_generate_character_image_ajax_handler');

function wp_ideogram_generate_character_image_ajax_handler() {
    check_ajax_referer('wp_ideogram_generate_nonce', 'nonce');

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $secondary_prompt = isset($_POST['secondary_prompt']) ? sanitize_textarea_field($_POST['secondary_prompt']) : '';
    $num_images = isset($_POST['num_images']) ? intval($_POST['num_images']) : 1;

    if (empty($post_id)) {
        wp_send_json_error(['message' => 'ID de post manquant.']);
    }

    $api_key = get_option('wp_ideogram_api_key');
    if (empty($api_key)) {
        wp_send_json_error(['message' => 'Clé API manquante.']);
    }

    $original_attachment = get_post($post_id);
    if (!$original_attachment || $original_attachment->post_type !== 'attachment') {
        wp_send_json_error(['message' => 'Pièce jointe invalide.']);
    }

    $original_image_url = wp_get_attachment_url($post_id);
    if (!$original_image_url) {
        wp_send_json_error(['message' => 'URL de l\'image originale introuvable.']);
    }

    $success_count = 0;
    $last_error_message = 'Erreur inconnue.';
    $used_prompt = '';
    for ($i = 0; $i < $num_images; $i++) {
        $result = wp_ideogram_generate_character_image($original_image_url, $api_key, $secondary_prompt);

        if ($result && !is_wp_error($result)) {
            $new_image_data = $result['image_data'];
            $used_prompt = $result['prompt'];
            wp_ideogram_upload_image_to_media_library(
                $new_image_data['url'],
                $original_attachment->post_title,
                $original_attachment->post_name
            );
            $success_count++;
        } else {
            $last_error_message = is_wp_error($result) ? $result->get_error_message() : 'Données d\'image générée non valides.';
            error_log('Ideogram: Échec de la génération pour l\'image ' . $post_id . ': ' . $last_error_message);
        }
    }

    if ($success_count > 0) {
        wp_send_json_success(['generated_count' => $success_count, 'message' => $success_count . ' image(s) générée(s) avec succès.', 'prompt' => $used_prompt]);
    } else {
        wp_send_json_error(['message' => 'Échec de la génération d\'image. Raison: ' . $last_error_message]);
    }
}

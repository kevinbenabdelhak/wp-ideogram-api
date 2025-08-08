<?php 

if (!defined('ABSPATH')) {
    exit;
}



add_filter('bulk_actions-upload', 'wp_ideogram_add_bulk_action');
function wp_ideogram_add_bulk_action($bulk_actions) {
    $bulk_actions['generate_character_image'] = 'Générer une image de personnage (Ideogram)';
    return $bulk_actions;
}
add_filter('handle_bulk_actions-upload', 'wp_ideogram_handle_bulk_action', 10, 3);
function wp_ideogram_handle_bulk_action($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'generate_character_image') {
        return $redirect_to;
    }

    $api_key = get_option('wp_ideogram_api_key');

    if (empty($api_key)) {
        $redirect_url = add_query_arg('ideogram_error', 'no_api_key', $redirect_to);
        return $redirect_url;
    }

    $generated_count = 0;
    foreach ($post_ids as $post_id) {
        $original_attachment = get_post($post_id);
        if (!$original_attachment || $original_attachment->post_type !== 'attachment') {
            continue; 
        }
        $original_image_url = wp_get_attachment_url($post_id);

        if ($original_image_url) {
            $new_image_data = wp_ideogram_generate_character_image($original_image_url, $api_key);

            if ($new_image_data && !is_wp_error($new_image_data)) {
                
                wp_ideogram_upload_image_to_media_library(
                    $new_image_data['url'], 
                    $original_attachment->post_title, 
                    $original_attachment->post_name
                );
                $generated_count++;
            } else {
                 if (is_wp_error($new_image_data)) {
                    error_log('Ideogram: Échec de la génération pour l\'image ' . $post_id . ': ' . $new_image_data->get_error_message());
                } else {
                    error_log('Ideogram: Données de l\'image générée non valides pour l\'image ' . $post_id);
                }
            }
        }
    }

    $redirect_url = add_query_arg('ideogram_success', $generated_count, $redirect_to);
    return $redirect_url;
}
<?php


if (!defined('ABSPATH')) {
    exit;
}

function generate_featured_image_action() {
    check_ajax_referer('generate_featured_image_nonce', 'security');

    $post_id = intval($_POST['post_id']);
    $post_title = get_the_title($post_id);
    $additional_text = get_option('wp_ideogram_additional_text', '');
    $aspect_ratio = get_option('wp_ideogram_aspect_ratio', 'ASPECT_10_16');
    $compression_level = intval(get_option('wp_ideogram_compression_level', 5)); // Obtenir le niveau de compression

    $prompt = sanitize_text_field($post_title . ' ' . $additional_text);

    $api_key = get_option('wp_ideogram_api_key');
    if (!$api_key) {
        wp_send_json_error(['data' => 'Clé API non configurée.']);
    }

    $response = wp_remote_post('https://api.ideogram.ai/generate', [
        'headers' => [
            'Api-Key' => $api_key,
            'Content-Type' => 'application/json'
        ],
        'body' => wp_json_encode([
            'image_request' => [
                'prompt' => $prompt,
                'aspect_ratio' => $aspect_ratio,
                'model' => 'V_2',
                'magic_prompt_option' => 'AUTO'
            ]
        ]),
        'timeout' => 30
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['data' => 'Erreur lors de la communication avec l\'API.']);
    } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['data'][0]['url'])) {
            $image_url = esc_url_raw($data['data'][0]['url']);

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $tmp = download_url($image_url);
            if (is_wp_error($tmp)) {
                wp_send_json_error(['data' => 'Erreur lors du téléchargement de l\'image.']);
            }

            $site_url = get_site_url();
            $parsed_url = parse_url($site_url);
            $domain = preg_replace('/^www\./', '', $parsed_url['host']);
            $filename = $domain . '-' . $post_id . '.jpg';

            $tmp_jpeg = tempnam(sys_get_temp_dir(), 'upload');
            $image_info = getimagesize($tmp);
            switch ($image_info['mime']) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($tmp);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($tmp);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($tmp);
                    break;
                default:
                    @unlink($tmp);
                    wp_send_json_error(['data' => 'Format d\'image non supporté.']);
            }

            // Compression de l'image
            $quality = 100 - ($compression_level * 10); 
            imagejpeg($image, $tmp_jpeg, $quality);
            imagedestroy($image);
            @unlink($tmp); 

            $file_array = array(
                'name' => $filename,
                'tmp_name' => $tmp_jpeg,
            );

            $media_id = media_handle_sideload($file_array, $post_id);
            if (is_wp_error($media_id)) {
                @unlink($file_array['tmp_name']);
                wp_send_json_error(['data' => 'Erreur lors de l\'upload de l\'image.']);
            }

            // Supprimer le fichier temporaire compressé
            @unlink($file_array['tmp_name']);

            set_post_thumbnail($post_id, $media_id);

            wp_send_json_success(['data' => 'Image téléchargée et mise en avant.']);
        } else {
            wp_send_json_error(['data' => 'L\'URL de l\'image n\'a pas été retournée par l\'API.']);
        }
    }
}
add_action('wp_ajax_generate_featured_image_action', 'generate_featured_image_action');
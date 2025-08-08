<?php 

if (!defined('ABSPATH')) {
    exit;
}

function wp_ideogram_generate_character_image($image_url, $api_key) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $temp_file = download_url($image_url);

    if (is_wp_error($temp_file)) {
        error_log('Ideogram: Erreur lors du téléchargement de l\'image de référence : ' . $temp_file->get_error_message());
        return $temp_file;
    }
    
    $prompt = get_option('wp_ideogram_character_prompt', 'A photo of the character in a different situation, cartoon style, vibrant colors');
    $multipart_boundary = '----IdeogramBoundary' . uniqid();
    $request_body = '';

    $request_body .= '--' . $multipart_boundary . "\r\n";
    $request_body .= 'Content-Disposition: form-data; name="prompt"' . "\r\n";
    $request_body .= "\r\n";
    $request_body .= $prompt . "\r\n";

    $request_body .= '--' . $multipart_boundary . "\r\n";
    $request_body .= 'Content-Disposition: form-data; name="rendering_speed"' . "\r\n";
    $request_body .= "\r\n";
    $request_body .= 'TURBO' . "\r\n";

    $request_body .= '--' . $multipart_boundary . "\r\n";
    $request_body .= 'Content-Disposition: form-data; name="style_type"' . "\r\n";
    $request_body .= "\r\n";
    $request_body .= 'AUTO' . "\r\n";
    
    $request_body .= '--' . $multipart_boundary . "\r\n";
    $request_body .= 'Content-Disposition: form-data; name="character_reference_images"; filename="' . basename($image_url) . '"' . "\r\n";
    $request_body .= 'Content-Type: ' . get_post_mime_type(attachment_url_to_postid($image_url)) . "\r\n";
    $request_body .= "\r\n";
    $request_body .= file_get_contents($temp_file);
    $request_body .= "\r\n";

    $request_body .= '--' . $multipart_boundary . '--' . "\r\n";
    
    $response = wp_remote_post(
        'https://api.ideogram.ai/v1/ideogram-v3/generate',
        array(
            'method' => 'POST',
            'headers' => array(
                'Api-Key'      => $api_key,
                'Content-Type' => 'multipart/form-data; boundary=' . $multipart_boundary,
            ),
            'body' => $request_body,
            'timeout' => 60,
        )
    );

    @unlink($temp_file);
    
    if (is_wp_error($response)) {
        error_log('Ideogram: Erreur lors de l\'appel à l\'API Ideogram : ' . $response->get_error_message());
        return new WP_Error('ideogram_api_error', 'Erreur lors de l\'appel à l\'API Ideogram : ' . $response->get_error_message());
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['data']) && !empty($data['data'])) {
        return $data['data'][0];
    } else {
        error_log('Ideogram: Réponse de l\'API invalide ou aucune image retournée. Réponse complète : ' . $body);
        return new WP_Error('ideogram_api_response_error', 'Réponse de l\'API invalide ou aucune image retournée.');
    }
}
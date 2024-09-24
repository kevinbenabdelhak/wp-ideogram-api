<?php 


if (!defined('ABSPATH')) {
    exit;
}


// générer l'image pour éditeur en ajax
function generate_image_not_featured_action() {
    check_ajax_referer('generate_featured_image_nonce', 'security');

    $post_id = intval($_POST['post_id']);
    $post_title = get_the_title($post_id);
    $prompt = sanitize_text_field($_POST['prompt']);
    $aspect_ratio = sanitize_text_field($_POST['aspect_ratio']);
    $magic_prompt_option = sanitize_text_field($_POST['magic_prompt_option']);
    $quality_level = intval($_POST['quality_level']);
    $prompt_for_filename = sanitize_title($prompt);

    // Votre logique de génération d'image ici
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
                'magic_prompt_option' => $magic_prompt_option
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
            


            // insérer les scripts avant de télécharger l'img
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // telecharge l'image ici
            $tmp = download_url($image_url);
            if (is_wp_error($tmp)) {
                wp_send_json_error(['data' => 'Erreur lors du téléchargement de l\'image.']);
            }

            // nom du fichier
            $filename = $prompt_for_filename . '-' . $post_id . '.jpg';

            // préparer les infos de l'image
            $file_array = array(
                'name' => $filename,
                'tmp_name' => $tmp
            );

            // convertir l'image en JPG
            $image_info = getimagesize($file_array['tmp_name']);
            switch ($image_info['mime']) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file_array['tmp_name']);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file_array['tmp_name']);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file_array['tmp_name']);
                    break;
                default:
                    @unlink($file_array['tmp_name']);
                    wp_send_json_error(['data' => 'Format d\'image non supporté.']);
            }



       // Convertir l'image en JPG
            $image_info = getimagesize($file_array['tmp_name']);
            switch ($image_info['mime']) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($file_array['tmp_name']);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($file_array['tmp_name']);
                    break;
                case 'image/gif':
                    $image = imagecreatefromgif($file_array['tmp_name']);
                    break;
                default:
                    @unlink($file_array['tmp_name']);
                    wp_send_json_error(['data' => 'Format d\'image non supporté.']);
            }

              // applique la compression de l'image
            $compress_quality = ($quality_level * 10); // le niveau de qualité est inversé
            
            // Sauvegarder l'image en JPG
            $jpg_path = tempnam(sys_get_temp_dir(), 'ideogram') . '.jpg';
             imagejpeg($image, $jpg_path, 100 - $compress_quality);
            imagedestroy($image);
            @unlink($file_array['tmp_name']); // supprime l'image temporairement

            // Préparer le fichier JPG pour le téléchargement de la nouvelle image
            $file_array['tmp_name'] = $jpg_path;

            // Charger l'image dans la bibliothèque de médias
            $media_id = media_handle_sideload($file_array, $post_id);
            if (is_wp_error($media_id)) {
                @unlink($file_array['tmp_name']);
                wp_send_json_error(['data' => 'Erreur lors de l\'upload de l\'image.']);
            }

            // mettre à jour les données de l'img
            $attachment_data = [
                'ID' => $media_id,
                'post_title' => $post_title,
                'post_excerpt' => '',
                'post_content' => ''
            ];
            wp_update_post($attachment_data);
            update_post_meta($media_id, '_wp_attachment_image_alt', $post_title);

            // URL de l'image après conversion
            $local_image_url = wp_get_attachment_url($media_id);
            wp_send_json_success(['image_url' => $local_image_url]);
        } else {
            wp_send_json_error(['data' => 'L\'URL de l\'image n\'a pas été retournée par l\'API.']);
        }
    }
}
add_action('wp_ajax_generate_image_not_featured', 'generate_image_not_featured_action');
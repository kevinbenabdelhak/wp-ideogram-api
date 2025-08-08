<?php 

if (!defined('ABSPATH')) {
    exit;
}

function wp_ideogram_upload_image_to_media_library($image_url, $title, $slug) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $temp_file = download_url($image_url);

    if (is_wp_error($temp_file)) {
        error_log('Ideogram: Erreur lors du téléchargement de l\'image depuis Ideogram : ' . $temp_file->get_error_message());
        return $temp_file;
    }
    
    $file_type = wp_check_filetype( basename(parse_url($image_url, PHP_URL_PATH)) );
    $extension = isset($file_type['ext']) ? $file_type['ext'] : 'png';

    // Crée un nom de fichier propre, basé sur le slug et un horodatage
    $filename = $slug . '-' . time() . '.' . $extension;

    $file_args = array(
        'name'     => $filename,
        'tmp_name' => $temp_file,
    );
    
    add_filter('upload_mimes', 'wp_ideogram_allow_specific_mime_types');
    
    // N'ajoute pas de parent_post_id pour que la nouvelle image soit un média indépendant
    $file_id = media_handle_sideload($file_args, 0, $title);
    
    remove_filter('upload_mimes', 'wp_ideogram_allow_specific_mime_types');

    if (is_wp_error($file_id)) {
        @unlink($temp_file);
        error_log('Ideogram: Erreur lors de l\'ajout de l\'image à la médiathèque : ' . $file_id->get_error_message());
        return $file_id;
    }
    
    // Met à jour le titre et le slug pour un affichage propre
    $attachment_post = array(
        'ID'           => $file_id,
        'post_title'   => $title,
        'post_name'    => $slug . '-' . time(),
    );
    
    wp_update_post($attachment_post);

    $attachment_url = wp_get_attachment_url($file_id);
    error_log('Ideogram: Image ajoutée à la médiathèque, URL : ' . $attachment_url);

    return $file_id;
}

// Filtre pour autoriser le MIME type des images Ideogram
function wp_ideogram_allow_specific_mime_types($mimes) {
    $mimes['png'] = 'image/png';
    $mimes['jpeg'] = 'image/jpeg';
    $mimes['jpg'] = 'image/jpeg';
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
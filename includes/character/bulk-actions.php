<?php 

if (!defined('ABSPATH')) {
    exit;
}



add_filter('bulk_actions-upload', 'wp_ideogram_add_bulk_action');
function wp_ideogram_add_bulk_action($bulk_actions) {
    $bulk_actions['generate_character_image'] = 'Générer une image de personnage (Ideogram)';
    return $bulk_actions;
}
add_action('admin_enqueue_scripts', 'wp_ideogram_enqueue_bulk_actions_script');
function wp_ideogram_enqueue_bulk_actions_script($hook) {
    if ('upload.php' !== $hook) {
        return;
    }
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_script('wp-ideogram-bulk-actions', $plugin_url . '../../assets/js/bulk-actions.js', array('jquery'), null, true);
    wp_localize_script('wp-ideogram-bulk-actions', 'wpIdeogram', array(
        'nonce' => wp_create_nonce('wp_ideogram_generate_nonce')
    ));
}

require_once(plugin_dir_path(__FILE__) . 'admin-ajax-handler.php');
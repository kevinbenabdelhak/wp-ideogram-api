<?php

if (!defined('ABSPATH')) {
    exit;
}


//page des réglages
function register_ideogram_api_settings_page() {
    add_options_page(
        'WP Ideogram API',
        'WP Ideogram API',
        'manage_options',
        'wp-ideogram',
        'wp_ideogram_settings_page'
    );
}
add_action('admin_menu', 'register_ideogram_api_settings_page');
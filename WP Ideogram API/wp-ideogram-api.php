<?php
/*
 * Plugin Name: WP Ideogram API
 * Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-ideogram-api/
 * Description: WP Ideogram API est un plugin qui génère vos images mises en avant sur vos publications en passant par l'API d'Ideogram.
 * Version: 1.3
 * Author: Kevin Benabdelhak
 * Author URI: https://kevin-benabdelhak.fr/
 * Contributors: kevinbenabdelhak
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-init.php';
require_once plugin_dir_path(__FILE__) . 'includes/bulk-action-js.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/postbox-image-mise-en-avant.php';

<?php
/*
 * Plugin Name: WP Ideogram API
 * Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-ideogram-api/
 * Description: WP Ideogram API est un plugin qui génère vos images mises en avant sur vos publications en passant par l'API d'Ideogram.
 * Version: 2.0
 * Author: Kevin Benabdelhak
 * Author URI: https://kevin-benabdelhak.fr/
 * Contributors: kevinbenabdelhak
*/

if (!defined('ABSPATH')) {
    exit;
}



if ( !class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
    require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
}
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$monUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/kevinbenabdelhak/wp-ideogram-api/', 
    __FILE__,
    'wp-ideogram-api' 
);

$monUpdateChecker->setBranch('main');






require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings-init.php';
require_once plugin_dir_path(__FILE__) . 'includes/bulk-action-js.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/postbox-image-mise-en-avant.php';

require_once plugin_dir_path(__FILE__) . 'includes/editeur/ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/editeur/selectionner-texte-editeur.php';




require_once plugin_dir_path(__FILE__) . 'includes/character/bulk-actions.php';
require_once plugin_dir_path(__FILE__) . 'includes/character/admin-notices.php';
require_once plugin_dir_path(__FILE__) . 'includes/character/generate-character-image.php';
require_once plugin_dir_path(__FILE__) . 'includes/character/upload-image.php';


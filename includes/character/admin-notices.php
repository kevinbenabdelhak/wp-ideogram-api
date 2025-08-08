<?php 

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_notices', 'wp_ideogram_admin_notices');
function wp_ideogram_admin_notices() {
    if (isset($_REQUEST['ideogram_error'])) {
        if ($_REQUEST['ideogram_error'] === 'no_api_key') {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Erreur Ideogram :</strong> La clé API n\'est pas configurée. Veuillez la saisir dans les réglages du plugin.</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p><strong>Erreur Ideogram :</strong> ' . esc_html(urldecode($_REQUEST['ideogram_error'])) . '</p></div>';
        }
    }
    if (isset($_REQUEST['ideogram_success'])) {
        $count = intval($_REQUEST['ideogram_success']);
        echo '<div class="notice notice-success is-dismissible"><p><strong>Succès Ideogram :</strong> ' . $count . ' image(s) de personnage générée(s) et ajoutée(s) à la médiathèque.</p></div>';
    }
}
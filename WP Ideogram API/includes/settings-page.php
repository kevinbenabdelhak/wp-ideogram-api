<?php


if (!defined('ABSPATH')) {
    exit;
}

// Appel du formulaire
function wp_ideogram_settings_page() {
    ?>
    <div class="wrap">
        <h1>WP Ideogram API</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_ideogram_options_group');
            do_settings_sections('wp-ideogram');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
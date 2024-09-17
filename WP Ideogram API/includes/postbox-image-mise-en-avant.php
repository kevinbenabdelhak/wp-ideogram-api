<?php 

if (!defined('ABSPATH')) {
    exit;
}

function wp_ideogram_add_featured_image_button() {
    add_action('admin_post_thumbnail_html', 'wp_ideogram_add_elements_to_thumbnail_box');
}
add_action('admin_init', 'wp_ideogram_add_featured_image_button');


function wp_ideogram_add_elements_to_thumbnail_box($content) {
    ob_start();
    ?>
    <div id="wp-ideogram-generate-featured-image" style="margin-top: 10px;">
        <button type="button" class="button button-primary" id="generate-featured-image"><?php esc_html_e('Générer une image mise en avant', 'wp-ideogram'); ?></button>
    </div>
    <div id="image-generation-loader" style="display: none; margin-top: 10px;">
        <span class="spinner is-active"></span> <span id="generation-progress"><?php esc_html_e('Chargement...', 'wp-ideogram'); ?></span>
    </div>
    <div id="image-generation-result" style="margin-top: 10px;"></div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#generate-featured-image').on('click', function() {
                var post_id = <?php echo get_the_ID(); ?>; 
                var nonceVal = '<?php echo wp_create_nonce('generate_featured_image_nonce'); ?>';

                // Afficher le loader
                $('#image-generation-loader').show();
                $('#image-generation-result').empty(); 
                $('#generate-featured-image').attr('disabled', true); 

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'generate_featured_image_action',
                        post_id: post_id,
                        security: nonceVal
                    },
                    success: function(response) {
                        var resultDiv = $('#image-generation-result');
                        $('#image-generation-loader').hide(); 
                        $('#generate-featured-image').attr('disabled', false); 

                        if (response.success) {
                            // Afficher le lien de l'image générée
                            resultDiv.append("<div class='notice notice-success'><p><?php esc_html_e('Image générée:', 'wp-ideogram'); ?> <a href='" + response.data.thumbnail_url + "' target='_blank'>" + response.data.thumbnail_url + "</a></p></div>");

                          
                            var mediaId = response.data.media_id;
                            $('#_thumbnail_id').val(mediaId); 

                            // remplacer l'image dans la balise <a id="set-post-thumbnail">
                            var thumbnailUrl = response.data.thumbnail_url;
                            $('#set-post-thumbnail').html('<img width="266" height="149" src="' + thumbnailUrl + '" class="attachment-266x266 size-266x266" alt="" loading="lazy" decoding="async">');
                        } else {
                            resultDiv.append("<div class='notice notice-error'><p><?php esc_html_e('Image non générée:', 'wp-ideogram'); ?> " + response.data + "</p></div>");
                        }
                    },
                    error: function() {
                        $('#image-generation-loader').hide();
                        $('#generate-featured-image').attr('disabled', false); 
                        $('#image-generation-result').append("<div class='notice notice-error'><p><?php esc_html_e('Image non générée.', 'wp-ideogram'); ?></p></div>");
                    }
                });
            });
        });
    </script>
    <?php
    $content .= ob_get_clean();
    return $content;
}
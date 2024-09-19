<?php 

if (!defined('ABSPATH')) {
    exit;
}


function wp_ideogram_add_custom_script() {
    $screen = get_current_screen();
    if ($screen->base === 'post') { 


        // récupère les options du plugin
        $aspect_ratio = get_option('wp_ideogram_aspect_ratio', 'ASPECT_10_16');
        $magic_prompt_option = get_option('wp_ideogram_magic_prompt_option', 'AUTO');
        $additional_text = get_option('wp_ideogram_additional_text', '');
        $quality_level = get_option('wp_ideogram_quality_level', 5);
        ?>



        <!-- js pour générer le boutton   -->      
        <script type="text/javascript">
          
            jQuery(document).ready(function($) {
                var generateButton;
                var loader = $('<div>', {
                    text: 'Génération en cours...',
                    class: 'wp-ideogram-loader'
                }).css({
                  position: 'fixed',
                    top: '40px', 
                    right: '270px',
                    padding: '10px',
                    backgroundColor: '#c21826',
					color:'#fff',
                    border: '1px solid #ccc',
                    zIndex: 10000,
                    display: 'none'
                }).appendTo('body');

                var requestCount = 0;
                var successCount = 0;

                function showLoader() {
                    requestCount++;
                    loader.text('Génération en cours... (' + successCount + '/' + requestCount + ')').show();
                }

                function hideLoader() {
                    successCount++;
                    loader.text('Génération en cours... (' + successCount + '/' + requestCount + ')');
                    if (successCount >= requestCount) {
                        loader.fadeOut();
                        requestCount = 0;
                        successCount = 0;
                    }
                }

                function handleErrorResponse(response) {
                  
                    var errorMessage;

                    if (typeof response === 'object' && response.responseJSON && response.responseJSON.data) {
                        errorMessage = response.responseJSON.data;
                    } else {
                        errorMessage = response.statusText || 'Erreur de connexion avec l\'API.';
                    }

                    alert('Erreur: ' + errorMessage);
                }

                function showGenerateButton(e) {
                    var selectedText = tinymce.editors['content'].selection.getContent({format: 'text'});
                    var selectedNode = tinymce.activeEditor.selection.getNode();



                    if (selectedText.length > 0) {
                        if (generateButton) {
                            generateButton.remove();
                        }
                        generateButton = $('<button>', {
                            text: 'Générer une image',
                            class: 'button button-primary wp-ideogram-generate-button'
                        }).css({
                            margin: '10px 0',
                            display: 'block',
							
                            position: 'absolute',
    
                          zIndex: '100000000',
                             top: '10px',
                         left: '145px'
                        }).insertBefore('#wp-content-media-buttons');

                        generateButton.on('click', function() {
                            showLoader();
                            var prompt = selectedText + '| Voici les instructions pour le style : ' + "<?php echo esc_js($additional_text); ?>";
                            $.ajax({
                                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                method: 'POST',
                                data: {
                                    action: 'generate_image_not_featured',
                                    post_id: $('#post_ID').val(),
                                    security: '<?php echo wp_create_nonce('generate_featured_image_nonce'); ?>',
                                    prompt: prompt,
                                    aspect_ratio: "<?php echo esc_js($aspect_ratio); ?>",
                                    magic_prompt_option: "<?php echo esc_js($magic_prompt_option); ?>",
                                    quality_level: "<?php echo esc_js($quality_level); ?>"
                                },
                                success: function(response) {
                                
                                    if (response.success) {
                                        var img = $('<img>', { src: response.data.image_url });

                                        // insérer l'image juste après le noeud d'origine
                                        tinymce.activeEditor.dom.insertAfter(tinymce.activeEditor.dom.create('p', {}, img.prop('outerHTML')), selectedNode);

                                    } else {
                                        handleErrorResponse(response);
                                    }
                                    hideLoader();
                                },
                                error: function(response) {
                                    handleErrorResponse(response);
                                    hideLoader();
                                }
                            });
                            generateButton.remove(); // supprimer le bouton après clic
                        });
                    } else if (generateButton) {
                        generateButton.remove();
                    }
                }

                if (typeof tinymce !== 'undefined' && tinymce.editors['content']) {
                    var editor = tinymce.editors['content'];
                    editor.on('init', function() {
               
                        var iframe = document.querySelector('#content_ifr').contentWindow;

                        $(iframe.document.body).on('mouseup', function(e) {
                     
                            showGenerateButton(e);
                        });
                    });
                } else {
                    console.error('TinyMCE n\'est pas défini');
                }
            });
        </script>
        <?php
    }
}
add_action('admin_footer', 'wp_ideogram_add_custom_script');
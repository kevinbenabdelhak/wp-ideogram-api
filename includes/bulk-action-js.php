<?php

if (!defined('ABSPATH')) {
    exit;
}

// Enregistrement de l'action groupée dans le menu déroulant WordPress
function wp_ideogram_register_bulk_action($bulk_actions) {
    $bulk_actions['generate_featured_image'] = __('Générer une image mise en avant', 'wp-ideogram');
    return $bulk_actions;
}
add_filter('bulk_actions-edit-post', 'wp_ideogram_register_bulk_action');

// JS d'exécution avec capture des messages d'erreur de l'API
function custom_bulk_action_admin_footer_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var nonceVal = '<?php echo wp_create_nonce('generate_featured_image_nonce'); ?>';

            $(document).on('click', '#doaction, #doaction2', function (e) {
                var action = $('select[name="action"]').val() !== '-1' ? $('select[name="action"]').val() : $('select[name="action2"]').val();
                
                if (action !== 'generate_featured_image') return;
                
                e.preventDefault();

                var postIDs = [];
                $('input[name="post[]"]:checked').each(function () {
                    postIDs.push($(this).val());
                });

                if (postIDs.length === 0) {
                    alert('Aucun post sélectionné');
                    return;
                }

                $('#bulk-action-loader').remove();
                $('#doaction, #doaction2').after("<div id='bulk-action-loader' style='display:inline-block; margin-left:10px;'><span class='spinner is-active' style='float:none; margin:0 5px 0 0;'></span> <span id='generation-progress'>0 / " + postIDs.length + " terminés</span></div>");

                var completedCount = 0;
                var errorMessages = [];

                function processNext(index) {
                    if (index >= postIDs.length) {
                        $('#bulk-action-loader').remove();

                        // Affichage du succès
                        if (completedCount > 0) {
                            var successNotice = "<div class='notice notice-success is-dismissible'><p>" + completedCount + " post(s) traité(s) avec succès.</p></div>";
                            $(successNotice).insertAfter(".wp-header-end");
                        }

                        // Affichage du détail des erreurs retournées par l'API
                        if (errorMessages.length > 0) {
                            var errorHtml = "<div class='notice notice-error is-dismissible'><p><strong>" + errorMessages.length + " échec(s) lors de la génération :</strong></p><ul>";
                            $.each(errorMessages, function(i, msg) {
                                errorHtml += "<li>" + msg + "</li>";
                            });
                            errorHtml += "</ul></div>";
                            $(errorHtml).insertAfter(".wp-header-end");
                        }

                        return;
                    }

                    var currentPostId = postIDs[index];

                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'generate_featured_image_action',
                            post_id: currentPostId,
                            security: nonceVal
                        },
                        success: function (response) {
                            if (response.success) {
                                completedCount++;
                            } else {
                                // Récupération de la réponse exacte transmise par l'API / PHP
                                var apiResponse = (typeof response.data === 'string') ? response.data : JSON.stringify(response.data);
                                errorMessages.push("Article ID " + currentPostId + " : " + (apiResponse || "Erreur inconnue"));
                            }
                            $('#generation-progress').text((completedCount + errorMessages.length) + " / " + postIDs.length + " terminés");
                            processNext(index + 1);
                        },
                        error: function (xhr, status, error) {
                            errorMessages.push("Article ID " + currentPostId + " : Erreur réseau / serveur (" + error + ")");
                            $('#generation-progress').text((completedCount + errorMessages.length) + " / " + postIDs.length + " terminés");
                            processNext(index + 1);
                        }
                    });
                }

                processNext(0);
            });
        });
    </script>
    <?php
}
add_action('admin_footer-edit.php', 'custom_bulk_action_admin_footer_js');
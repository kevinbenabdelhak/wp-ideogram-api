<?php


if (!defined('ABSPATH')) {
    exit;
}


// bouton mode bulk + requête ajax
function custom_bulk_action_admin_footer_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            if ($('select[name="action"] option[value="generate_featured_image"]').length === 0) {
                $('select[name="action"], select[name="action2"]').append('<option value="generate_featured_image">Générer une image mise en avant</option>');
            }

            var nonceVal = '<?php echo wp_create_nonce('generate_featured_image_nonce'); ?>';

            $(document).on('click', '#doaction, #doaction2', function (e) {
                var action = $('select[name="action"]').val() !== '-1' ? $('select[name="action"]').val() : $('select[name="action2"]').val();
                if (action !== 'generate_featured_image') return;
                e.preventDefault();
                var postIDs = [];
                $('tbody th.check-column input[type="checkbox"]:checked').each(function () {
                    postIDs.push($(this).val());
                });
                if (postIDs.length === 0) {
                    alert('Aucun post sélectionné');
                    return;
                }

                $('#bulk-action-loader').remove();
                $('#doaction, #doaction2').after("<div id='bulk-action-loader'><span class='spinner is-active' style='margin-left: 10px;'></span> <span id='generation-progress'>0 / " + postIDs.length + " terminés</span></div>");
                var completedCount = 0;
                var failedCount = 0;

                function processNext(index) {
                    if (index >= postIDs.length) {
                        $('#bulk-action-loader').remove();
                        if (completedCount > 0) {
                            var message = completedCount + " post(s) traité(s) avec succès.";
                            $("<div class='notice notice-success is-dismissible'><p>" + message + "</p></div>").insertAfter(".wp-header-end");
                        }
                        if (failedCount > 0) {
                            var message = failedCount + " échec(s).";
                            $("<div class='notice notice-error is-dismissible'><p>" + message + "</p></div>").insertAfter(".wp-header-end");
                        }
                        return;
                    }

                    $.ajax({
                        url: ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'generate_featured_image_action',
                            post_id: postIDs[index],
                            security: nonceVal
                        },
                        success: function (response) {
                            if (response.success) {
                                completedCount++;
                            } else {
                                failedCount++;
                            }
                            $('#generation-progress').text(completedCount + " / " + postIDs.length + " terminés");
                            processNext(index + 1);
                        },
                        error: function () {
                            failedCount++;
                            $('#generation-progress').text(completedCount + " / " + postIDs.length + " terminés");
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
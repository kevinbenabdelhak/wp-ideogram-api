jQuery(document).ready(function($) {
    const bulkActionSelect = $('select[name="action"], select[name="action2"]');
    const generateAction = 'generate_character_image';

    const customControls = $(`
        <div id="ideogram-bulk-controls" style="display: none; margin-top: 10px;">
            <label for="ideogram-secondary-prompt">Prompt Secondaire:</label>
            <textarea id="ideogram-secondary-prompt" style="width: 100%;" rows="3"></textarea>
            <label for="ideogram-num-images">Nombre d'images à générer par sélection:</label>
            <input type="number" id="ideogram-num-images" value="1" min="1" max="10" style="width: 60px;">
            <div id="ideogram-progress-container" style="margin-top: 10px;"></div>
        </div>
    `);

    $('.bulkactions').append(customControls);

    bulkActionSelect.on('change', function() {
        if ($(this).val() === generateAction) {
            $('#ideogram-bulk-controls').show();
        } else {
            $('#ideogram-bulk-controls').hide();
        }
    });

    $('#doaction, #doaction2').on('click', function(e) {
        const selectedAction = $(this).closest('.bulkactions').find('select').val();
        if (selectedAction !== generateAction) {
            return;
        }

        e.preventDefault();

        const postIds = $('input[name="media[]"]:checked').map(function() {
            return $(this).val();
        }).get();

        if (postIds.length === 0) {
            alert('Veuillez sélectionner des images.');
            return;
        }

        const secondaryPrompt = $('#ideogram-secondary-prompt').val();
        const numImages = $('#ideogram-num-images').val();
        const totalImagesToGenerate = numImages * postIds.length;
        const progressContainer = $('#ideogram-progress-container');
        progressContainer.html('<p id="ideogram-progress-text"></p>');
        const progressText = $('#ideogram-progress-text');
        progressText.text(`Génération de ${totalImagesToGenerate} images (quantité: ${numImages})...`);
        let completed = 0;
        let successful = 0;
        let failed = 0;

        function processImage(index) {
            if (index >= postIds.length) {
                progressContainer.append('<p>Toutes les opérations sont terminées. Rechargement de la page...</p>');
                setTimeout(function() {
                    location.reload();
                }, 2000);
                return;
            }

            const postId = postIds[index];
            const data = {
                action: 'generate_character_image_ajax',
                nonce: wpIdeogram.nonce,
                post_id: postId,
                secondary_prompt: secondaryPrompt,
                num_images: numImages
            };

            progressText.text(`Génération de l'image ${index + 1}/${postIds.length}...`);

            $.post(ajaxurl, data, function(response) {
                completed++;
                if (response.success) {
                    successful += response.data.generated_count;
                    progressContainer.append(`<p>Prompt utilisé pour l'image ${index + 1}: ${response.data.prompt}</p>`);
                } else {
                    failed++;
                    progressContainer.append(`<p style="color: red;">Erreur pour l'image ID ${postId}: ${response.data.message}</p>`);
                }
                
                progressText.text(`Images générées : ${successful}/${totalImagesToGenerate}`);

                setTimeout(function() {
                    processImage(index + 1);
                }, 1000); 
            });
        }

        processImage(0);
    });
});

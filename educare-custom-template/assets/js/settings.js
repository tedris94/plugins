jQuery(document).ready(function($) {
    // Handle thumbnail upload
    $('#upload_thumbnail').on('click', function(e) {
        e.preventDefault();

        var frame = wp.media({
            title: 'Select Template Thumbnail',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#educare_custom_template_thumbnail').val(attachment.id);
            $('.thumbnail-preview').html('<img src="' + attachment.url + '" alt="Template Thumbnail" style="max-width: 300px;">');
            $('#remove_thumbnail').show();
        });

        frame.open();
    });

    // Handle thumbnail removal
    $('#remove_thumbnail').on('click', function(e) {
        e.preventDefault();
        $('#educare_custom_template_thumbnail').val('');
        $('.thumbnail-preview').empty();
        $(this).hide();
    });
}); 
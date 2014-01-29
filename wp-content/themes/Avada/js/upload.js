jQuery(document).ready(function($){
    if($('.avada_upload_button').length >= 1) {
        window.avada_uploadfield = '';

        $('.avada_upload_button').live('click', function() {
            window.avada_uploadfield = $('.upload_field', $(this).parent());
            tb_show('Upload', 'media-upload.php?type=image&TB_iframe=true', false);

            return false;
        });

        window.avada_send_to_editor_backup = window.send_to_editor;
        window.send_to_editor = function(html) {
            if(window.avada_uploadfield) {
                var image_url = $('img', html).attr('src');
                $(window.avada_uploadfield).val(image_url);
                window.avada_uploadfield = '';
                
                tb_remove();
            } else {
                window.avada_send_to_editor_backup(html);
            }
        }
    }
});
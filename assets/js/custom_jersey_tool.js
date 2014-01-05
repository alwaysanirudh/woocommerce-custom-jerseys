(function($) {

    jQuery('#iframe_cart').load(function() {
        var $this = $(this), $contents = jQuery(this).contents();
        $contents.find('.container:eq(0)').remove();
        $contents.find('.container:eq(1)').remove();

        $this.height($contents.find('body')[0].scrollHeight + 25);

        var $wrapper_custom_jerseys_orders = jQuery('#custom_jerseys_orders');

        $wrapper_custom_jerseys_orders.on('change', '.qty', function() {
            var $this = jQuery(this),
                    $wrapper_tr_children = $this.closest('.children');

            var current_val_children = 0;
            $wrapper_custom_jerseys_orders.find('tr[children_id="' + $wrapper_tr_children.attr('children_id') + '"] .quantity input.qty').map(function() {
                current_val_children += parseFloat(this.value);
            });


            console.log(current_val_children);

            var $wrapper_tr_parent = $wrapper_tr_children.closest('#custom_jerseys_orders').find('tr[parent_id="' + $wrapper_tr_children.attr('children_id') + '"]');
            $wrapper_tr_parent.find('input:hidden').val(current_val_children);

        });

    });

    var $container_tabs = $('#container_tabs'), $btn_fileupload = $('#btn_fileupload'), $filename = $('#fileupload_name'), $fileupload_requirements = $('#fileupload_requirements');

    var fileuploadDone = function() {
        var $this = $(this), data = $this.data();

        if (!data) {
            $this.prop('disabled', true);
            return false;
        }

        if (!$fileupload_requirements.is(':checked')) {
            $this.prop('disabled', false);
            return false;
        }

        $this.off('click')
                .text('Abort')
                .on('click', function() {
            $this.attr('disabled', true);
            data.abort();
        });

        data.submit().always(function() {
            $this.attr('disabled', true);
        });
    }

    $btn_fileupload.prop('disabled', true);

    $('#fileupload').fileupload({
        url: custom_jersey_ajax.url,
        dataType: 'json',
        autoUpload: false,
        acceptFileTypes: /(\.|\/)(eps|jpe?g|ai|pdf)$/i,
        maxFileSize: 5000000 // 5 MB
    }).on('fileuploadsubmit', function(e, data) {
        var $current_shirt_color = $container_tabs.find('#tab_colors hgroup a.seleccionado'),
                data_params = $current_shirt_color.data('form_data');

        data.formData = {
            action: 'woocommerce_custom_jerseys_ajax',
            type: 'upload_logos',
            product_id: data_params.product_id
        };

        if (!data.formData.product_id) {
            return false;
        }

    }).on('fileuploadadd', function(e, data) {
        data.context = $btn_fileupload.parent();
        $.each(data.files, function(index, file) {
            $filename.val(file.name);
        });

        $btn_fileupload.on('click', fileuploadDone).data(data).prop('disabled', false);

    }).on('fileuploadprocessalways', function(e, data) {
        var index = data.index,
                file = data.files[index],
                $context = data.context;

        $context.find('span.text-danger').remove();
        if (file.error) {
            $context.append($('<span class="text-danger"/>').text(file.error));
        }

        if (index + 1 === data.files.length) {
            $context.find('button')
                    .text('Upload')
                    .prop('disabled', !!data.files.error);
        }

    }).on('fileuploaddone', function(e, data) {

        $btn_fileupload.removeData()
                .text('Upload')
                .prop('disabled', true);

        $filename.val('');
        $fileupload_requirements.removeAttr('checked');

        $.each(data.result.files, function(index, file) {
            console.log(file);
            if (file.url) {
                nombreSeleccionado = file.name;
                confirmarUpload();

                var data_logo_new = {
                    size: file.size,
                    filename: file.filename_codify,
                    type: 'logo'
                };

                if (actualLogoTab >= 2) {
                    var fileupload_logo = pestanhas[actualLogoTab].getElementsByTagName("hgroup")[0].getElementsByTagName("div")[actualLogoIndex];
                    var data_logos = $(fileupload_logo).data('logos');
                    $.extend(data_logos, data_logo_new);
                } else {
                    var data_logos = $('#jdt_logo_name').data('logos');
                    $.extend(data_logos, data_logo_new);
                }

                smalldog_custom_jerseys_one.logoUploaded(file);

            } else if (file.error) {
                data.context.append($('<span class="text-danger"/>').text(file.error));
                data.context.find('button').attr('disabled', true);
            }
        });

    }).on('fileuploadfail', function(e, data) {
        data.context.append($('<span class="text-danger"/>').text(file.error));
        data.context.find('button').attr('disabled', true);
    });


})(jQuery);

var smalldog_custom_jerseys = function() {
    var data_filename_logos = [], render_logos = null;

    var _logoUploaded = function(logo_uploaded) {
        var logos_uploaded_data = jQuery(document).data('logos_uploaded') ? jQuery(document).data('logos_uploaded') : [];

        logos_uploaded_data.push(logo_uploaded);
        jQuery(document).data('logos_uploaded', logos_uploaded_data);
        var logos_uploaded_current = jQuery(document).data('logos_uploaded');

        var indice = 0;
        for (var logo in logos_uploaded_current) {
            logos_uploaded_current[logo].indice = indice++;
        }

        this.render_logos.render(logos_uploaded_current);
    };

    var _renderLogos = function(logos_uploaded) {
        if (!this.render_logos) {
            this.render_logos = Tempo.prepare('wrapper_logos_uploaded');
        }
        if (logos_uploaded) {
            this.render_logos.render(logos_uploaded);
            jQuery(document).data('logos_uploaded',logos_uploaded);
        }

    };

    var _removeLogo = function(filename) {
        var data_logos = jQuery(document).data('logos_uploaded');

        for (var logo in data_logos) {
            if (data_logos[logo].filename == filename) {
                data_logos.splice(logo, 1);
                break;
            }
        }
    };

    var _getLogosUploaded = function() {
        return jQuery(document).data('logos_uploaded') || [];
    };

    var _getLogoByFile = function(name) {
        var data_logos = jQuery(document).data('logos_uploaded');

        for (var logo in data_logos) {
            if (data_logos[logo].name == name) {
                return data_logos[logo];
                break;
            }
        }

        return null;

    };

    var _getTabCurrent = function() {
        return jQuery('#container_tabs').find('article.visible') || null;
    };

    return {
        renderLogos: _renderLogos,
        logoUploaded: _logoUploaded,
        getLogosUploaded: _getLogosUploaded,
        getLogoByFile: _getLogoByFile,
        getTabCurrent: _getTabCurrent,
        removeLogo: _removeLogo
    };
};

var smalldog_custom_jerseys_one = smalldog_custom_jerseys();
var wrapper;

var stepsDeNav;
var divSteps;
var actualStep;

//step2
var pestanhas;
var nombresPestanhas;
var miActualPestanha;

//team logo
var validoGrupo1;
var validoGrupo2;
var validoGrupo3;


//popups
var actualLogoTab;
var actualLogoIndex;

var popup1;
var popup2;
var popup3;
var popupActivo;

var template_jerseys;
var data_form = {};
var iFrames;

//popup2
var actualSponsorLogos;
var sponsorLogosArticle;

//popup3
var selectArtworkSection;
var nombreSeleccionado;

window.addEventListener("load", function load(event) {
    init();
}, false);

function init()
{
    wrapper = document.getElementById("wrapper_custom_jerseys");
    template_jerseys = "";
    if (wrapper) {

        iFrames = jQuery('#container_order iframe');
        divSteps = wrapper.getElementsByTagName("div")[0];
        stepsDeNav = wrapper.getElementsByTagName("nav")[0].getElementsByTagName("a");
        actualStep = 0;

        pestanhas = wrapper.getElementsByTagName("section")[1].getElementsByTagName("article");
        //nombresPestanhas = document.getElementById("nombresPestanhas").getElementsByTagName("a");

        miActualPestanha = 0;

        validoGrupo1 = 0;
        validoGrupo2 = 0;
        validoGrupo3 = 0;


        popup1 = document.getElementById("logoPopup1");
        popup2 = document.getElementById("logoPopup2");
        popup3 = document.getElementById("logoPopup3");

        actualSponsorLogos = [];
        sponsorLogosArticle = popup2.getElementsByTagName("article")[1];

        selectArtworkSection = document.getElementById("selectArtworkSection");

        jQuery(document).data('jerseys', []);
        jQuery(document).data('logos_uploaded', []);

        nombreSeleccionado = "";
    }
}


function loadStep(nuevoStep)
{

    var nuevoLeft = -100 * nuevoStep;
    divSteps.style.left = nuevoLeft + "%";

    for (var k = 0; k < stepsDeNav.length; k++)
    {
        var indexReal = stepsDeNav.length - k - 1;
        var soyStepLink = stepsDeNav[indexReal];
        soyStepLink.className = k == nuevoStep ? "activo" : "";
    }

}

function pasarAStep(nuevoStep, obj)
{

    if (nuevoStep == 1) {

        var $obj = jQuery(obj), is_action_design = $obj.is('#action_search_design'), type;
        var data = {action: 'woocommerce_custom_jerseys_ajax'};

        if (is_action_design == true) {
            var design_id = jQuery('#search_design_id').val();

            if (!design_id) {
                return false;
            }

            type = data.type = 'search_design';
            data.design_id = design_id;

        } else {
            if (!$obj.data('product_id')) {
                return false;
            }

            type = data.type = 'search_product';
            data.product_id = $obj.data('product_id');
        }

        jQuery(document).data('type_search', data.type);

        jQuery.ajax({
            type: "POST",
            url: custom_jersey_ajax.url,
            data: data,
            dataType: 'json'
        }).done(function(response) {
            if (response.success == 1) {
                loadStep(nuevoStep);
                jQuery('#container_tabs').hide().html(response.partial).show();
                nombresPestanhas = document.getElementById("nombresPestanhas").getElementsByTagName("a");
                template_jerseys = Tempo.prepare('wrapper_jerseys');

                if (response.logos) {
                    smalldog_custom_jerseys_one.renderLogos(response.logos);
                } else {
                    smalldog_custom_jerseys_one.renderLogos();
                }


                if (type == 'search_design') {
                    desbloquearNext(0);
                    jQuery(document).data('jerseys', response.jerseys);
                    template_jerseys.render(response.jerseys);

                    //smalldog_custom_jerseys_one.logoUploaded();


                    //smalldog_custom_jerseys_one.logoUploaded(file);
                }
            }
        });
    }

    if (nuevoStep == 2 && !obj) {
        loadStep(nuevoStep);
    }

    if (nuevoStep === 0) {
        init();
        jQuery('#container_tabs').empty();
        loadStep(nuevoStep);
    }

}




// ---------- PESTANHAS ------------------

function desbloquearNext(indexActualPestanha)
{
    var pestanhaActual = pestanhas[indexActualPestanha];
    var nextBoton = pestanhaActual.getElementsByTagName("a");

    nextBoton = nextBoton[nextBoton.length - 1];
    nextBoton.className = "activo";
}


function bloquearNext(indexActualPestanha)
{
    var pestanhaActual = pestanhas[indexActualPestanha];
    var nextBoton = pestanhaActual.getElementsByTagName("a");

    nextBoton = nextBoton[nextBoton.length - 1];
    nextBoton.className = "";

    var nombreActual = nombresPestanhas[indexActualPestanha];
    nombreActual.className = "visible";
}


function desbloquearPestanha(indexPestanha)
{
    var nombrePestanhaNueva = nombresPestanhas[indexPestanha];
    if (nombrePestanhaNueva.className == "")
    {
        nombrePestanhaNueva.className = "activo";
    }


    nombresPestanhas[miActualPestanha].className = "visible confirmado";
}



function DesbloquearYmostrarPestanha(nuevaPestanha)
{

    var grupoNext = pestanhas[miActualPestanha].getElementsByTagName("a");
    var soyNext = grupoNext[grupoNext.length - 1];
    var $tab_current = jQuery(pestanhas[miActualPestanha]);
    var type_search = jQuery(document).data('type_search');

    if (soyNext.className == "activo")
    {
        if ($tab_current[0].id == 'tab_colors') {
            var $current_variation = $tab_current.find('hgroup a.seleccionado');
            data_form = $current_variation.data('form_data') || [];
        }

        if ($tab_current[0].id == 'tab_text_font') {
            var params = [], logo_name = $tab_current.find('#jdt_team_name')[0].value;
            var data_logos = $tab_current.find((logo_name ? '#jdt_team_name' : '#jdt_logo_name')).data('logos');
            data_form.text_fonts = {};

            if (logo_name) {
                data_form.text_fonts['team_name'] = {position_id: data_logos.position_id, filename: logo_name, type: 'name'};
            } else {
                data_form.text_fonts['team_name'] = data_logos;
            }
            //data_form.text_fonts['position_id']['is_logo'] = logo_name ? false : true;
            data_form.text_fonts['back'] = $tab_current.find('#jdt_logo_position_back').val();
            data_form.text_fonts['front'] = $tab_current.find('#jdt_logo_position_front').val();
            data_form.text_fonts['sleeve'] = $tab_current.find('#jdt_logo_position_sleeve').val();
            data_form.text_fonts['comment'] = $tab_current.find('#jdt_comment_logo').val();
            data_form.text_fonts['font'] = $tab_current.find('#jdt_font').val();
            data_form.text_fonts['text_fill'] = $tab_current.find('#jdt_text_fill').val();
            data_form.text_fonts['outline'] = $tab_current.find('#jdt_outline_color').val();
        }

        if ($tab_current[0].id == 'tab_front_logos' || $tab_current[0].id == 'tab_back_logos' || $tab_current[0].id == 'tab_sleeve_logos') {
            var params = {}, $tab_logos = $tab_current.find('hgroup div.seleccionado');

            if (data_form.logos) {
                data_form.logos[$tab_current.data('type')] = {};
            } else {
                data_form.logos = {};
            }

            data_form.logos[$tab_current.data('type')] = jQuery.map($tab_logos, function(data, i) {
                return jQuery(data).data('logos');
            });
        }

        if ($tab_current[0].id == 'tab_jerseys') {
            var jerseys = jQuery(document).data('jerseys') || [];

            if (jerseys.length < 1) {
                return;
            }

            data_form.jerseys = jerseys;

            jQuery.ajax({
                type: "POST",
                url: custom_jersey_ajax.url,
                data: {action: 'woocommerce_custom_jerseys_ajax', type: 'save_order', custom_jerseys: data_form},
                dataType: 'json'
            }).done(function(response) {
                iFrames.attr("src", response.url_cart).load(function() {
                    pasarAStep(2, null);
                });
            });
        }

        console.log(data_form);

        setTimeout(function() {
            mostrarPestanhaDesbloqueada(nuevaPestanha);

            var is_validate_tab = jQuery(document).data('is_validate_tab') || 0;

            if (jQuery('#tab_text_font').hasClass('visible')) {
                if (type_search == 'search_design' && is_validate_tab == 0) {
                    jQuery(document).data('is_validate_tab', 1)
                    jQuery('#tab_text_font').find(':input').each(function() {

                        if (this.value && this.id == 'jdt_team_name') {
                            aumentarValidacionGrupo(0, 0);
                        }
                        if (this.value && this.id == 'jdt_logo_name') {
                            nombreSeleccionado = this.value;
                            aumentarValidacionGrupo(0, 1);
                        }
                        if (this.value && this.id == 'jdt_logo_position_back') {
                            aumentarValidacionGrupo(1, 0);
                        }
                        if (this.value && this.id == 'jdt_logo_position_front') {
                            aumentarValidacionGrupo(1, 1);
                        }
                        if (this.value && this.id == 'jdt_logo_position_sleeve') {
                            aumentarValidacionGrupo(1, 2);
                        }
                        if (this.value && this.id == 'jdt_font') {
                            aumentarValidacionGrupo(2, 0);
                        }
                        if (this.value && this.id == 'jdt_text_fill') {
                            aumentarValidacionGrupo(2, 1);
                        }
                        if (this.value && this.id == 'jdt_outline_color') {
                            aumentarValidacionGrupo(2, 2);
                        }

                    });

                }
            }

        }, 600);

        desbloquearPestanha(nuevaPestanha);
    }

}

function mostrarPestanhaDesbloqueada(nuevaPestanha)
{
    var nuevaNombrePestanha = nombresPestanhas[nuevaPestanha];


    if (nuevaNombrePestanha.className.indexOf("activo") != -1 || nuevaNombrePestanha.className.indexOf("confirmado") != -1)
    {

        var antiguaNombrePestanha = nombresPestanhas[miActualPestanha];
        if (antiguaNombrePestanha.className.indexOf("confirmado") != -1)
        {
            antiguaNombrePestanha.className = "confirmado";
        }
        else
        {
            antiguaNombrePestanha.className = "activo";
        }

        pestanhas[miActualPestanha].className = "";



        if (nuevaNombrePestanha.className.indexOf("confirmado") != -1)
        {

            nuevaNombrePestanha.className = "confirmado visible";

        }
        else
        {
            nuevaNombrePestanha.className = "visible";
        }

        pestanhas[nuevaPestanha].className = "visible";
        miActualPestanha = nuevaPestanha;
    }



}
// ---------- END PESTANHAS ------------------

// ---------- TEAM TAB ------------------------
function elegirLogoTeam()
{
    var divGrupo = pestanhas[1].getElementsByTagName("div")[0];
    var botonSubir = divGrupo.getElementsByTagName("a")[0];

    if (botonSubir.className == "")
    {

        mostrarPopup1(0, 1);

    }
    else
    {
        aumentarValidacionGrupo(0, 1);
    }

}

function aumentarValidacionGrupo(indexGrupo, indexControl)
{
    var divGrupo = pestanhas[1].getElementsByTagName("div")[indexGrupo];

    if (indexGrupo == 0)
    {
        var misInput = divGrupo.getElementsByTagName("input");

        if (indexControl == 0)
        {
            validoGrupo1 = misInput[0].value == "" ? 0 : 1;
        }


        else if (indexControl == 1)
        {
            var botonSubir = divGrupo.getElementsByTagName("a")[0];

            if (botonSubir.className == "")
            {
                validoGrupo1 = 1;

                misInput[0].value = "";
                misInput[0].style.opacity = "0";

                if (nombreSeleccionado)
                {
                    misInput[1].value = nombreSeleccionado;
                }
                else
                {
                    misInput[1].value = "MiTeamLogo.ai";
                }

                botonSubir.innerHTML = "Remove";
                botonSubir.className = "eliminar";
            }
            else
            {
                validoGrupo1 = 0;

                misInput[0].style.opacity = "1";
                misInput[1].value = "";

                botonSubir.innerHTML = "Select";
                botonSubir.className = "";

            }


        }

    }
    else if (indexGrupo == 1)
    {

        var selecciones = divGrupo.getElementsByTagName("select");
        validoGrupo2 = 0;

        for (var k = 0; k < selecciones.length; k++)
        {
            if (selecciones[k].selectedIndex > 0)
            {
                validoGrupo2++;
            }
        }

        var indexTabLogo;
        if (indexControl == 0)
        {
            indexTabLogo = 1;
        }
        else if (indexControl == 1)
        {
            indexTabLogo = 0;
        }
        else
        {
            indexTabLogo = indexControl;
        }

        habilitarODeshabilitarLogo(indexTabLogo + 2, selecciones[indexControl].selectedIndex - 1);

    }
    else if (indexGrupo == 2)
    {
        var selecciones = divGrupo.getElementsByTagName("select");
        validoGrupo3 = 0;

        for (var k = 0; k < selecciones.length; k++)
        {
            if (selecciones[k].selectedIndex > 0)
            {
                validoGrupo3++;
            }
        }
    }

    validarDesbloquearNextTeam();
}


function validarDesbloquearNextTeam()
{
    if (validoGrupo1 > 0 && validoGrupo2 > 0 && validoGrupo3 >= 3)
    {
        desbloquearNext(1);
    }
    else
    {
        bloquearNext(1);
    }
}

// ----------END TEAM TAB --------------------
// 
// ---------- SHIRT COLOR TAB ----------------

function elegirColor(indexColor)
{
    var colores = pestanhas[0].getElementsByTagName("hgroup")[0].getElementsByTagName("a");

    for (var k = 0; k < colores.length; k++)
    {
        colores[k].className = k == indexColor ? "seleccionado" : "";
    }

    var $color_current = jQuery(colores[indexColor]);
    var data_colors = $color_current.data('images');
    jQuery('#container_tabs').find('figure:eq(0) img').attr('src', data_colors.image_front);
    jQuery('#container_tabs').find('figure:eq(1) img').attr('src', data_colors.image_back);

    desbloquearNext(0);
}

// --------- END SHIRT COLOR TAB -------------

// --------- LOGOS TAB -----------------------
function confirmarElegirLogo(indexTabLogo, indexLogo)
{
    var logo = pestanhas[indexTabLogo].getElementsByTagName("hgroup")[0].getElementsByTagName("div")[indexLogo];

    if (logo.className != "disabled")
    {
        logo.className = "seleccionado";

        var miMensaje = logo.getElementsByTagName("p")[0];
        if (nombreSeleccionado)
        {
            miMensaje.innerHTML = nombreSeleccionado;
        }
        else
        {
            miMensaje.innerHTML = "ImagenABC.ai";
        }

    }


}

function elegirLogo(indexTabLogo, indexLogo)
{
    var logo = pestanhas[indexTabLogo].getElementsByTagName("hgroup")[0].getElementsByTagName("div")[indexLogo];


    if (logo.className != "disabled")
    {
        mostrarPopup1(indexTabLogo, indexLogo);

    }


}



function deselegirLogo(indexTabLogo, indexLogo)
{
    var logo = pestanhas[indexTabLogo].getElementsByTagName("hgroup")[0].getElementsByTagName("div")[indexLogo];
    logo.className = "";

    jQuery(logo).removeData('logos');
    var miMensaje = logo.getElementsByTagName("p")[0];
    miMensaje.innerHTML = "Select of Upload a file";

}


function habilitarODeshabilitarLogo(indexTabLogo, indexLogo)
{

    if (indexLogo >= 0)
    {

        var logos = pestanhas[indexTabLogo].getElementsByTagName("hgroup")[0].getElementsByTagName("div");

        for (var k = 0; k < logos.length; k++)
        {
            if (logos[k].className == "disabled")
            {
                logos[k].className = "";

                var miMensaje = logos[k].getElementsByTagName("p")[0];
                miMensaje.innerHTML = "Select of Upload a file";
            }

            if (k == indexLogo)
            {
                logos[k].className = "disabled";

                var miMensaje = logos[k].getElementsByTagName("p")[0];
                miMensaje.innerHTML = "Unavaliable";
            }


        }



    }

    var soyNombre = nombresPestanhas[indexTabLogo];

    if (soyNombre.className.indexOf("confirmado") != -1)
    {

        soyNombre.className = "activo";

    }

}


// ------- END LOGOS TAB ---------------------




//----------- JERSEYS TAB --------------------

function mostrarNewJersey(idJersey)
{
    var grupoJersey = document.getElementById(idJersey);
    var misDivs = grupoJersey.getElementsByTagName("div");

    if (idJersey != 'newJersey') {
        var size = jQuery(grupoJersey).find('div:eq(0) span:eq(2)').text();

        //jQuery(grupoJersey).find('a.selectBox').remove();
        jQuery(grupoJersey).find('select option[value="' + size + '"]').attr("selected", true);
//        jQuery(grupoJersey).find('select').selectBox({
//            'menuTransition': 'fade',
//            'menuSpeed': 'fast'
//        });
    }

    misDivs[1].className = "visible";
}


function ocultarNewJersey(idJersey)
{
    var grupoJersey = document.getElementById(idJersey);

    var misDivs = grupoJersey.getElementsByTagName("div");

    misDivs[1].className = "";


}


function eliminarJersey(idJersey, indice)
{
    var grupoJersey = document.getElementById(idJersey);
    var jerseys_data = jQuery(document).data('jerseys'), jersey_data_write = [];

    for (var jersey in jerseys_data) {
        if (parseInt(jerseys_data[jersey].indice) == parseInt(indice)) {
            jerseys_data.splice(jersey, 1);
            grupoJersey.remove();
            break;
        }
    }

    var p_indice = jerseys_data.length;
    for (var index in  jerseys_data) {
        jerseys_data[index].indice = p_indice--;
        jersey_data_write.push(jerseys_data[index]);
    }

    template_jerseys.render(jersey_data_write);

}

function saveEditarJersey(idJersey)
{
    var indice = parseInt(idJersey.replace(/[^\d]/g, ''));
    var grupoJersey = document.getElementById(idJersey);
    var $new_jersey_field = jQuery(grupoJersey).find(':input'), jersey = {};
    var jerseys_data = jQuery(document).data('jerseys'), jersey_data_write = [];

    jersey.name = $new_jersey_field[0].value;
    jersey.number = $new_jersey_field[1].value;
    jersey.size = $new_jersey_field[2].value;

    if (!jersey.name || !jersey.number || !jersey.size) {
        return;
    }

    for (var index in jerseys_data) {
        if (parseInt(jerseys_data[index].indice) == parseInt(indice)) {
            jerseys_data[index].name = jersey.name;
            jerseys_data[index].number = jersey.number;
            jerseys_data[index].size = jersey.size;
        }

        jersey_data_write.push(jerseys_data[index]);
    }

    jQuery(document).data('jerseys', jersey_data_write);
    template_jerseys.render(jersey_data_write);

}

function agregarNuevoJersey()
{
    ocultarNewJersey("newJersey");

    setTimeout(function() {

        var listaJerseys = pestanhas[5].getElementsByTagName("div")[0];
        var hgroupJerseys = listaJerseys.getElementsByTagName("hgroup");
        var $new_jersey_field = jQuery(hgroupJerseys[0]).find(':input'), jersey = {};
        var jerseys_data = jQuery(document).data('jerseys') ? jQuery(document).data('jerseys') : [];

        jersey.indice = listaJerseys.children.length;
        jersey.name = $new_jersey_field[0].value;
        jersey.number = $new_jersey_field[1].value;
        jersey.size = $new_jersey_field[2].value;

        if (!jersey.name || !jersey.number || !jersey.size) {
            return;
        }

        jerseys_data.push(jersey);
        jQuery(document).data('jerseys', jerseys_data);

        jerseys_data.sort(function(obj1, obj2) {
            return obj2.indice - obj1.indice;
        });

        template_jerseys.render(jerseys_data);

        setTimeout(function() {
            $new_jersey_field[0].value = '';
            $new_jersey_field[1].value = '';
            $new_jersey_field[2].selectedIndex = 0;
            // jQuery($new_jersey_field[2]).selectBox('refresh');

            hgroupJerseys[1].className = "";

            validarConfirmJersey();
        }, 200);

    }, 400);


}



function validarConfirmJersey()
{
    var listaJerseys = pestanhas[5].getElementsByTagName("div")[0];
    var hgroupJerseys = listaJerseys.getElementsByTagName("hgroup");

    var confirmButton = pestanhas[5].getElementsByTagName("a");
    confirmButton = confirmButton[confirmButton.length - 1];

    confirmButton.className = hgroupJerseys.length > 1 ? "activo" : "";



}

// ------------- END JERSEYS TAB ----------------






// ------------ POPUPS -------------

function mostrarPopup1(indexTab, indexLogo)
{
    popup1.className = "popUpVisible";
    popupActivo = popup1;

    actualLogoTab = indexTab;
    actualLogoIndex = indexLogo;
}


function mostrarPopup2()
{
    if (popupActivo)
    {
        ocultarPopUpGeneral();
    }

    popup2.className = "popUpVisible";
    popupActivo = popup2;
}


function mostrarPopup3()
{
    if (popupActivo)
    {
        ocultarPopUpGeneral();
    }

    popup3.className = "popUpVisible";
    popupActivo = popup3;
}


function escogerSponsorLogo(indexSponsor)
{
    var sponsors = popup2.getElementsByTagName("article")[1].getElementsByTagName("a");

    for (var k = 0; k < sponsors.length; k++)
    {
        sponsors[k].className = k == indexSponsor ? "seleccionado" : "";
    }

    var boton = popup2.getElementsByTagName("a");
    boton = boton[boton.length - 1];

    if (boton.className != "activo")
    {
        boton.className = "activo";
    }

}


function cambiarPaginaSponsorLogos(indexPagina)
{
    //llenar actualSponsorLogos;

    var paginas = popup2.getElementsByTagName("article")[0].getElementsByTagName("a");
    var fragmento = document.createDocumentFragment();
    var boton = popup2.getElementsByTagName("a"), actualSponsorLogos = [];

    jQuery.ajax({
        type: "POST",
        url: custom_jersey_ajax.url,
        data: {action: 'woocommerce_custom_jerseys_ajax', type: 'sponsor_logos', page: indexPagina},
        dataType: 'json'
    }).done(function(response) {

        var data_sponsor_logos = response.sponsor_logos;
        for (var logo in data_sponsor_logos) {
            actualSponsorLogos[logo] = [];
            actualSponsorLogos[logo][0] = data_sponsor_logos[logo].filename;
            actualSponsorLogos[logo][1] = data_sponsor_logos[logo].name;
            actualSponsorLogos[logo][2] = data_sponsor_logos[logo].id;
        }

        if (data_sponsor_logos.length == 0) {
            actualSponsorLogos = [];
        }

        for (var w = 0; w < paginas.length; w++)
        {
            paginas[w].className = w == indexPagina ? "activo" : "";
        }

        sponsorLogosArticle.innerHTML = "";

        for (var k = 0; k < actualSponsorLogos.length; k++)
        {
            var logo = document.createElement("a");
            logo.href = "javascript:escogerSponsorLogo(" + k + ");";
            logo.setAttribute('data-sponsor_id', actualSponsorLogos[k][2]);

            var logoFigure = document.createElement("figure");
            var image = document.createElement("img");
            image.src = actualSponsorLogos[k][0];
            logoFigure.appendChild(image);
            logo.appendChild(logoFigure);

            var logoNombre = document.createElement("h1");
            logoNombre.appendChild(document.createTextNode(actualSponsorLogos[k][1]));
            logo.appendChild(logoNombre);

            fragmento.appendChild(logo);
        }

        sponsorLogosArticle.appendChild(fragmento);

        boton = boton[boton.length - 1];

        boton.className = "";
    });

}

function validarSponsor()
{
    var boton = popup2.getElementsByTagName("a");
    boton = boton[boton.length - 1], $sponsorCurrent = jQuery('#wrapper_sponsor_logos a.seleccionado');
    nombreSeleccionado = $sponsorCurrent.find('h1').text();

    var data_sponsor_logos = {
        sponsor_id: $sponsorCurrent.data('sponsor_id'),
        name: nombreSeleccionado,
        filename: $sponsorCurrent.find('img').attr('src'),
        type: 'logo'
    };

    if (boton.className == "activo")
    {
        confirmarUpload();

        if (actualLogoTab >= 2)
        {
            var fileupload_logo = pestanhas[actualLogoTab].getElementsByTagName("hgroup")[0].getElementsByTagName("div")[actualLogoIndex];
            var data_logos = jQuery(fileupload_logo).data('logos');
            jQuery.extend(data_logos, data_sponsor_logos);


//            data_logos.sponsor_id = $sponsorCurrent.data('sponsor_id');
//            data_logos.name = nombreSeleccionado;
//            data_logos.filename = $sponsorCurrent.find('img').attr('src');
//            $(fileupload_logo).data('logos', data_logos);
        } else {
            var data_logos = jQuery('#jdt_logo_name').data('logos');
            jQuery.extend(data_logos, data_sponsor_logos);
        }
    }
}

function aceptarTerminos()
{
    var elDiv = popup3.getElementsByTagName("div")[0].getElementsByTagName("div")[0];

    elDiv.className = "desplazado";

}

function ocultarPopUpGeneral()
{
    popupActivo.className = "";
    popupActivo = 0;
}

function confirmarUpload()
{
    ocultarPopUpGeneral();

    if (actualLogoTab >= 2)
    {
        confirmarElegirLogo(actualLogoTab, actualLogoIndex);
    }
    else
    {
        aumentarValidacionGrupo(actualLogoTab, actualLogoIndex);
    }

}

function seleccionarLogoEnPopup3(indexLogo)
{
    var listaLogos = selectArtworkSection.getElementsByTagName("ul")[0].getElementsByTagName("a");

    for (var k = 0; k < listaLogos.length; k++)
    {
        listaLogos[k].className = k == indexLogo ? "seleccionado" : "";
    }

    var accionesLogos = selectArtworkSection.getElementsByClassName("desactivado");

    for (var k = 0; k < accionesLogos.length; k)
    {
        accionesLogos[k].className = "activo";
    }
}


function eliminarLogoEnPopup3()
{
    var logoAEliminar = selectArtworkSection.getElementsByTagName("ul")[0].getElementsByClassName("seleccionado")[0];
    var ulLogos = selectArtworkSection.getElementsByTagName("ul")[0];
    var filename = jQuery(logoAEliminar).data('filename');

    ulLogos.removeChild(logoAEliminar);
    smalldog_custom_jerseys_one.removeLogo(filename);

    var listaLogos = ulLogos.getElementsByTagName("a");

    for (var k = 0; k < listaLogos.length; k++)
    {
        listaLogos[k].href = "javascript:seleccionarLogoEnPopup3(" + k + ");";
    }

    var accionesLogos = selectArtworkSection.getElementsByClassName("activo");

    for (var k = 0; k < accionesLogos.length; k)
    {
        accionesLogos[k].className = "desactivado";
    }
}


function ConfirmarSeleccionLogoEnPopup3()
{
    var filaSeleccionada = selectArtworkSection.getElementsByTagName("ul")[0].getElementsByClassName("seleccionado")[0];
    nombreSeleccionado = filaSeleccionada.getElementsByTagName("span")[0].innerHTML;
    var logo = smalldog_custom_jerseys_one.getLogoByFile(nombreSeleccionado);

    ocultarPopUpGeneral();
    var data_logo_new = {
        size: logo.size,
        filename: logo.filename_codify,
        type: 'logo'
    };

    if (actualLogoTab >= 2)
    {
        confirmarElegirLogo(actualLogoTab, actualLogoIndex);
        var fileupload_logo = pestanhas[actualLogoTab].getElementsByTagName("hgroup")[0].getElementsByTagName("div")[actualLogoIndex];
        var data_logos = jQuery(fileupload_logo).data('logos');
        jQuery.extend(data_logos, data_logo_new);
        /*var data_logos = jQuery(fileupload_logo).data('logos');
         
         data_logos.filename = logo.filename_codify;
         jQuery(fileupload_logo).data('logos', data_logos);*/
    }
    else
    {
        aumentarValidacionGrupo(actualLogoTab, actualLogoIndex);
        var data_logos = jQuery('#jdt_logo_name').data('logos');
        jQuery.extend(data_logos, data_logo_new);
        //jQuery('#jdt_logo_name').data('logos', logo.filename);
    }
}

// --------- END POPUPS ------------



function mostrarLoadingDiv(elementoPadre)
{
    var l = document.createElement("div");
    l.className = "loadingDiv";

    elementoPadre.appendChild(l);
}

function ocultarLoadingDiv(elementoPadre)
{
    var loadings = elementoPadre.getElementsByClassName("loadingDiv");

    for (var k = 0; k < loadings.length; k)
    {
        elementoPadre.removeChild(loadings[k]);
    }

}
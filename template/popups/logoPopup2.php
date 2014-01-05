<?php

$sponsor_logos = woocommerce_custom_jerseys_sponsor_logos();

 ?>
<section id="logoPopup2" class="">
    <hgroup>

        <header>
            <h1>
                Select a Sponsor Logo
            </h1>

            <a href="javascript:ocultarPopUpGeneral();">
                X
            </a>
        </header>

        <article id="wrapper_pagination_sponsor_logos">
            <a href="javascript:cambiarPaginaSponsorLogos(0);" class="activo">A-E</a>
            <a href="javascript:cambiarPaginaSponsorLogos(1);">F-J</a>
            <a href="javascript:cambiarPaginaSponsorLogos(2);">K-O</a>
            <a href="javascript:cambiarPaginaSponsorLogos(3);">P-T</a>
            <a href="javascript:cambiarPaginaSponsorLogos(4);">U-Z</a>
        </article>

        <article id="wrapper_sponsor_logos">
            <?php foreach ($sponsor_logos as $key => $logo): ?>
                <a data-sponsor_id="<?php echo $logo->id; ?>"  href="javascript:escogerSponsorLogo(<?php echo $key; ?>);">
                    <figure><img src="<?php echo $logo->filename; ?>" /></figure>
                    <h1><?php echo $logo->name; ?></h1>
                </a>
            <?php endforeach; ?>
        </article>
        <a href="javascript:validarSponsor();">
            Save / Select
        </a>

    </hgroup>
</section>
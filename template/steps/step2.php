
<?php
extract($array_data_products);
?>

<header>
  <figure>
    <h1>FRONT</h1>
    <img src="<?php echo ($is_search_design ? $dbr_team->image_front : ''); ?>">
  </figure>
  <figure>
    <h1>BACK</h1>
    <img src="<?php echo ($is_search_design ? $dbr_team->image_back : ''); ?>">
  </figure>
</header>
<hgroup id="nombresPestanhas">
  <a class="<?php echo ($is_search_design ? 'confirmado visible' : 'visible'); ?>" href="javascript:mostrarPestanhaDesbloqueada(0);" >
    <span>&#10003;</span>
    <h1>Colors</h1>
  </a>
  <a  href="javascript:mostrarPestanhaDesbloqueada(1);">
    <span>&#10003;</span>
    <h1>Text & Fonts</h1>
  </a>
  <a  href="javascript:mostrarPestanhaDesbloqueada(2);">
    <span>&#10003;</span>
    <h1>Front Logos</h1>
  </a>
  <a href="javascript:mostrarPestanhaDesbloqueada(3);">
    <span>&#10003;</span>
    <h1>Back Logos</h1>
  </a>
  <a href="javascript:mostrarPestanhaDesbloqueada(4);">
    <span>&#10003;</span>
    <h1>Sleeve Logos</h1>
  </a>
  <a href="javascript:mostrarPestanhaDesbloqueada(5);">
    <span>&#10003;</span>
    <h1>Jerseys</h1>
  </a>
</hgroup>

<div>
<?php
/**
 * woocommerce_single_product_summary hook
 *
 * @hooked teamLogoTab
 * @hooked frontLogosTab
 * @hooked backLogosTab
 * @hooked sleeveLogosTab
 * @hooked jerseysTab
 * 
 */
do_action('woocommerce_custom_jerseys_tool_two', $array_data_products);
?>
</div>

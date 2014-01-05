<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $woocommerce, $product, $post, $wc_custom_jerseys_team, $wc_custom_jerseys_position;
?>
<section id = "container_jerseys">
    <article>
        <header>
            Select a style
        </header>
        <?php $dbl_products = $wc_custom_jerseys_team->getProducts(); ?>
        <?php foreach ($dbl_products as $dbr_product): ?>
            <?php $params_product = array('product_id' => $dbr_product['id']); ?>
            <hgroup>
                <a data-product_id='<?php echo $dbr_product['id']; ?>' onclick="javascript:pasarAStep(1, this);return false;">
                    <img src="<?php echo $dbr_product['image'][0]; ?>" alt="Placeholder" />
                </a>
                <h1>
                    <?php echo  $dbr_product['currency_symbol'] ." ". $dbr_product['price']; ?>
                </h1>
            </hgroup>
        <?php endforeach; ?>
    </article>
    <article>
        <header>
            or load a Design ID
        </header>
        <hgroup>
            <input name="search[design_id]" id="search_design_id" type="text" placeholder="Enter your Design ID"/>
            <a id="action_search_design" onclick="javascript:pasarAStep(1, this); return false;">LOAD</a>
        </hgroup>
    </article>
</section>
<section id="container_tabs"></section>
<section id="container_order">
    <iframe id="iframe_cart" width="100%"  scrolling="auto" src="" frameborder="0"></iframe>
</section>
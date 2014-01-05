<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $post, $woocommerce, $wc_custom_jerseys_team, $wc_custom_jerseys_position;


/*$array = array(
    'product_id' => 22,
    'product_variation_id' => 22,
    'text_fonts' => array('name_logo'=>'name', 'file_name'=>'','logo_position'=>'B1,F1,F10', 'font' => 'fuente', 'text_fill'=>'text_fill', 'outline'=> 'outline'),
    'logos' => array(
        array('filename' => 'ruta_imagen', 'logo_position'=>'F1', 'type' =>'FRONT', 'comment' => 'comentario'),
        array('filename' => 'ruta_imagen', 'logo_position'=>'B2', 'type' =>'BACK', 'comment' => 'comentario'),
    ),

    'jerseys '=> array(
        array('name' => 'Pepe', 'number' => '15', 'size'=>'S'),
        array('name' => 'Pepe', 'number' => '15', 'size'=>'S'),
        array('name' => 'Pepe', 'number' => '15', 'size'=>'S')
    )
);

echo json_encode($array);*/


?>
<div id ="wrapper_product">
    <?php $data_product_variations = $wc_custom_jerseys_team->getProductVariations(); ?>
    <?php foreach ($data_product_variations as $_product_variation): ?>
        <?php

        $_SESSION['current_id'][$_product_variation['product_id']] = $wc_custom_jerseys_team->generateDesignId() ;
        $array_data_products['product_id'] = $_product_variation['product_id'];
        $array_data_products['gallery'] = $wc_custom_jerseys_team->getGalleryByProduct($_product_variation['product']);
        $array_data_products['variations'] = $wc_custom_jerseys_team->getVariationsByProduct($_product_variation['product']);
        $array_data_products['positions'] = $wc_custom_jerseys_position->getPositionByProductId($_product_variation['product_id']);
        ?>

        <div data-products ='<?php echo json_encode($array_data_products); ?>'>
            <img src="<?php echo $_product_variation['featured_picture']; ?>" alt="Placeholder" />
            <h3><?php echo $_product_variation['title']; ?></h3>
            <span class="price"><?php echo $_product_variation['currency_symbol'] . " " . $_product_variation['price']; ?></span>           
        </div>
    <?php endforeach; ?>
</div>
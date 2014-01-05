<?php
if (!defined('ABSPATH'))
  exit; // Exit if accessed directly

global $wc_custom_jerseys_team;
?>
<div id ="wrapper_custom_jerseys" >

  <nav>
    <h1>
      CUSTOM JERSEYS
    </h1>


    <hgroup>		
      <a onclick="javascript:pasarAStep(2, this);
                            return false;">
        <h2>3</h2>
        <h3>CHECK OUT</h3>
      </a>

      <a onclick="javascript:pasarAStep(1, this);
                            return false;">
        <h2>2</h2>
        <h3>DESIGN JERSEY</h3>
      </a>

      <a onclick="javascript:pasarAStep(0, this);
                            return false;" class="activo">
        <h2>1</h2>
        <h3>SELECT A STYLE</h3>
      </a>

    </hgroup>
  </nav>



  <div>

    <?php
    /**
     * woocommerce_single_product_summary hook
     *
     * @hooked step1
     * @hooked woocommerce_output_step2
     * @hooked woocommerce_output_step3
     * @hooked woocommerce_output_step4
     * 
     */
    do_action('woocommerce_custom_jerseys_tool_one');
    ?>


  </div>


  <?php
  /**
   * woocommerce_single_product_summary hook
   *
   * @hooked woocommerce_output_logoPopup1
   * @hooked woocommerce_output_logoPopup2
   * @hooked woocommerce_output_logoPopup3
   * 
   */
  do_action('woocommerce_custom_jerseys_tool_three');
  ?>


</div>
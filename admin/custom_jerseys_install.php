<?php

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

  load_plugin_textdomain('woocommerce-custom-jerseys', false, dirname(plugin_basename(__FILE__)) . '/languages/');

  class WC_Custom_Jersey
  {

    public function __construct()
    {
      $this->custom_jerseys_enabled = get_option('woocommerce_custom_jerseys_enable', true);

      add_action('init', array($this, 'plugin_init'));

      add_option('woocommerce_custom_jerseys_error_message', 'Sorry you cannot access here!');
    }

    function plugin_init()
    {
      global $woocommerce;

      if ($this->custom_jerseys_enabled) {

        if (!session_id())
          session_start();

        add_shortcode('custom_jerseys', array($this, 'createTemplate'));
      }
    }

    function createTemplate()
    {
      $out = get_option('woocommerce_custom_jerseys_error_message', 'Sorry you cannot access here');
      $user = wp_get_current_user();

      /* if (empty($user->ID)) {
        echo $out;
        } else { */
      include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/custom_jerseys_tools.php');
      //}
    }

    function customJerseysInstall()
    {
      global $wpdb;

      $collate = '';
      if ($wpdb->has_cap('collation')) {
        if (!empty($wpdb->charset))
          $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
        if (!empty($wpdb->collate))
          $collate .= " COLLATE $wpdb->collate";
      }

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

      $query = "CREATE TABLE {$wpdb->prefix}custom_jerseys_font_colors (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(80) NOT NULL,
                type_font varchar(45) NOT NULL DEFAULT 'font',
                status tinyint(1) NOT NULL DEFAULT '1',
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_position (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(15) NOT NULL,
                type varchar(20) NOT NULL DEFAULT 'FRONT',
                status tinyint(1) NOT NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_product_font_color (
                id int(11) NOT NULL AUTO_INCREMENT,
                font_colors_id int(11) NOT NULL,
                product_id bigint(20) NOT NULL,
                status tinyint(1) DEFAULT '1',
                created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT NULL,
                PRIMARY KEY (id),
                KEY font_colors_id (font_colors_id),
                KEY product_id (product_id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_product_position (
                id int(11) NOT NULL AUTO_INCREMENT,
                position_id int(11) NOT NULL,
                product_id bigint(20) NOT NULL,
                side varchar(15) DEFAULT NULL,
                for_logo_position tinyint(1) DEFAULT '0',
                PRIMARY KEY (id),
                KEY position_id (position_id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_shirt_color (
                id int(11) NOT NULL AUTO_INCREMENT,
                color_left varchar(15) NOT NULL,
                color_right varchar(15) NOT NULL,
                slug_attribute varchar(200) NOT NULL,
                status tinyint(1) DEFAULT '1',
                PRIMARY KEY (id),
                UNIQUE KEY slug_attribute_UNIQUE (slug_attribute)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_sponsor_logo (
                id int(11) NOT NULL AUTO_INCREMENT,
                filename varchar(150) DEFAULT NULL,
                hash_filename varchar(255) DEFAULT NULL,
                name varchar(45) DEFAULT NULL,
                letter varchar(1) DEFAULT NULL,
                status tinyint(1) DEFAULT '1',
                PRIMARY KEY (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_team (
                id int(11) NOT NULL AUTO_INCREMENT,
                design_id varchar(60) NOT NULL,
                order_id bigint(20) NOT NULL,
                product_shirt_color_id bigint(20) NOT NULL,
                created_by bigint(20) NOT NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY design_id_UNIQUE (design_id),
                KEY order_id (order_id),
                KEY design_id (design_id),
                KEY product_shirt_color_id (product_shirt_color_id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_team_meta (
                id int(11) NOT NULL AUTO_INCREMENT,
                team_id int(11) NOT NULL,
                team_meta_key varchar(100) NOT NULL,
                team_meta_value LONGTEXT  NOT NULL,
                team_meta_tab varchar(15) DEFAULT NULL,
                PRIMARY KEY (id),
                KEY team_id (team_id),
                KEY team_meta_key (team_meta_key)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_product_shirt_color (
                id int(11) NOT NULL AUTO_INCREMENT,
                shirt_color_id int(11) DEFAULT NULL,
                product_id bigint(20) NOT NULL,
                image_front varchar(255) NOT NULL,
                image_back varchar(255) NOT NULL,
                PRIMARY KEY (id),
                KEY product_id (product_id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}custom_jerseys_team_logos (
                id int(11) NOT NULL AUTO_INCREMENT,
                team_id int(11) NOT NULL,
                filename varchar(255) NOT NULL,
                name varchar(100) NOT NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
                ) $collate ;";

      dbDelta($query);

      $upload_array = wp_upload_dir();
      $upload_dir_temp = $upload_array['basedir'] . '/' . $directory . '/';

      WC_Custom_Jersey_Functions::createDirLogos($upload_dir_temp . '/files_temp/');
      WC_Custom_Jersey_Functions::createDirLogos($upload_dir_temp . '/files_custom_jerseys/');
      WC_Custom_Jersey_Functions::create_page(esc_sql('custom_jerseys'), 'woocommerce_custom_jerseys_page_id', __('Custom Jerseys', 'woo_custom_jerseys'), '[custom_jerseys]');

      WC_Custom_Jerseys_Position::setSettings();
      WC_Custom_Jerseys_Font_Color::setSettings();
      WC_Custom_Jerseys_Shirt_Color::setSettings();

      $files = WC_Custom_Jersey_Functions::readFiles('sponsor_logos');
      if (count($files)) {
        WC_Custom_Jersey_Functions::registerSponsorLogos($files);
      }
    }

    public function customJerseysUninstall()
    {
      global $wpdb;

      wp_delete_post(get_option('woocommerce_custom_jerseys_error_message'), true);
      wp_delete_post(get_option('woocommerce_custom_jerseys_page_id'), true);

      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_team_font");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_font_colors");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_logo");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_logo_position");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_order");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_position");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_product_position");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_shirt_color");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_team");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_product_font_color");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_sponsor_logo");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_team_post");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_team_meta");
      $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "custom_jerseys_product_shirt_color");

      // Delete options
      $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'woocommerce_custom_jerseys_%';");
    }

  }

  $GLOBALS['wc_custom_jersey'] = new WC_Custom_Jersey();
}

if (!function_exists('woocommerce_output_custom_jerseys_step1')) {

  function woocommerce_output_custom_jerseys_step1()
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/steps/step1.php');
  }

}

if (!function_exists('woocommerce_output_custom_jerseys_step2')) {

  function woocommerce_output_custom_jerseys_step2()
  {

    global $wc_custom_jerseys_team, $wc_custom_jerseys_font_color, $wc_custom_jerseys_position, $wc_custom_jerseys_shirt_color;

    $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;
    $array_data_products = array();

    if ($type == 'search_product') {
      $product_id = isset($_REQUEST['product_id']) && $_REQUEST['product_id'] ? $_REQUEST['product_id'] : null;
      if (!$product_id) {
        return null;
      }
    }

    if ($type == 'search_design') {
      $design_id = isset($_REQUEST['design_id']) && $_REQUEST['design_id'] ? $_REQUEST['design_id'] : null;
      if (!$design_id) {
        return null;
      }

      $dbr_team = $wc_custom_jerseys_team->getTeamByDesingId($design_id);

      if (!$dbr_team) {
        return false;
      }

      $product_id = $dbr_team->product_id;
    }

    if (function_exists('get_product')) {
      $_product = get_product($product_id);
    } else {
      $_product = new WC_Product($product_id);
    }

    $colors = $wc_custom_jerseys_shirt_color->getshirtColorByProductId($product_id);
    $data_size_values = get_the_terms($product_id, 'pa_size');
    $_SESSION['current_id'][$product_id] = $design_id_new = $wc_custom_jerseys_team->generateDesignId();

    $array_data_products['product_id'] = $product_id;
    $array_data_products['size_values'] = $data_size_values;
    $array_data_products['colors'] = $colors;
    $array_data_products['font_colors'] = $wc_custom_jerseys_font_color->getFontColor($product_id);
    $array_data_products['positions'] = $wc_custom_jerseys_position->getPositionByProductId($product_id);
    $array_data_products['is_search_design'] = false;

    if ($type == 'search_design') {
      $array_data_products['is_search_design'] = true;
      $array_data_products['dbr_team'] = $dbr_team;
      $array_data_products['dbl_team_meta'] = $dbl_team_meta = $wc_custom_jerseys_team->getTeamMetaByTeamId($dbr_team->id);
      $dbl_team_logos = $wc_custom_jerseys_team->getLogosByTeamId($dbr_team->id);

      $upload_dir = wp_upload_dir();
      $dir_source = $upload_dir['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_LOGOS . '/' . $dbr_team->design_id;
      $dir_dest = $upload_dir['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_TEMP . '/' . $design_id_new;

      WC_Custom_Jersey_Functions::createDirLogos($dir_dest);
      $wc_custom_jerseys_team->copyLogos($dir_source, $dir_dest);

      $data_jerseys = array();
      $data_logos = array();
      foreach ($dbl_team_meta as $dbr_team_meta) {
        if ($dbr_team_meta->team_meta_tab != 'jerseys') {
          continue;
        }

        $data_jerseys = json_decode($dbr_team_meta->team_meta_value, true);
      }

      foreach ($dbl_team_logos as $key => $logo) {
        $logo->filename_codify = base64_encode($logo->name);
        $logo->indice = $key;
        $data_logos[] = $logo;
      }
    }

    ob_start();
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/steps/step2.php');
    $include_step = ob_get_clean();

    $response['html'] = $include_step;
    if ($type == 'search_design') {
      $response['jerseys'] = $data_jerseys;
      $response['logos'] = $data_logos;
    }

    return $response;
  }

}

if (!function_exists('woocommerce_output_custom_jerseys_step3')) {

  function woocommerce_output_custom_jerseys_step3()
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/steps/step3.php');
  }

}

if (!function_exists('woocommerce_output_custom_jerseys_step4')) {

  function woocommerce_output_custom_jerseys_step4()
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . "/template/steps/step4.php");
  }

}

if (!function_exists('woocommerce_output_custom_colors')) {

  function woocommerce_output_custom_colors($array_data_products)
  {

    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/tabs/colorsTab.php');
  }

}


if (!function_exists('woocommerce_output_custom_team_logos')) {

  function woocommerce_output_custom_team_logos($array_data_products)
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/tabs/teamLogoTab.php');
  }

}

if (!function_exists('woocommerce_output_custom_from_logos')) {

  function woocommerce_output_custom_from_logos($array_data_products)
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/tabs/frontLogosTab.php');
  }

}

if (!function_exists('woocommerce_output_custom_back_logos')) {

  function woocommerce_output_custom_back_logos($array_data_products)
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/tabs/backLogosTab.php');
  }

}

if (!function_exists('woocommerce_output_custom_sleeve_logos')) {

  function woocommerce_output_custom_sleeve_logos($array_data_products)
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/tabs/sleeveLogosTab.php');
  }

}

if (!function_exists('woocommerce_output_custom_jersey_tab')) {

  function woocommerce_output_custom_jersey_tab($array_data_products)
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/tabs/jerseysTab.php');
  }

}

if (!function_exists('woocommerce_output_custom_jerseys_jersey_poput_one')) {

  function woocommerce_output_custom_jerseys_jersey_poput_one()
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/popups/logoPopup1.php');
  }

}

if (!function_exists('woocommerce_output_custom_jerseys_jersey_poput_two')) {

  function woocommerce_output_custom_jerseys_jersey_poput_two()
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/popups/logoPopup2.php');
  }

}

if (!function_exists('woocommerce_output_custom_jerseys_jersey_poput_three')) {

  function woocommerce_output_custom_jerseys_jersey_poput_three()
  {
    include(WOO_CUSTOM_JERSEYS_FILE_PATH . '/template/popups/logoPopup3.php');
  }

}


if (!function_exists('enqueue_scripts')) {

  function enqueue_scripts()
  {

    wp_enqueue_style('style_custom_jerseys', WOO_CUSTOM_JERSEYS_CSS_URL . '/jdt.css');
    wp_enqueue_style('jquery-fileupload-style', WOO_CUSTOM_JERSEYS_CSS_URL . '/style.css');
    wp_enqueue_style('jquery-fileupload-ui-style', WOO_CUSTOM_JERSEYS_CSS_URL . '/jquery.fileupload.css');


    wp_enqueue_script('tempo', WOO_CUSTOM_JERSEYS_JS_URL . '/tempo.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-ui-widget', WOO_CUSTOM_JERSEYS_JS_URL . '/jquery.ui.widget.js', array('jquery'), '', true);

    wp_enqueue_script('jquery-iframe-transport-script', WOO_CUSTOM_JERSEYS_JS_URL . '/jquery.iframe-transport.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-fileupload-script', WOO_CUSTOM_JERSEYS_JS_URL . '/jquery.fileupload.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-fileupload-process', WOO_CUSTOM_JERSEYS_JS_URL . '/jquery.fileupload-process.js', array('jquery'), '', true);
    wp_enqueue_script('jquery-fileupload-validate', WOO_CUSTOM_JERSEYS_JS_URL . '/jquery.fileupload-validate.js', array('jquery'), '', true);

    wp_enqueue_script('script_custom_jerseys_functions', WOO_CUSTOM_JERSEYS_JS_URL . '/custom_jersey_functions.js', array('jquery'), '', true);
    wp_enqueue_script('script_custom_jerseys', WOO_CUSTOM_JERSEYS_JS_URL . '/jdt.js', array('jquery'), '', true);
    wp_enqueue_script('add_script_custom_jerseys', WOO_CUSTOM_JERSEYS_JS_URL . '/custom_jersey_tool.js', array(), false, true);
    wp_localize_script('add_script_custom_jerseys', 'custom_jersey_ajax', array('url' => admin_url('admin-ajax.php')));
  }

}

function woocommerce_custom_jerseys_save_upload_file()
{
  require WOO_CUSTOM_JERSEYS_FILE_PATH . '/class/UploadHandler.php';
  global $wc_custom_jerseys_team;

  $a_response = array();

  $product_id = isset($_REQUEST['product_id']) && $_REQUEST['product_id'] ? $_REQUEST['product_id'] : null;

  $a_response['success'] = 0;
  if (!$product_id) {
    $a_response['message'] = 'Error al subir la imagen';
    return $a_response;
  }

  if (!$wc_custom_jerseys_team->createFileCurrent($product_id)) {
    $a_response['message'] = 'Error al subir la imagen';
    return $a_response;
  }

  $current_dir = $wc_custom_jerseys_team->getCurrentDesingId($product_id);

  ob_start();
  $upload_handler = new UploadHandler(null, true, null, $current_dir);
  $response_string = ob_get_contents();
  ob_end_clean();
  $a_response = json_decode($response_string, true);
  $a_response['files'][0]['date_register'] = date('d/m/Y');
  $a_response['files'][0]['size'] = round((float) ($a_response['files'][0]['size'] / 1024), 4);
  $a_response['files'][0]['filename_codify'] = base64_encode($a_response['files'][0]['name']);

  unset($a_response['files'][0]['deleteType'], $a_response['files'][0]['deleteUrl'], $a_response['files'][0]['thumbnailUrl'], $a_response['files'][0]['type']);

  $a_response = array_merge(array('success' => 1), $a_response);

  return $a_response;
}

function woocommerce_custom_jerseys_view_orders()
{

  global $woocommerce, $wc_custom_jerseys_team;

  $response_data = array();
  $custom_jerseys_content = isset($_POST['custom_jerseys']) ? $_POST['custom_jerseys'] : null;

//  print_r($custom_jerseys_content);
//  
//  die();

  if (!count($custom_jerseys_content)) {
    $response_data['success'] = 0;
    $response_data['message'] = 'Error en la petición';

    return $response_data;
  }

  $response = $wc_custom_jerseys_team->saveOrder($custom_jerseys_content);

  if ($response) {
    $cart_url = $woocommerce->cart->get_cart_url();
    $response_data['success'] = 1;
    $response_data['url_cart'] = $cart_url;
  } else {
    $response_data['success'] = 0;
    $response_data['message'] = 'Error ala guardar las ordenes';
  }

  return $response_data;
}

function woocommerce_custom_jerseys_sponsor_logos()
{
  global $wc_custom_jerseys_team;

  $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
  $a_letters = array(0 => "A-E", 1 => "F-J", 2 => "K-O", 3 => "P-T", 4 => "U-Z");
  $range_letters = $a_letters[$page];

  $dbl_sponsor_logos = $wc_custom_jerseys_team->getSponsorLogos($range_letters);

  return $dbl_sponsor_logos;
}

if (!function_exists('woocommerce_custom_jerseys_ajax')) {

  function woocommerce_custom_jerseys_ajax()
  {
    $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : null;

    $response_data = array();

    if (!$type) {
      $response_data['success'] = 0;
      $response_data['message'] = 'Error en la petición';

      echo json_encode($response_data);
      die();
    }

    switch ($type) {
      case 'upload_logos':
        $response_data = woocommerce_custom_jerseys_save_upload_file();
        break;
      case 'save_order':
        $response_data = woocommerce_custom_jerseys_view_orders();
        break;
      case 'search_design' :
      case 'search_product' :
        $response = woocommerce_output_custom_jerseys_step2();

        if (count($response) > 0) {
          $response_data['partial'] = $response['html'];
          if (isset($response['jerseys'])) {
            $response_data['jerseys'] = $response['jerseys'];
          }

          if (isset($response['logos'])) {
            $response_data['logos'] = $response['logos'];
          }

          $response_data['success'] = 1;
        } else {
          $response_data['success'] = 0;
        }
        break;
      case 'sponsor_logos':
        $dbl_sponsor_logos = woocommerce_custom_jerseys_sponsor_logos();
        $response_data['sponsor_logos'] = $dbl_sponsor_logos;
        $response_data['success'] = 1;

        break;
    }

    if (count($response_data) == 0) {
      $response_data['success'] = 0;
      $response_data['message'] = 'Error en la petición';
      echo json_encode($response_data);
      die();
    }

    echo json_encode($response_data);

    die();
  }

}

if (!function_exists('woocommerce_custom_jerseys_place_order')) {

  function woocommerce_custom_jerseys_place_order($order_id, $posted)
  {
    global $wc_custom_jerseys_team;

    $wc_custom_jerseys_team->saveCustomJersey($order_id);
    //die('***'); 
    //aki va ir todo el guarda a las tablas de custom jerseys de lo datos de la session 
    //despoues del registro se destrye la session
    //Guardav de las imagenes que se intergaran a la orden
  }

}

if (!function_exists('woocommerce_custom_jerseys_update_quantity')) {

  function woocommerce_custom_jerseys_update_quantity()
  {

    global $woocommerce, $wc_custom_jerseys_team;

    if ((!empty($_POST['update_cart']) || !empty($_POST['proceed']) ) && $woocommerce->verify_nonce('cart')) {
      $data_custom_jerseys = $wc_custom_jerseys_team->getOrders();

      $custom_jerseys_totals = isset($_POST['custom_jersey']) ? $_POST['custom_jersey'] : null;

      foreach ($data_custom_jerseys as $cart_item_key => $custom_jerseys) {
        foreach ($custom_jerseys['jerseys'] as $jersey_id => $jersey) {
          if (!$custom_jerseys_totals[$cart_item_key][$jersey_id]['qty']) {
            continue;
          }

          $quantity = $custom_jerseys_totals[$cart_item_key][$jersey_id]['qty'];

          if ($quantity == $jersey['quantity']) {
            continue;
          }

          $wc_custom_jerseys_team->setQuantityOrder($cart_item_key, $jersey_id, $quantity);
        }
      }
    }
  }

}


add_action('wp_enqueue_scripts', 'enqueue_scripts');

add_action('woocommerce_custom_jerseys_tool_one', 'woocommerce_output_custom_jerseys_step1', 10);
//add_action('woocommerce_custom_jerseys_tool_one', 'woocommerce_output_custom_jerseys_step2', 11);
//add_action('woocommerce_custom_jerseys_tool_one', 'woocommerce_output_custom_jerseys_step3', 12);
//add_action('woocommerce_custom_jerseys_tool_one', 'woocommerce_output_custom_jerseys_step4', 13);

add_action('woocommerce_custom_jerseys_tool_two', 'woocommerce_output_custom_colors', 10);
add_action('woocommerce_custom_jerseys_tool_two', 'woocommerce_output_custom_team_logos', 11);
add_action('woocommerce_custom_jerseys_tool_two', 'woocommerce_output_custom_from_logos', 12);
add_action('woocommerce_custom_jerseys_tool_two', 'woocommerce_output_custom_back_logos', 13);
add_action('woocommerce_custom_jerseys_tool_two', 'woocommerce_output_custom_sleeve_logos', 14);
add_action('woocommerce_custom_jerseys_tool_two', 'woocommerce_output_custom_jersey_tab', 15);

add_action('woocommerce_custom_jerseys_tool_three', 'woocommerce_output_custom_jerseys_jersey_poput_one', 10);
add_action('woocommerce_custom_jerseys_tool_three', 'woocommerce_output_custom_jerseys_jersey_poput_two', 11);
add_action('woocommerce_custom_jerseys_tool_three', 'woocommerce_output_custom_jerseys_jersey_poput_three', 12);

add_action('wp_ajax_nopriv_woocommerce_custom_jerseys_ajax', 'woocommerce_custom_jerseys_ajax');
add_action('wp_ajax_woocommerce_custom_jerseys_ajax', 'woocommerce_custom_jerseys_ajax');

add_action('woocommerce_checkout_order_processed', 'woocommerce_custom_jerseys_place_order', 15, 2);
add_action('woocommerce_after_cart_item_quantity_update', 'woocommerce_custom_jerseys_update_quantity')
?>
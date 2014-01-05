<?php

class WC_Custom_Jerseys_Team
{

  private $id;
  public $design_id;
  private $custom_jerseys_content = array();
  private $current_file;

  public function __construct()
  {
    add_action('init', array($this, 'init'), 5);
    add_action('init', array($this, 'removeJersey'), 6);
  }

  public function init()
  {
    $this->getCustomJerseysFromSession();
  }

  public function resetCustomJerseys()
  {
    global $woocommerce;
    $this->custom_jerseys_content = array();
    unset($woocommerce->session->custom_jerseys);
  }

  public function removeJersey()
  {
    global $woocommerce;

    if ((isset($_GET['remove_item']) && $_GET['remove_item']) || (isset($_GET['remove_item_order']) && $_GET['remove_item_order'])) {

      $totals_quantity = 0;

      if ($_GET['remove_item'] && $_GET['remove_item_order']) {
        $this->setQuantityOrder($_GET['remove_item'], $_GET['remove_item_order'], 0);

        foreach ($this->custom_jerseys_content[$_GET['remove_item']]['jerseys'] as $jersey) {
          $totals_quantity += $jersey['quantity'];
        }

        $woocommerce->cart->set_quantity($_GET['remove_item'], $totals_quantity);
      } elseif ($_GET['remove_item'] && !$_GET['remove_item_order']) {
        $woocommerce->cart->set_quantity($_GET['remove_item'], 0);
      }

      if ($totals_quantity == 0) {
        unset($this->custom_jerseys_content[$_GET['remove_item']]);
        $this->setSessionCustomJerseys();
      }

      $woocommerce->add_message(__('Cart updated.', 'woocommerce'));
      $referer = ( wp_get_referer() ) ? wp_get_referer() : $woocommerce->cart->get_cart_url();
      wp_safe_redirect($referer);
      exit;
    }
  }

  public function getRemoveJerseyUrl($cart_item_key, $jersey_id = null)
  {

    global $woocommerce;
    $page_custom_jerseys_id = get_option('woocommerce_custom_jerseys_page_id');
    $args_url = array();

    if ($page_custom_jerseys_id) {
      if ($cart_item_key) {
        $args_url['remove_item'] = $cart_item_key;
      }

      if ($jersey_id) {
        $args_url['remove_item_order'] = $jersey_id;
      }

      return $woocommerce->nonce_url('custom_jerseys', add_query_arg($args_url, get_permalink($page_custom_jerseys_id)));
    }
  }

  public function setCurrentFile($current_file)
  {
    $this->current_file = $current_file;
  }

  public function getCurrentFile()
  {
    return $this->current_file;
  }

  public function getCurrentDesingId($product_id)
  {

    if (!array_key_exists($product_id, $_SESSION['current_id'])) {
      return '';
    }

    return $_SESSION['current_id'][$product_id];
  }

  public function getDesignId()
  {
    return $this->generateDesignId();
  }

  public function generateDesignId()
  {
    return substr(number_format(time() * rand(), 0, '', ''), 0, 10);
  }

  public function getCustomJerseysFromSession()
  {
    global $woocommerce;

    if (isset($woocommerce->session->custom_jerseys) && is_array($woocommerce->session->custom_jerseys)) {
      $custom_jerseys = $woocommerce->session->custom_jerseys;

      foreach ($custom_jerseys as $key => $value) {
        $this->custom_jerseys_content[$key] = $value;
      }
    }

    if (count($this->custom_jerseys_content) == 0) {
      $this->custom_jerseys_content = array();
    }
  }

  public function setSessionCustomJerseys()
  {
    global $woocommerce;

    $custom_jerseys_session = array();

    if ($this->custom_jerseys_content) {
      foreach ($this->custom_jerseys_content as $key => $value) {
        $custom_jerseys_session[$key] = $value;
      }
    }

    if (count($custom_jerseys_session)) {
      $this->setOrderCookies();
    } else {
      $this->setOrderCookies(false);
    }

    $woocommerce->session->custom_jerseys = $custom_jerseys_session;
  }

  public function saveOrder($data_custom_jerseys = array())
  {
    global $woocommerce;

    if (count($data_custom_jerseys) == 0) {
      return false;
    }

    if (!$data_custom_jerseys['product_id']) {
      return false;
    }

    if (!$data_custom_jerseys['color_id']) {
      return false;
    }

    $product_id = $data_custom_jerseys['product_id'];
    $color_id = $data_custom_jerseys['color_id'];
    $product_data = get_product($product_id);
    $current_desing_id = $this->getCurrentDesingId($product_id);
    $cart_id = $woocommerce->cart->generate_cart_id($product_id);
    $cart_order_item_key = $this->findOrderInCart($cart_id);

    if (!$product_data) {
      return false;
    }

    $array_text_fonts = $data_custom_jerseys['text_fonts'];
    $array_logos = $data_custom_jerseys['logos'];

    if (count($data_custom_jerseys['jerseys']) < 1) {
      return false;
    }

    $array_jerseys = $this->validateJerseys($data_custom_jerseys['jerseys']);

    if (count($array_text_fonts) == 0 || count($array_jerseys) == 0) {
      return false;
    }

    if (!$array_text_fonts['front'] && !$array_text_fonts['back'] && !$array_text_fonts['sleeve']) {
      return false;
    }

    if ($cart_order_item_key) {
      $this->custom_jerseys_content[$cart_order_item_key]['colors']['color_id'] = $color_id;
      $this->custom_jerseys_content[$cart_order_item_key]['text_fonts'] = $array_text_fonts;
      $this->custom_jerseys_content[$cart_order_item_key]['logos'] = $array_logos;
      $this->custom_jerseys_content[$cart_order_item_key]['jerseys'] = $array_jerseys;
    } else {

      $this->custom_jerseys_content[$cart_id] = array(
          'colors' => array('desing_id' => $current_desing_id, 'product_id' => $product_id, 'color_id' => $color_id),
          'text_fonts' => $array_text_fonts,
          'logos' => $array_logos,
          'jerseys' => $array_jerseys
      );
    }

    $quantity = count($array_jerseys);
    $this->setOrderCookies();
    $this->setSessionCustomJerseys();
    $woocommerce->cart->add_to_cart($product_id, $quantity);

    return true;
  }

  public function findOrderInCart($cart_id = null)
  {
    if (!$cart_id) {
      return null;
    }

    if (count($this->custom_jerseys_content) == 0) {
      return null;
    }

    foreach ($this->custom_jerseys_content as $order_item_key => $order_item) {
      if ($order_item_key == $cart_id) {
        return $order_item_key;
        break;
      }
    }
  }

  public function validateJerseys($jerseys_data)
  {
    if (is_array($jerseys_data) && count($jerseys_data) == 0) {
      return array();
    }

    $is_error = false;
    $array_jerseys_data = array();
    foreach ($jerseys_data as $jersey) {
      if (!$jersey['name'] || !$jersey['number'] || !$jersey['size']) {
        $is_error = true;
        break;
      }

      //$jersey['quantity'] = 1;
      $hash = hash('md5', implode('', $jersey));
      $array_jerseys_data[$hash] = $jersey;
    }

    if ($is_error) {
      return array();
    }

    return $array_jerseys_data;
  }

  public function setQuantityOrder($cart_order_item_key, $jersey_id, $quantity = 1)
  {
    if (is_array($this->custom_jerseys_content)) {
      if ($quantity == 0 || $quantity < 0) {
        unset($this->custom_jerseys_content[$cart_order_item_key]['jerseys'][$jersey_id]);
      } else {
        $this->custom_jerseys_content[$cart_order_item_key]['jerseys'][$jersey_id]['quantity'] = $quantity;
      }

      $this->setSessionCustomJerseys();
    }
  }

  public function getOrders()
  {
    return $this->custom_jerseys_content;
  }

  private function setOrderCookies($set = true)
  {

    if ($set) {
      setcookie("woocommerce_item_in_order_jerseys", "1", 0, COOKIEPATH, COOKIE_DOMAIN, false);
      setcookie("woocommerce_order_jerseys_hash", md5(json_encode($this->getOrders())), 0, COOKIEPATH, COOKIE_DOMAIN, false);
    } else {
      setcookie("woocommerce_item_in_order_jerseys", "0", time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false);
      setcookie("woocommerce_order_jerseys_hash", "0", time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false);
    }
  }

  public function getOrderJerseyByOrderId($order_id)
  {
    global $wpdb;

    $query = "SELECT o.team_id, o.name, o.number, o.size, o.price_unit, o.quantity, o.total FROM {$wpdb->prefix}custom_jerseys_order o 
                  INNER JOIN {$wpdb->prefix}custom_jerseys_team t
                  ON o.team_id = t.id
                  WHERE t.order_id = %d ORDER BY o.created_at";

    $results = $wpdb->get_results($wpdb->prepare($query, $order_id));

    return $results;
  }

  public function getTeamByDesingId($desing_id)
  {
    global $wpdb;

    if (!isset($desing_id) && !$desing_id) {
      return false;
    }

    $query = "SELECT t.id, t.design_id, p.id as color_id, p.product_id, p.image_front, p.image_back, s.color_left, s.color_right, s.slug_attribute FROM wp_custom_jerseys_team t 
              INNER JOIN wp_custom_jerseys_product_shirt_color p 
              ON t.product_shirt_color_id = p.id 
              INNER JOIN wp_custom_jerseys_shirt_color s 
              ON p.shirt_color_id = s.id 
              WHERE t.design_id = %s ";

    $dbr_team = $wpdb->get_row($wpdb->prepare($query, $desing_id));

    return $dbr_team ? $dbr_team : null;
  }

  public function getTeamMetaByTeamId($team_id)
  {
    global $wpdb;

    if (!isset($team_id) && !$team_id) {
      return false;
    }

    $query = "SELECT team_meta_key, team_meta_value, team_meta_tab  FROM wp_custom_jerseys_team_meta WHERE team_id = %d";

    $results = $wpdb->get_results($wpdb->prepare($query, $team_id));

    return $results;
  }

  public function getProducts()
  {

    $product_data = array();
    $args = array('post_type' => 'product');
    $loop = new WP_Query($args);

    while ($loop->have_posts()) {
      $loop->the_post();

      if (function_exists('get_product')) {
        $_product = get_product($loop->post->ID);
      } else {
        $_product = new WC_Product($loop->post->ID);
      }

      if ($loop->post->post_status != "publish" || !$_product->get_price()) {
        continue;
      }

      ob_start();
      the_title();
      $title = ob_get_contents();
      ob_end_clean();

      $product_data[$_product->id]['id'] = $_product->id;
      $product_data[$_product->id]['price'] = $_product->get_price();
      $product_data[$_product->id]['title'] = $title;
      $product_data[$_product->id]['currency_symbol'] = get_woocommerce_currency_symbol();
      $product_data[$_product->id]['image'] = wp_get_attachment_image_src(get_post_thumbnail_id($_product->id), 'medium');
    }

    wp_reset_query();

    return $product_data;
  }

  public function getGalleryByProduct($_product)
  {

    if (!isset($_product)) {
      return false;
    }

    $attachment_ids = $_product->get_gallery_attachment_ids();

    $data_image = array();

    if ($attachment_ids) {
      foreach ($attachment_ids as $attachment_id) {

        $image_link = wp_get_attachment_url($attachment_id);


        //print_r($image_link);
        echo $image_link . "\n";

        if (!$image_link)
          continue;

        $image_src = wp_get_attachment_image_src($attachment_id, apply_filters('single_product_small_thumbnail_size', 'shop_single'));

        $data_image[] = $image_src[0];
      }
    }


    print_r($data_image);

    die();
    return (count($data_image) == 1 ? $data_image[0] : $data_image );
  }

  public function saveTeamLogos($team_id, $logos_data)
  {
    global $wpdb;

    print_r($logos_data);

    $data = array(
        'team_id' => $team_id,
        'filename' => $logos_data['filename'],
        'size' => $logos_data['size'],
        'name' => $logos_data['name']
    );

    $wpdb->insert("{$wpdb->prefix}custom_jerseys_team_logos", $data);
  }

  public function getLogosByTeamId($team_id)
  {
    global $wpdb;

    $query = "SELECT  filename, name, size, DATE_FORMAT(created_at, '%d/%m/%Y') as date_register FROM {$wpdb->prefix}custom_jerseys_team_logos WHERE team_id = {$team_id} ";

    $results = $wpdb->get_results($query);

    return $results;
  }

  public function processSaveLogos($team_id, $desing_id, $logos_data = array())
  {
    if (!isset($desing_id)) {
      return false;
    }

    if (count($logos_data) == 0) {
      return false;
    }

    $upload_dir = wp_upload_dir();

    $dir_source = $upload_dir['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_TEMP . '/' . $desing_id;
    $dir_dest = $upload_dir['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_LOGOS . '/' . $desing_id;

    WC_Custom_Jersey_Functions::createDirLogos($dir_dest);

    $logos_data_position = array();
    $array_file_sponsor_logos = array();
    $array_file_logos = array();

    foreach ($logos_data as $logo) {
      if (isset($logo['sponsor_id'])) {
        $array_file_sponsor_logos[] = $logo;
        continue;
      }

      $filename = base64_decode($logo['filename']);
      $logo['name'] = $filename;
      $logo['filename'] = $upload_dir['baseurl'] . '/' . WOO_CUSTOM_JERSEYS_FILES_LOGOS . '/' . $desing_id . '/' . $filename;
      $logos_data_position[sha1($filename)] = $logo;
    }

    $array_file_logos = $this->copyFiles($team_id, $dir_source, $dir_dest, $logos_data_position);
    $data_logos = array();

    if (count($array_file_logos) > 0 && count($array_file_sponsor_logos) > 0) {
      $data_logos = array_merge($array_file_logos, $array_file_sponsor_logos);
    } else {
      if (count($array_file_logos) > 0) {
        $data_logos = $array_file_logos;
      }

      if (count($array_file_sponsor_logos) > 0) {
        $data_logos = $array_file_sponsor_logos;
      }
    }

    return $data_logos;
  }

  public function copyFiles($team_id, $dir_source, $dir_dest, $logos_data = array())
  {

    if (count($logos_data) == 0) {
      return array();
    }

    if (is_dir($dir_source))
      $dir_handle = opendir($dir_source);

    $array_file_names = array();

    while ($file = readdir($dir_handle)) {
      if ($file != '.' && $file != '..') {
        if (is_file($dir_source . '/' . $file)) {

          if (!isset($logos_data[sha1($file)])) {
            continue;
          }


          $filename_dest = $dir_dest . "/" . $file;
          if (file_exists($filename_dest)) {
            $array_file_names[] = $logos_data[sha1($file)];
            continue;
          }
          copy($dir_source . '/' . $file, $filename_dest);

          if ($team_id) {
            $this->saveTeamLogos($team_id, $logos_data[sha1($file)]);
            $array_file_names[] = $logos_data[sha1($file)];
          }
        }
      }
    }

    closedir($dir_handle);

    return $array_file_names;
  }

  public function copyLogos($dir_source, $dir_dest)
  {
    if (is_dir($dir_source))
      $dir_handle = opendir($dir_source);

    while ($file = readdir($dir_handle)) {
      if ($file != '.' && $file != '..') {
        if (is_file($dir_source . '/' . $file)) {
          $filename_dest = $dir_dest . "/" . $file;
          copy($dir_source . '/' . $file, $filename_dest);
        }
      }
    }

    closedir($dir_handle);
  }

  public function createFileCurrent($product_id, $is_dir_temp = true)
  {
    $upload_array = wp_upload_dir();

    $current_base_dir = ($is_dir_temp == true ? $upload_array['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_TEMP : $upload_array['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_LOGOS);
    $current_desing_id = $this->getCurrentDesingId($product_id); //$_SESSION['current_id'][$product_id];

    if (!$current_desing_id) {
      return false;
    }

    if (wp_mkdir_p($current_base_dir . '/' . $current_desing_id)) {
      return true;
    }

    return false;
  }

  public function saveCustomJerseysTeam($data_team)
  {

    global $wpdb, $current_user;
    get_currentuserinfo();

    $data = array(
        'design_id' => $data_team['desing_id'],
        'product_shirt_color_id' => $data_team['color_id'],
        'order_id' => $data_team['order_id'],
        'created_by' => $current_user->ID
    );

    $wpdb->insert("{$wpdb->prefix}custom_jerseys_team", $data);

    return ($wpdb->insert_id ? $wpdb->insert_id : new WP_Error('error_save_team', __('Error al guardar Custom jerseys Team')));
  }

  public function saveMetaCustomJerseys($team_id, $team_meta_key, $team_meta_value, $team_meta_tab)
  {

    global $wpdb;

    if (!isset($team_id) && !$team_id) {
      return false;
    }

    $team_id = (int) $team_id;

    $query = "SELECT COUNT(*) FROM {$wpdb->prefix}custom_jerseys_team_meta WHERE team_id = %d and team_meta_key = %s";

    $team_meta_count = $wpdb->get_var($wpdb->prepare($query, $team_id, $team_meta_key));

    if ($team_meta_count == 0) {
      $insert_data = array(
          'team_id' => $team_id,
          'team_meta_value' => (is_array($team_meta_value) ? json_encode($team_meta_value) : $team_meta_value),
          'team_meta_key' => $team_meta_key,
          'team_meta_tab' => $team_meta_tab
      );

      $wpdb->insert("{$wpdb->prefix}custom_jerseys_team_meta", $insert_data);
    } else {

      $where = array('team_id' => $team_id, 'team_meta_key' => $team_meta_key);
      $update_data = array('team_meta_key' => $team_meta_key);

      $wpdb->update("{$wpdb->prefix}custom_jerseys_team_meta", $update_data, $where);
    }
  }

  public function saveCustomJersey($order_id)
  {

    global $woocommerce;

    try {
      $data_custom_jerseys_order = $this->getOrders();

      foreach ($data_custom_jerseys_order as $jersey_order) {
        if (array_key_exists('colors', $jersey_order)) {
          $desing_id = $jersey_order['colors']['desing_id'];
          $product_id = $jersey_order['colors']['product_id'];
          $jersey_order['colors']['order_id'] = $order_id;
          $team_id = $this->saveCustomJerseysTeam($jersey_order['colors']);
        }

        if ($team_id > 0) {
          if (array_key_exists('text_fonts', $jersey_order)) {
            $data_text_fonts = $jersey_order['text_fonts'];

            $data_team_name = $data_text_fonts['team_name'];
            if ($data_text_fonts['team_name']['type'] == 'logo') {
              $data_team_name = $this->processSaveLogos($team_id, $desing_id, array($data_text_fonts['team_name']));
              $data_team_name = (isset($data_team_name[0]) ? $data_team_name[0] : $data_team_name);
            }

            $this->saveMetaCustomJerseys($team_id, '_team_name_logo', (count($data_team_name) > 0 ? $data_team_name : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_position_back', ($data_text_fonts['back'] ? $data_text_fonts['back'] : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_position_front', ($data_text_fonts['front'] ? $data_text_fonts['front'] : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_position_sleeve', ($data_text_fonts['sleeve'] ? $data_text_fonts['sleeve'] : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_position_comment', ($data_text_fonts['comment'] ? $data_text_fonts['comment'] : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_font', ($data_text_fonts['font'] ? $data_text_fonts['font'] : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_text_fill', ($data_text_fonts['text_fill'] ? $data_text_fonts['text_fill'] : null), 'text_fonts');
            $this->saveMetaCustomJerseys($team_id, '_team_outline', ($data_text_fonts['outline'] ? $data_text_fonts['outline'] : null), 'text_fonts');
          }

          if (array_key_exists('logos', $jersey_order)) {

            $data_logos = $jersey_order['logos'];

            if (array_key_exists('front', $data_logos) && count($data_logos)) {
              $data_logos['front'] = $this->processSaveLogos($team_id, $desing_id, $data_logos['front']);
              $data_logos['front'] = (count($data_logos['front']) > 0 ? $data_logos['front'] : null);
              $this->saveMetaCustomJerseys($team_id, '_team_front_logos', $data_logos['front'], 'front_logos');
            }

            if (array_key_exists('back', $data_logos) && count($data_logos)) {
              $data_logos['back'] = $this->processSaveLogos($team_id, $desing_id, $data_logos['back']);
              $data_logos['back'] = (count($data_logos['back']) > 0 ? $data_logos['back'] : null);
              $this->saveMetaCustomJerseys($team_id, '_team_back_logos', $data_logos['back'], 'back_logos');
            }

            if (array_key_exists('sleeve', $data_logos) && count($data_logos)) {
              $data_logos['sleeve'] = $this->processSaveLogos($team_id, $desing_id, $data_logos['sleeve']);
              $data_logos['sleeve'] = (count($data_logos['sleeve']) > 0 ? $data_logos['sleeve'] : null);
              $this->saveMetaCustomJerseys($team_id, '_team_sleeve_logos', $data_logos['sleeve'], 'sleeve_logos');
            }

            $upload_dir = wp_upload_dir();
            WC_Custom_Jersey_Functions::rrmdir($upload_dir['basedir'] . '/' . WOO_CUSTOM_JERSEYS_FILES_TEMP . '/' . $desing_id);
          }

          if (array_key_exists('jerseys', $jersey_order)) {
            $data_jerseys = $jersey_order['jerseys'];
            $data_jerseys = array_values($data_jerseys);
            $this->saveMetaCustomJerseys($team_id, '_team_jerseys', $data_jerseys, 'jerseys');
          }
        }
      }

      $this->resetCustomJerseys();
    } catch (Exception $e) {
      $woocommerce->add_error($e->getMessage());
    }
  }

  public function getSponsorLogos($pagination_letters = null)
  {
    global $wpdb;
    $query = "SELECT id, filename, name, letter FROM wp_custom_jerseys_sponsor_logo where status = 1";

    $letters = "";
    if ($pagination_letters) {
      $range_letters = explode('-', $pagination_letters);
      $letters = '"' . implode('","', $range_letters) . '"';
    }

    if ($letters) {
      $query.= " AND letter in ($letters)";
    }

    return $wpdb->get_results($query);
  }

  public function getOrderSubtotal($_product, $quantity)
  {

    $price = $_product->get_price();

    $subtotal_jersey = $price * $quantity;

    return woocommerce_price($subtotal_jersey);
  }

}

$GLOBALS['wc_custom_jerseys_team'] = new WC_Custom_Jerseys_Team();
?>
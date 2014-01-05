<?php

class WC_Custom_Jerseys_Shirt_Color
{

  public static function setSettings()
  {
    global $wpdb, $wc_custom_jerseys_shirt_color;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $insert_query = "INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#FFF', '#999', 'white_grey');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#FFF', '#f26722', 'white_orange');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#FFF', '#8dc63f', 'white_green');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#000', '#293993', 'black_blue');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#000', '#bf1e2e', 'black_red');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#000', '#2b3a90', 'black_blue_2');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#000', '#666', 'black_grey');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#000', '#8a8a2b', 'black_green');
    INSERT INTO {$wpdb->prefix}custom_jerseys_shirt_color (color_left, color_right, slug_attribute) VALUES ('#000', '#ec1f27', 'black_red_2');";

    dbDelta($insert_query);
    unset($insert_query);

    $query = "SELECT ID, post_title, post_name FROM {$wpdb->prefix}posts where post_type = 'product' AND post_status = 'publish' AND ID NOT IN (SELECT  DISTINCT post_parent FROM {$wpdb->prefix}posts WHERE post_type = 'product_variation' AND post_status = 'publish')";

    $dbl_products = $wpdb->get_results($query);

    $data_images_products = array();

    foreach ($dbl_products as $dbr_product) {

      $query_image = "SELECT * FROM {$wpdb->prefix}posts p where p.post_type = 'attachment' AND (p.post_mime_type LIKE 'image/%')  AND (p.post_status = 'inherit') AND p.post_parent = {$dbr_product->ID} ORDER BY p.post_date DESC";

      $data_images_products[$dbr_product->ID]['product_id'] = $dbr_product->ID;
      $data_images_products[$dbr_product->ID]['sku'] = WC_Custom_Jersey_Functions:: getSkuByPostId($dbr_product->ID);

      $dbl_images_products = $wpdb->get_results($query_image);

      foreach ($dbl_images_products as $dbr_image_product) {
        $image_link = wp_get_attachment_url($dbr_image_product->ID);

        if (!$image_link)
          continue;

        $data_images_products[$dbr_product->ID]['images'][$dbr_image_product->ID] = $image_link;
      }
    }

    $dbl_shirt_colors = $wc_custom_jerseys_shirt_color->getShirtColors();

    foreach ($data_images_products as $image_product) {
      if (!array_key_exists('images', $image_product)) {
        continue;
      }

      $query_insert = "";
      $product_sku = $image_product['sku'];
      $product_id = $image_product['product_id'];

      foreach ($image_product['images'] as $image) {

        $data_image = pathinfo($image);
        $filename_image_position_front = $product_sku . "_logo_position_front";
        $filename_image_position_back = $product_sku . "_logo_position_back";

        if (sha1($data_image['filename']) == sha1($filename_image_position_front)) {
          $filename_front = $data_image['dirname'] . "/" . $filename_image_position_front . "." . $data_image['extension'];
          $filename_back = $data_image['dirname'] . "/" . $filename_image_position_back . "." . $data_image['extension'];
          $query_insert = "INSERT INTO {$wpdb->prefix}custom_jerseys_product_shirt_color (product_id, image_front, image_back) VALUES({$product_id}, '{$filename_front}', '{$filename_back}')";
          dbDelta($query_insert);
          unset($query_insert);
          continue;
        }

        foreach ($dbl_shirt_colors as $dbr_shirt_color) {
          $filename_image_front = $product_sku . "_" . $dbr_shirt_color->slug_attribute . "_front";
          $filename_image_back = $product_sku . "_" . $dbr_shirt_color->slug_attribute . "_back";

          if (sha1($data_image['filename']) == sha1($filename_image_front)) {
            $filename_front = $data_image['dirname'] . "/" . $filename_image_front . "." . $data_image['extension'];
            $filename_back = $data_image['dirname'] . "/" . $filename_image_back . "." . $data_image['extension'];
            $query_insert = "INSERT INTO {$wpdb->prefix}custom_jerseys_product_shirt_color (shirt_color_id, product_id, image_front, image_back) VALUES({$dbr_shirt_color->id},{$product_id}, '{$filename_front}', '{$filename_back}')";

            dbDelta($query_insert);
            unset($query_insert);
          }
        }
      }
    }
  }

  public function getshirtColorByProductId($product_id)
  {
    global $wpdb;

    if (!$product_id) {
      return false;
    }

    $query = "SELECT p.id, p.image_front, p.image_back , sc.color_left, sc.color_right, IFNULL(sc.slug_attribute,'sin_slug') as slug_attribute FROM wp_custom_jerseys_product_shirt_color p LEFT JOIN wp_custom_jerseys_shirt_color sc on p.shirt_color_id = sc.id WHERE p.product_id = %d AND (sc.status = 1 OR  ISNULL(sc.status)) ORDER BY p.shirt_color_id;";

    $results = $wpdb->get_results($wpdb->prepare($query, $product_id));

    return $results;
  }

  public function getShirtColors()
  {
    global $wpdb;

    $query = "SELECT * FROM {$wpdb->prefix}custom_jerseys_shirt_color WHERE status = 1";

    $results = $wpdb->get_results($query);

    return $results;
  }

}

$GLOBALS['wc_custom_jerseys_shirt_color'] = new WC_Custom_Jerseys_Shirt_Color();
?>
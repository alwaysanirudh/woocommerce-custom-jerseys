<?php

Class WC_Custom_Jerseys_Font_Color
{

  public static function setSettings()
  {
    global $wpdb;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $insert_query = "INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Diamond', 'font');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Impact', 'font');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Capital Daren', 'font');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Feast of Flesh', 'font');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Black', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Blue', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Green', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Purple', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Red', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Yellow', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('White', 'text_fill');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Black', 'outline');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Blue', 'outline');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Green', 'outline');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Purple', 'outline');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Red', 'outline');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('Yellow', 'outline');
		INSERT INTO {$wpdb->prefix}custom_jerseys_font_colors (name, type_font) VALUES('White', 'outline');";

    dbDelta($insert_query);
    unset($insert_query);

    $query = "SELECT ID, post_title, post_name FROM {$wpdb->prefix}posts where post_type = 'product' AND post_status = 'publish' AND ID NOT IN (SELECT  DISTINCT post_parent FROM {$wpdb->prefix}posts WHERE post_type = 'product_variation' AND post_status = 'publish')";

    $dbl_products = $wpdb->get_results($query);

    foreach ($dbl_products as $dbr_product) {
      $insert_query = "INSERT INTO {$wpdb->prefix}custom_jerseys_product_font_color (font_colors_id, product_id, status) SELECT id, {$dbr_product->ID}, 1 from {$wpdb->prefix}custom_jerseys_position";

      dbDelta($insert_query);
    }
  }

  public function getFontColor($product_id)
  {
    global $wpdb;

    $query = "SELECT p.id, f.name, UPPER(type_font) as type, 1 as for_logo_position  FROM {$wpdb->prefix}custom_jerseys_product_font_color p
              INNER JOIN {$wpdb->prefix}custom_jerseys_font_colors f on p.font_colors_id = f.id
              WHERE p.product_id = %d AND p.status = 1 ORDER BY type_font;";

    return $wpdb->get_results($wpdb->prepare($query, $product_id));
  }

}

$GLOBALS['wc_custom_jerseys_font_color'] = new WC_Custom_Jerseys_Font_Color();
?>
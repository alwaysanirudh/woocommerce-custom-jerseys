<?php

class WC_Custom_Jerseys_Position
{

  public static function setSettings()
  {
    global $wpdb;

    $positons = array(
        'FRONT' => array('F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'TEAM_NAME/LOGO', 'NUMBER'),
        'BACK' => array('B1', 'B2', 'B3', 'B4', 'NAME', 'NUMBER'),
        'SLEEVE' => array('S1', 'S2', 'S3', 'S4', 'S5')
    );

    $insert_query = "";
    foreach ($positons as $key => $position) {
      foreach ($position as $value) {
        $data_insert_positions[] = '("' . $value . '","' . $key . '")';
      }
    }

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $insert_query = "INSERT INTO {$wpdb->prefix}custom_jerseys_position (name, type) VALUES " . implode(",", $data_insert_positions);

    dbDelta($insert_query);
    unset($insert_query);

    $query = "SELECT ID, post_title, post_name FROM {$wpdb->prefix}posts where post_type = 'product' AND post_status = 'publish' AND ID NOT IN (SELECT  DISTINCT post_parent FROM {$wpdb->prefix}posts WHERE post_type = 'product_variation' AND post_status = 'publish')";

    $dbl_product = $wpdb->get_results($query);

    foreach ($dbl_product as $dbr_product) {
      if ($dbr_product->post_name == 'contact') {
        $insert_query = "INSERT INTO {$wpdb->prefix}custom_jerseys_product_position (position_id, product_id, for_logo_position)  SELECT id, {$dbr_product->ID}, CASE name WHEN 'NAME' then 0 WHEN 'NUMBER' THEN 0 WHEN 'TEAM_NAME/LOGO' THEN 0 ELSE 1 END from {$wpdb->prefix}custom_jerseys_position;";
      } else {
        $insert_query = "INSERT INTO {$wpdb->prefix}custom_jerseys_product_position (position_id, product_id, for_logo_position)  SELECT id, {$dbr_product->ID}, CASE name WHEN 'NAME' then 0 WHEN 'NUMBER' THEN 0 WHEN 'TEAM_NAME/LOGO' THEN 0 ELSE 1 END from {$wpdb->prefix}custom_jerseys_position WHERE name<>'S5';";
      }

      dbDelta($insert_query);
    }
  }

  public function getPositionBytype($type_logo = 'FRONT', $is_logo_position = null)
  {

    global $wpdb;

    $query = "SELECT id, name  FROM {$wpdb->prefix}custom_jerseys_position WHERE status = 1 AND type = %s";

    if (!is_null($is_logo_position)) {
      $query .= " AND flg_logo = %d";
    }

    $query .= " ORDER BY name";

    if (!is_null($is_logo_position) && is_numeric($is_logo_position)) {
      $results = $wpdb->get_results($wpdb->prepare($query, $type_logo, $is_logo_position));
    } else {
      $results = $wpdb->get_results($wpdb->prepare($query, $type_logo));
    }

    return $results;
  }

  public function getPositionByProductId($product_id, $for_logo_position = null, $name_positions = array())
  {
    global $wpdb;

    if (!isset($product_id)) {
      return false;
    }

    $query = "SELECT pr.id, pr.position_id, po.name, po.type, pr.for_logo_position FROM {$wpdb->prefix}custom_jerseys_product_position pr 
              INNER JOIN {$wpdb->prefix}custom_jerseys_position po ON pr.position_id = po.id
              WHERE product_id = %d ";

    if (!is_null($for_logo_position) && is_numeric($for_logo_position)) {
      $query .= " AND pr.for_logo_position = %d";
    }

    if (is_array($name_positions) && count($name_positions)) {
      $names = implode(',', $name_positions);
      $query .= " AND po.name in ( $names)";
    }


    $query .= " ORDER BY type, name";

    if (!is_null($for_logo_position) && is_numeric($for_logo_position)) {
      $results = $wpdb->get_results($wpdb->prepare($query, $type_logo, $for_logo_position));
    } else {
      $results = $wpdb->get_results($wpdb->prepare($query, $product_id));
    }

    return $results;
  }

  public function saveCustomJerseysLogos($team_id, $data_logo)
  {

    global $wpdb;

    if (!isset($team_id) && !$team_id) {
      return false;
    }

    $data = array(
        'team_id' => $team_id,
        'filename' => $data_logo['filename'],
        'hash_filename' => $data_logo['hash_filename'],
        'comment' => $data_logo['comment'],
        'is_logo_position' => $data_logo['is_logo_position']
    );

    $wpdb->insert("{$wpdb->prefix}custom_jerseys_logo", $data);

    return $wpdb->insert_id;
  }

  public function saveCustomJerseysLogoPosition($product_id, $logo_id, $data_name_position = array())
  {
    global $wpdb;

    if (!isset($logo_id)) {
      return false;
    }

    $dbr_position_logos = $this->getPositionByProductId($product_id, null, $data_name_position);

    $data_position_logo = array();
    foreach ($dbr_position_logos as $position_logo) {
      $data_position_logo['product_position_id'] = $position_logo->id;
      $data_position_logo['logo_id'] = $logo_id;

      $wpdb->insert("{$wpdb->prefix}custom_jerseys_logo_position", $data_position_logo);
    }
  }

}

$GLOBALS['wc_custom_jerseys_position'] = new WC_Custom_Jerseys_Position();
?>
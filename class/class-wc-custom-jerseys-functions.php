<?php

class WC_Custom_Jersey_Functions
{

  public static function create_page($slug, $option, $page_title = '', $page_content = '', $post_parent = 0)
  {
    global $wpdb;

    $option_value = get_option($option);

    if ($option_value > 0 && get_post($option_value))
      return;

    $page_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s  AND post_type = 'page' LIMIT 1;", $slug));
    if ($page_id != NULL) {
      update_option($option, $page_id);
      return $page_id;
    }

    $page_data = array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_author' => 1,
        'post_name' => $slug,
        'post_title' => $page_title,
        'post_content' => $page_content,
        'post_parent' => $post_parent,
        'comment_status' => 'closed'
    );

    $page_id = wp_insert_post($page_data);
    update_option($option, $page_id);

    return $page_id;
  }

  public static function createDirLogos($directory)
  {

    if (!isset($directory)) {
      return false;
    }

    //  $upload_array = wp_upload_dir();
//    $upload_dir_temp = $upload_array['basedir'] . '/' . $directory . '/';

    $upload_dir_temp = $directory;

    if (!is_dir($upload_dir_temp)) {
      $is_success = mkdir($upload_dir_temp, '0755', true);
      if (!$is_success) {
        return false;
      }

      return true;
    }
  }

  public static function readFiles($directory, $path = null)
  {

    $upload_dir = wp_upload_dir();
    if (!$path) {
      $path = (!$path ? $upload_dir['basedir'] . '/' . $path . '/' . $directory : $path . '/' . $directory );
    }

    $dir = opendir($path);
    $files = array();

    while ($element = readdir($dir)) {
      if ($element != "." && $element != "..") {
        if (is_file($path . '/' . $element) === true) {
          $filename_url = $upload_dir['baseurl'] . '/' . $directory . '/' . $element;

          $files[] = array(
              'path_filename' => $path . '/' . $element,
              'filename' => $filename_url,
              'name' => $element
          );
        }
      }
    }

    return $files;
  }

  public static function rrmdir($directory)
  {

    foreach (glob($directory . '/*') as $file) {
      if (is_dir($file))
        self::rrmdir($file);
      else
        unlink($file);
    }
    rmdir($directory);
  }

  public static function registerSponsorLogos($files)
  {
    global $wpdb;
    foreach ($files as $file) {
      $data = array(
          'filename' => $file['filename'],
          'hash_filename' => hash_file('crc32b', $file['path_filename']),
          'name' => $file['name'],
          'letter' => mb_strtoupper(substr($file['name'], 0, 1)
          )
      );

      $wpdb->insert("{$wpdb->prefix}custom_jerseys_sponsor_logo", $data);
    }
  }

  public static function getSkuByPostId($post_id)
  {
    global $wpdb;

    $product_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='_sku' AND post_id='%s' LIMIT 1", $post_id));

    return ($product_id ? $product_id : null);
  }

}

?>
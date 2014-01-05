<?php
/*
  Plugin Name: Woocommerce Custom Jersey
  Plugin URI: http://paveltocto.com
  Description: Plugin que permite personalizar los productos que esta agregando al carrito de compras
  Version: 1.0
  Author: Pavel Tocto F.
  Author URI: http://paveltocto.com
 */

define('WOO_CUSTOM_JERSEYS_FILE_PATH', dirname(__FILE__));
define('WOO_CUSTOM_JERSEYS_DIR_NAME', basename(WOO_CUSTOM_JERSEYS_FILE_PATH));
define('WOO_CUSTOM_JERSEYS_FOLDER', dirname(plugin_basename(__FILE__)));
define('WOO_CUSTOM_JERSEYS_NAME', plugin_basename(__FILE__));
define('WOO_CUSTOM_JERSEYS_URL', untrailingslashit(plugins_url('/', __FILE__)));
define('WOO_CUSTOM_JERSEYS_DIR', WP_CONTENT_DIR . '/plugins/' . WOO_CUSTOM_JERSEYS_FOLDER);
define('WOO_CUSTOM_JERSEYS_IMAGES_URL', WOO_CUSTOM_JERSEYS_URL . '/assets/images');
define('WOO_CUSTOM_JERSEYS_JS_URL', WOO_CUSTOM_JERSEYS_URL . '/assets/js');
define('WOO_CUSTOM_JERSEYS_CSS_URL', WOO_CUSTOM_JERSEYS_URL . '/assets/css');
define('WOO_CUSTOM_JERSEYS_FILES_TEMP', "files_temp");
define('WOO_CUSTOM_JERSEYS_FILES_LOGOS', "files_custom_jerseys");


include('class/class-wc-custom-jerseys-functions.php');
include('class/class-wc-custom-jerseys-font-color.php');
include('class/class-wc-custom-jerseys-team.php');
include('class/class-wc-custom-jerseys-shirt-color.php');
include('class/class-wc-custom-jerseys-position.php');
include('admin/custom_jerseys_install.php');

/*
 * Este activation Hook crea una tabla en la base de datos para mantener el registro
 * de las transacciones.
 */

register_activation_hook(__FILE__, array('WC_Custom_Jersey', 'customJerseysInstall'));
register_uninstall_hook(__FILE__, array('WC_Custom_Jersey', 'customJerseysUninstall'));
?>
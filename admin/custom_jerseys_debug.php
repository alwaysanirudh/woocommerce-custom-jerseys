<?php 

/*
 * @author = Pavel Tocto F.
 * @web = http://www.paveltocto.com
 * 
 */

function log_me($message, $sufijo = "") {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($sufijo . "\t-> " . $message);
        }
    }
}

?>
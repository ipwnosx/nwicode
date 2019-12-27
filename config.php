<?php
/**
 * NwicodeCMS
 *
 * @version 1.3.1
 * @author Nwicode CMS <support@nwicode.com>
 *
 * @configuration
 *
 */

if (!empty($_SERVER["HTTP_ORIGIN"])) {
    header("Access-Control-Allow-Origin: {$_SERVER["HTTP_ORIGIN"]}");
    header("Access-Control-Allow-Credentials: true", true);
    header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,OPTIONS", true);
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, X-HTTP-Method-Override, Content-Type, Accept, Pragma, Set-Cookie", true);
    header("Access-Control-Max-Age: 86400", true);
}

$_config = array();
$_config["environment"] = "production";

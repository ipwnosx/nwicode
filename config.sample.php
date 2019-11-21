<?php
/**
 * Nwicode
 *
 * @version 4.16.7
 * @author Nwicode SAS <dev@nwicode.com>
 *
 * @configuration
 *
 */

$_config = [];
$_config['environment'] = 'production';

try {
    if (is_file(__DIR__ . "/config.user.php")) {
        require __DIR__ . "/config.user.php";
    }
} catch (\Exception $e) {
    // Skip user config!
}
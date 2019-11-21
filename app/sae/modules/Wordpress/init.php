<?php
/**
 * Wordpress Module
 *
 * @param $bootstrap
 */
$init = function($bootstrap) {
    // Exporter!
    Nwicode_Exporter::register('wordpress', 'Wordpress_Model_Wordpress');
};

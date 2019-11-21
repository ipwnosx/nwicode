<?php

/**
 * @param $bootstrap
 */
$init = function ($bootstrap) {
    // Register wordpress v2 scss for dynamic rebuild in colors page!
    \Nwicode_Assets::registerScss([
        "/app/sae/modules/Wordpress2/features/wordpress_v2/scss/wordpress2.scss"
    ]);
};
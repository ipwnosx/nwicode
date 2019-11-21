<?php
/**
 * @param $bootstrap
 */
$init = function ($bootstrap) {
    Nwicode_Privacy::registerModule(
        "fanwall",
        __("Fanwall"),
        "comment/gdpr.phtml");
};
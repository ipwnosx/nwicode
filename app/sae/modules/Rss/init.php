<?php

use Nwicode\Assets;
use Nwicode\Exporter;

$init = function($bootstrap) {
    # Exporter
    Exporter::register("rss_feed", "Rss_Model_Feed");

    Assets::registerScss([
        "/app/sae/modules/Rss/features/rss/scss/rss.scss"
    ]);
};


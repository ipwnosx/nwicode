<?php
$init = function($bootstrap) {
    # Exporter
    Nwicode_Exporter::register("weblink_mono", "Weblink_Model_Type_Mono");
    Nwicode_Exporter::register("weblink_multi", "Weblink_Model_Type_Multi");

};

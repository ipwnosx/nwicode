<?php
$init = function($bootstrap) {
    # Exporter
    Nwicode_Exporter::register("discount", "Promotion_Model_Promotion");
    Nwicode_Exporter::register("qr_discount", "Promotion_Model_Promotion");

};

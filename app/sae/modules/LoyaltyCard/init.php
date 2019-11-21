<?php
$init = function($bootstrap) {
    # Exporter
    Nwicode_Exporter::register("loyalty", "LoyaltyCard_Model_LoyaltyCard");
};

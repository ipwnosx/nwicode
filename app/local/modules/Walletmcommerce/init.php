<?php

$init = function($bootstrap) {

	$base = Core_Model_Directory::getBasePathTo("/app/local/modules/Walletmcommerce/");
	require_once "{$base}/Mcommerce/Model/Db/Table/Payment/Method/Walletmcommerce.php";
	require_once "{$base}/Mcommerce/Model/Payment/Method/Walletmcommerce.php";	
	# Register assets
	Nwicode_Assets::registerAssets("Walletmcommerce", "/app/local/modules/Walletmcommerce/resources/var/apps/");
	Nwicode_Assets::addJavascripts(array(
		"modules/Walletmcommerce/controllers/walletmcommerce.js",
		"modules/Walletmcommerce/factories/walletmcommerce.js",
	));		
};
<?php

$init = function($bootstrap) {

	$base = Core_Model_Directory::getBasePathTo("/app/local/modules/WalletPaypalPS");
	# Register assets
	Nwicode_Assets::registerAssets("WalletPaypalPS", "/app/local/modules/WalletPaypalPS/resources/var/apps/");
	Nwicode_Assets::addJavascripts(array(
		"modules/walletpaypalps/controllers/walletpaypalps.js",
		"modules/walletpaypalps/factories/walletpaypalps.js",
	));	

};
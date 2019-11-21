<?php

$init = function($bootstrap) {

	$base = Core_Model_Directory::getBasePathTo("/app/local/modules/WalletYandexPS");
	# Register assets
	Nwicode_Assets::registerAssets("WalletYandexPS", "/app/local/modules/WalletYandexPS/resources/var/apps/");
	Nwicode_Assets::addJavascripts(array(
		"modules/walletyandexps/controllers/walletyandexps.js",
		"modules/walletyandexps/factories/walletyandexps.js",
	));	

};
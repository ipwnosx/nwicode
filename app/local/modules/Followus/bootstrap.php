<?php
	class Followus_Bootstrap
{

    public static function init($bootstrap) {
	
        Nwicode_Assets::registerAssets("sublock", "/app/local/modules/Followus/resources/var/apps/");
        Nwicode_Assets::addJavascripts(array("modules/followus/libraries/fa5svg.js"));

    }
    
}
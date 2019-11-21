<?php

namespace Nwicode;

/**
 * A collection of stubs, extents & aliases for backward compatibility with "un-namespaced" classes!
 */
class Stubs {
    // Empty class

    public function __construct()
    {
        // Empty one!
    }

    /**
     * Stubs loader!
     */
    public static function loadAliases ()
    {
        class_alias("\Nwicode\Utils", "Utils");
        class_alias("\Nwicode\Utils", "Nwicode_Utils");
        class_alias("\Nwicode\Cron", "Nwicode_Cron");
        class_alias("\Nwicode\Exporter", "Nwicode_Exporter");
        class_alias("\Core\Model\Base", "Core_Model_Default");
        class_alias("\Nwicode\Exception", "Nwicode_Exception");
        class_alias("\Nwicode\Version", "Nwicode_Version");
        class_alias("\Nwicode\Cache", "Nwicode_Cache");
        class_alias("\Nwicode\Cache\Apps", "Nwicode_Cache_Apps");
        class_alias("\Nwicode\Cache\Design", "Nwicode_Cache_Design");
        class_alias("\Nwicode\Cache\Translation", "Nwicode_Cache_Translation");
        class_alias("\Nwicode\Cache\CacheInterface", "Nwicode_Cache_Interface");
        class_alias("\Nwicode\Api", "Nwicode_Api");
        class_alias("\Nwicode\Assets", "Nwicode_Assets");
        class_alias("\Nwicode\Autoupdater", "Nwicode_Autoupdater");
        class_alias("\Nwicode\Json", "Nwicode_Json");
        class_alias("\Nwicode\Image", "Nwicode_Image");
        class_alias("\Nwicode\Minify", "Nwicode_Minify");
        class_alias("\Nwicode\Feature", "Nwicode_Feature");
        class_alias("\Nwicode\Scss", "Nwicode_Scss");
        class_alias("\Nwicode\Yaml", "Nwicode_Yaml");
        class_alias("\Nwicode\Color", "Nwicode_Color");
        class_alias("\Nwicode\Currency", "Nwicode_Currency");
        class_alias("\Nwicode\Date", "Nwicode_Date");
        class_alias("\Nwicode\Debug", "Nwicode_Debug");
        class_alias("\Nwicode\Service", "Nwicode_Service");
        class_alias("\Nwicode\Wrapper\Sqlite", "Nwicode_Wrapper_Sqlite");
        class_alias("\Nwicode\Wrapper\SqliteException", "Nwicode_Wrapper_Sqlite_Exception");
        class_alias("\Nwicode\Cpanel", "Nwicode_Cpanel");
        class_alias("\Nwicode\Cpanel\Api", "Nwicode_Cpanel_Api");
        class_alias("\Nwicode\ZebraImage", "Nwicode_ZebraImage");
        class_alias("\Nwicode\View", "Nwicode_View");
        class_alias("\Nwicode\VestaCP", "Nwicode_VestaCP");
        class_alias("\Nwicode\VestaCP\Api", "Nwicode_VestaCP_Api");
        class_alias("\Nwicode\VestaCP\Client", "Nwicode_VestaCP_Client");
        class_alias("\Nwicode\Exec", "Nwicode_Exec");
        class_alias("\Nwicode\Log", "Nwicode_Log");
        class_alias("\Nwicode\Request", "Nwicode_Request");
        class_alias("\Nwicode\Mail", "Nwicode_Mail");
        class_alias("\Nwicode\Session", "Nwicode_Session");
        class_alias("\Nwicode\Resource", "Nwicode_Resource");
        class_alias("\Nwicode\Privacy", "Nwicode_Privacy");
        class_alias("Nwicode_Layout", "\Nwicode\Layout");
        class_alias("\Nwicode\Layout\Email", "Nwicode_Layout_Email");
    }
}



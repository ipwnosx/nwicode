<?php

class Installer_Backoffice_ModuleController extends Backoffice_Controller_Default {

    /**
     * @var array
     */
    static $MODULES = array();

    /**
     * @var bool
     */
    public $increase_timelimit = false;

    public function loadAction() {

        if(class_exists("Core_Model_Statistics")) {
            $stats = new Core_Model_Statistics();
            $stats->statistics();
        }

        $config = new System_Model_Config();
        $configs = $config->findAll(array(new Zend_Db_Expr('code LIKE "ftp_%"')));
        
        $html = array(
            "title" => __("Modules"),
            "icon" => "fa-cloud-download"
        );

        $this->_sendHtml($html);

    }

    public function downloadupdateAction() {

        try {

            set_time_limit(6000);
            ini_set('max_execution_time', 6000);
            ini_set("memory_limit", "512M");
            $currentversion = Nwicode_Version::VERSION;
            $verionname = '';
            $newversion = "";
            $noupdate = '';
            if(Nwicode_Version::TYPE === 'SAE'){
                $verionname = 'sae';
            }
            if(Nwicode_Version::TYPE === 'MAE'){
                $verionname = 'mae';
            }
            if(Nwicode_Version::TYPE === 'PE'){
                $verionname = 'pe';
            }
                    
            $str = file_get_contents('https://updates.nwicode.com/public/updates/'.$verionname.'/update.json');
            $json = json_decode($str);
            foreach($json->update as $item)
            {
                if($item->latest == $currentversion)
                {
                     $newversion = $item->newversion;
                } 
            }
            $tmpdir = Core_Model_Directory::getTmpDirectory(true)."/nwicode.update.".$newversion.".zip";
            $updatefileurl = "https://updates.nwicode.com/public/updates/".$verionname."/nwicode.update.".$newversion.".zip";
            file_put_contents($tmpdir, file_get_contents($updatefileurl));
            $data["url"] = "https://updates.nwicode.com/public/updates/".$verionname."/nwicode.update.".$newversion.".zip";
            $data["filename"] = "nwicode.update.".$newversion.".zip";
            
            if(!empty($data["url"]) AND !empty($data["filename"])) {

                $tmp_path = Core_Model_Directory::getTmpDirectory(true)."/".$data["filename"];

                # for hotfix ssl
                $client = new Zend_Http_Client($data["url"], array(
                    'adapter'   => 'Zend_Http_Client_Adapter_Curl',
                    'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
                ));

                $client->setMethod(Zend_Http_Client::POST);
                $client->setParameterPost("secret", Core_Model_Secret::SECRET);

                $response = $client->request();

                

                

                $data = $this->_getPackageDetails($tmp_path);
            }

        } catch(Exception $e) {
            if($noupdate = 1) {
                $data = array(
                    "success" => 1,
                    "message" => __("Your system is up to date.")
                );
            } else {
                $data = array(
                    "error" => 1,
                    "message" => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($data);

    }
    
    

    public function downloadupdatemoduleAction() {

        $params = Nwicode_Json::decode($this->getRequest()->getRawBody());
        $moduleupdatepathurl = $params["moduleupdatepathurl"];
        $moduleversion = $params["moduleversion"];
        $modulename = $params["modulename"];
        $noupdate = '';
        try {

            set_time_limit(6000);
            ini_set('max_execution_time', 6000);
            ini_set("memory_limit", "512M");
            $currentversion = $moduleversion;
            $newversion = "";
                    
            $str = file_get_contents('https://'.$moduleupdatepathurl.'/update.json');
            $json = json_decode($str);
            foreach($json->update as $item)
            {
                if($item->latest == $currentversion)
                {
                     $newversion = $item->newversion;
                } else {
                    $noupdate = 1;
                    $message = __("The module version is the most current!");
                    
                    throw new Nwicode_Exception($message);
                    
                }
            
            }
            $tmpdir = Core_Model_Directory::getTmpDirectory(true)."/".$modulename."_v".$newversion.".zip";
            $updatefileurl = "https://".$moduleupdatepathurl."/".$modulename."_v".$newversion.".zip";
            file_put_contents($tmpdir, file_get_contents($updatefileurl));
            $data["url"] = "https://".$moduleupdatepathurl."/".$modulename."_v".$newversion.".zip";
            $data["filename"] = "".$modulename."_v".$newversion.".zip";
            
            if(!empty($data["url"]) AND !empty($data["filename"])) {

                $tmp_path = Core_Model_Directory::getTmpDirectory(true)."/".$data["filename"];

                # for hotfix ssl
                $client = new Zend_Http_Client($data["url"], array(
                    'adapter'   => 'Zend_Http_Client_Adapter_Curl',
                    'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
                ));

                $client->setMethod(Zend_Http_Client::POST);
                $client->setParameterPost("secret", Core_Model_Secret::SECRET);

                $response = $client->request();

                

                

                $data = $this->_getPackageDetails($tmp_path);
            }

        } catch(Exception $e) {
            if($noupdate = 1) {
                $data = array(
                    "success" => 1,
                    "message" => $e->getMessage()
                );
            } else {
                $data = array(
                    "error" => 1,
                    "message" => $e->getMessage()
                );
            }
        }

        $this->_sendHtml($data);

    }

    public function uploadAction() {

        try {

            if(empty($_FILES) || empty($_FILES['file']['name'])) {
                throw new Nwicode_Exception(__("No file has been sent"));
            }

            $adapter = new Zend_File_Transfer_Adapter_Http();
            $adapter->setDestination(Core_Model_Directory::getTmpDirectory(true));

            if($adapter->receive()) {

                $file = $adapter->getFileInfo();

                $data = $this->_getPackageDetails($file['file']['tmp_name']);

            } else {
                $messages = $adapter->getMessages();
                if(!empty($messages)) {
                    $message = implode("\n", $messages);
                } else {
                    $message = __("An error occurred during the process. Please try again later.");
                }

                throw new Nwicode_Exception($message);
            }
        } catch(Exception $e) {
            $data = array(
                "error" => 1,
                "message" => $e->getMessage()
            );
        }

        $this->_sendHtml($data);
    }

    public function checkpermissionsAction() {

        if($file = $this->getRequest()->getParam("file")) {

            $data = array();

            try {

                $filename = base64_decode($file);
                $file = Core_Model_Directory::getTmpDirectory(true)."/$filename";

                if(!file_exists($file)) {
                    throw new Nwicode_Exception(__("The file %s does not exist", $filename));
                }

                $parser = new Installer_Model_Installer_Module_Parser();
                $is_ok = $parser->setFile($file)->checkPermissions();

                if(!$is_ok) {
                    $ftp_host = System_Model_Config::getValueFor("ftp_host");
                    $ftp_user = System_Model_Config::getValueFor("ftp_username");
                    $ftp_password = System_Model_Config::getValueFor("ftp_password");
                    $ftp_port = System_Model_Config::getValueFor("ftp_port");
                    $ftp_path = System_Model_Config::getValueFor("ftp_path");
                    $ftp = new Nwicode_Ftp($ftp_host, $ftp_user, $ftp_password, $ftp_port, $ftp_path);

                    if($ftp->checkConnection() AND $ftp->isNwicodeDirectory()) {
                        $is_ok = true;
                    }
                }

                if($is_ok) {
                    $data = array("success" => 1);
                } else {

                    $messages = $parser->getErrors();
                    $message = implode("\n", $messages);
                    throw new Nwicode_Exception(__($message));
                }

            } catch(Exception $e) {
                $data = array(
                    "error" => 1,
                    "message" => $e->getMessage()
                );
            }

            $this->_sendHtml($data);
        }

    }

    public function saveftpAction() {

        if($data = Zend_Json::decode($this->getRequest()->getRawBody())) {

            try {

                $error_code = 0;
                $ftp_host = !empty($data["host"]) ? $data["host"] : null;
                $ftp_user = !empty($data["username"]) ? $data["username"] : null;
                $ftp_password = !empty($data["password"]) ? $data["password"] : null;
                $ftp_port = !empty($data["port"]) ? $data["port"] : Nwicode_Ftp::DEFAULT_PORT;
                $ftp_path = null;

                if(!empty($data["path"])) {
                    $ftp_path = rtrim($data["path"], "/");
                }
                if(!$ftp_path) {
                    $ftp_path = Nwicode_Ftp::DEFAULT_PATH;
                }

                $ftp = new Nwicode_Ftp($ftp_host, $ftp_user, $ftp_password, $ftp_port, $ftp_path);
                if(!$ftp->checkConnection()) {
                    $error_code = 1;
                    throw new Nwicode_Exception(__("Unable to connect to your FTP. Please check the connection information."));
                } else if(!$ftp->isNwicodeDirectory()) {
                    $error_code = 2;
                    throw new Nwicode_Exception(__("Unable to detect your site. Please make sure the entered path is correct."));
                }

                $fields = array(
                    "ftp_host" => $ftp_host,
                    "ftp_username" => $ftp_user,
                    "ftp_password" => $ftp_password,
                    "ftp_port" => $ftp_port,
                    "ftp_path" => $ftp_path,
                );

                foreach($fields as $key => $value) {
                    $config = new System_Model_Config();
                    $config->find($key, "code");

                    if(!$config->getId()) {
                        $config->setCode($key)
                            ->setLabel(ucfirst(implode(" ", explode("_", $key))))
                        ;
                    }

                    $config->setCode($key)
                        ->setValue($value)
                        ->save()
                    ;
                }

                $data = array(
                    "success" => 1,
                    "message" => __("Info successfully saved")
                );

            } catch(Exception $e) {
                $data = array(
                    "error" => 1,
                    "code" => $error_code,
                    "message" => $e->getMessage()
                );
            }

            $this->_sendHtml($data);
        }

    }

    public function copyAction() {

        if($file = $this->getRequest()->getParam("file")) {

            $data = array();

            try {

                $filename = base64_decode($file);
                $file = Core_Model_Directory::getTmpDirectory(true)."/$filename";

                if(!file_exists($file)) {
                    throw new Nwicode_Exception(__("The file %s does not exist", $filename));
                }

                $parser = new Installer_Model_Installer_Module_Parser();
                if($parser->setFile($file)->copy()) {

                    $data = array("success" => 1);

                } else {

                    $messages = $parser->getErrors();
                    $message = implode("\n", $messages);

                    throw new Nwicode_Exception(__($message));

                }

            } catch(Exception $e) {
                $data = array(
                    "error" => 1,
                    "message" => $e->getMessage()
                );
            }

            $this->_sendHtml($data);
        }

    }

    public function installAction() {

        # Increase the timelimit to ensure update will finish
        //$this->increase_timelimit = set_time_limit(300);

        $data = array();

        try {

            $cache = Zend_Registry::isRegistered('cache') ? Zend_Registry::get('cache') : null;
            if($cache) {
                $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
            }

            $cache_ids = array('js_mobile.js', 'js_desktop.js', 'css_mobile.css', 'css_desktop.css');
            foreach ($cache_ids as $cache_id) {
                if(file_exists(Core_Model_Directory::getCacheDirectory(true)."/{$cache_id}")) {
                    unlink(Core_Model_Directory::getCacheDirectory(true)."/{$cache_id}");
                }
            }

            $module_names = Zend_Controller_Front::getInstance()->getDispatcher()->getSortedModuleDirectories();
            self::$MODULES = array();
            foreach($module_names as $module_name) {
                $module = new Installer_Model_Installer_Module();
                $module->prepare($module_name);
                if($module->canUpdate()) {
                    self::$MODULES[] = $module->getName();
                }
            }

            self::$MODULES = array_unique(self::$MODULES);

            $installers = array();
            foreach(self::$MODULES as $module) {
                $installer = new Installer_Model_Installer();
                $installer->setModuleName($module)
                    ->install()
                ;

                $installers[] = $installer;

                # Try to increase max execution time (if the set failed)
                $this->_signalRetry();
            }

            foreach($installers as $installer) {
                $installer->insertData();

                # Try to increase max execution time (if the set failed)
                $this->_signalRetry();
            }

            /** Try installing fresh template. */
            $installer = new Installer_Model_Installer();
            $installer->setModuleName("Template")
                ->install()
            ;

            /** Clear cache */
            Nwicode_Cache_Design::clearCache();
            Nwicode_Cache_Translation::clearCache();
            Nwicode_Minify::clearCache();

            $host = $this->getRequest()->getHeader("host");
            if($host AND $host == base64_decode("YXBwcy5tb2JpdXNjcy5jb20=")) {
                $email = base64_decode("Y29udGFjdEBzaWJlcmlhbmNtcy5jb20=");
                $object = "$host - Nwicode Update";
                $message = "Nwicode " . Nwicode_Version::NAME . " " . Nwicode_Version::VERSION;
                mail($email, $object, $message);
            }

            $data = array(
                "success" => 1,
                "message" => __("Module successfully installed")
            );

            # Try to increase max execution time (if the set failed)
            $this->_signalRetry();

            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            Nwicode_Autoupdater::configure($protocol.$this->getRequest()->getHttpHost());

            $cron_model = new Cron_Model_Cron();
            $cachebuilder = $cron_model->find("cachebuilder", "command");

            if($cachebuilder->getId()) {
                $options = array(
                    "host" => $protocol.$this->getRequest()->getHttpHost(),
                );
                $cachebuilder->setOptions(Nwicode_Json::encode($options))->save();
                $cachebuilder->enable();
            }

        } catch(Nwicode_Exec_Exception $e) {
            $data = array(
                "success" => 1,
                "reached_timeout" => true,
                "message" => $e->getMessage()
            );
        } catch(Exception $e) {
            $data = array(
                "error" => 1,
                "message" => $e->getMessage()
            );
        }

        $this->_sendHtml($data);

    }

    /**
     * Detect if we are close to the timeout and send a signal to continue the installation process.
     *
     * @todo remove class_exists("Nwicode_Exec") after 4.8.7
     */
    protected function _signalRetry() {
        if(class_exists("Nwicode_Exec") && !$this->increase_timelimit) {
            if(Nwicode_Exec::willReachMaxExecutionTime(5)) {
                throw new Nwicode_Exec_Exception("Installation will continue, please wait ...");
            }
        }
    }

    protected function _fetchUpdates() {

        /** Default updates url in case of missing configuration */
        $updates_url = "https://updates.nwicode.com";

        $update_channel = System_Model_Config::getValueFor("update_channel");
        if(in_array($update_channel, array("stable", "beta"))) {
            switch($update_channel) {
                case "stable":
                    $updates_url = "https://updates.nwicode.com";
                    break;
                case "beta":
                    $updates_url = "https://updates.nwicode.com";
                    break;
            }
        }

        $current_version = Nwicode_Version::VERSION;
        $platform_type = strtolower(Nwicode_Version::TYPE);
        $url = "{$updates_url}/check.php?";
        $url .= "type={$platform_type}&version={$current_version}";

        $client = new Zend_Http_Client($url, array(
            'adapter'   => 'Zend_Http_Client_Adapter_Curl',
            'curloptions' => array(CURLOPT_SSL_VERIFYPEER => false),
        ));

        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost("secret", Core_Model_Secret::SECRET);

        $response = $client->request();

        $content = $response->getBody();

        if(empty($content)) {
            throw new Nwicode_Exception(__("An error occurred while loading. Please, try again later."));
        }

        $content = Zend_Json::decode($content);
        if($response->getStatus() != 200) {

            $message = __("Unable to check for updates now. Please, try again later.");
            if(!empty($content["error"]) AND !empty($content["message"])) {
                $message = __($content["message"]);
            }

            throw new Nwicode_Exception($message);
        } else if(empty($content["url"])) {
            $content["message"] = __("Your system is up to date.");
        }

        return $content;

    }

    protected function _getPackageDetails($file) {

        $installer = new Installer_Model_Installer();
        $installer->parse($file);

        $package = $installer->getPackageDetails();

        $path = pathinfo($file);
        $filename = $path["filename"].".".$path["extension"];

        $data = array(
            "success" => 1,
            "filename" => base64_encode($filename),
            "package_details" => array(
                "name" => __("%s Update", $package->getName()),
                "version" => $package->getVersion(),
                "description" => $package->getDescription()
            )
        );

        $data["release_note"] = array(
            "url" => false,
            "show" => false,
        );

        if(($release_note = $package->getReleaseNote())) {
            $data["release_note"] = $package->getReleaseNote();
        }

        return $data;

    }

}

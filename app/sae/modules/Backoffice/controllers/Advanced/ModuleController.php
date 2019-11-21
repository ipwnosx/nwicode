<?php

/**
 * Class Backoffice_Advanced_ModuleController
 */
class Backoffice_Advanced_ModuleController extends Backoffice_Controller_Default
{

    public function loadAction()
    {
        
        $default = new Core_Model_Default();
        $baseUrl = explode('.', parse_url($default->getBaseUrl())['host'])[0].".".explode('.', parse_url($default->getBaseUrl())['host'])[1];
        
        $payload = [
            'title' => sprintf('%s > %s > %s',
                __('Settings'),
                __('Advanced'),
                __('Modules')),
            'icon' => 'fa-sliders',
            'base_url' => $baseUrl,
            "words" => [
                "confirmDelete" => __("YES I want to proceed with my own risk"),
                "cancelDelete" => __("NO I don't want to proceed"),
                "deleteTitle" => __("WARNING!"),
                "deleteMessage" => __("<b class=\"delete-warning\">Removing a module is a non-reversible action. You will lose all the data related to this module stored in the DB. Also we cannot assure that this will create side-effects with modules that interact with the module that you are going to remove.</b><br />This action can lead to data loss. To prevent accidental actions we ask you to confirm your intention.<br />Please type <code style=\"user-select: none;\">#module_version#</code> to proceed or close this modal to cancel.")
            ],
            "version" => Nwicode_Version::VERSION,
        ];

        $this->_sendJson($payload);
    }

    public function findallAction()
    {

        $core_modules = (new Installer_Model_Installer_Module())->findAll(
            [
                'can_uninstall = ?' => 0,
                'type NOT IN (?)' => ['template']
            ],
            [
                'name ASC'
            ]
        );
        $installed_modules = (new Installer_Model_Installer_Module())->findAll(
            [
                'can_uninstall = ?' => 1,
                'type NOT IN (?)' => ['template']
            ],
            [
                'name ASC'
            ]
        );

        $templates = (new Installer_Model_Installer_Module())->findAll(
            [
                'type IN (?)' => ['template']
            ],
            [
                'name ASC'
            ]
        );

        $features = (new Application_Model_Option())->findAll(
            [],
            [
                'name ASC'
            ]
        );

        $data = [
            'core_modules' => [],
            'modules' => [],
            'layouts' => [],
            'templates' => [],
            'features' => [],
            'icons' => [],
        ];

        foreach ($core_modules as $core_module) {
            $data['core_modules'][] = [
                'id' => $core_module->getId(),
                'name' => __($core_module->getData('name')),
                'original_name' => $core_module->getData('name'),
                'version' => $core_module->getData('version'),
                'actions' => Nwicode_Module::getActions($core_module->getData('name')),
                'created_at' => $core_module->getFormattedCreatedAt(),
                'updated_at' => $core_module->getFormattedUpdatedAt(),
            ];
        }

        foreach ($installed_modules as $installed_module) {
            switch ($installed_module->getData('type')) {
                case 'layout':
                    $type = 'layouts';
                    break;
                case 'icons':
                    $type = 'icons';
                    break;
                default:
                case 'module':
                    $type = 'modules';
                    break;

            }
            $data[$type][] = [
                'id' => $installed_module->getId(),
                'name' => __($installed_module->getData('name')),
                'original_name' => $installed_module->getData('name'),
                'version' => $installed_module->getData('version'),
                'updatepathurl' => $installed_module->getData('updatepathurl'),
                'actions' => Nwicode_Module::getActions($installed_module->getData('name')),
                'created_at' => $installed_module->getFormattedCreatedAt(),
                'updated_at' => $installed_module->getFormattedUpdatedAt(),
            ];
        }

        foreach ($templates as $template) {
            $data['templates'][] = [
                'id' => $template->getId(),
                'name' => __($template->getData('name')),
                'original_name' => $template->getData('name'),
                'version' => $template->getData('version'),
            ];
        }

        foreach ($features as $feature) {
            $data['features'][] = [
                'id' => $feature->getId(),
                'name' => $feature->getName(),
                'code' => $feature->getCode(),
                'description' => $feature->getBackofficeDescription(),
                'is_enabled' => (boolean) $feature->getIsEnabled(),
            ];
        }

        $this->_sendJson($data);

    }

    public function togglefeatureAction()
    {
        try {
            $params = Nwicode_Json::decode($this->getRequest()->getRawBody());
            $featureId = $params['featureId'];
            $isEnabled = filter_var($params['isEnabled'], FILTER_VALIDATE_BOOLEAN);

            if (!$featureId) {
                throw new Nwicode_Exception(__('Missing parameters!'));
            }

            $feature = (new Application_Model_Option())
                ->find($featureId);

            if (!$feature->getId()) {
                throw new Nwicode_Exception(__("The feature you are trying to edit doesn't exists!"));
            }

            $feature
                ->setIsEnabled($isEnabled)
                ->save();

            $payload = [
                'success' => true,
                'message' => __('Feature is now %s', ($isEnabled) ? __('enabled') : __('disabled'))
            ];
        } catch (Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * Execute a related module action
     */
    public function executeAction()
    {

        $params = Nwicode_Json::decode($this->getRequest()->getRawBody());
        $module = $params["module"];
        $action = $params["action"];

        try {

            if ($actions = Nwicode_Module::getActions($module)) {
                if (isset($actions[$action])) {
                    $module_action = $actions[$action];

                    if (strpos($module_action["action"], "::") !== false) {
                        $parts = explode("::", $module_action["action"]);
                        $class = $parts[0];
                        $method = $parts[1];
                        if (class_exists($class) && method_exists($class, $method) && call_user_func($parts)) {
                            $data = [
                                "success" => 1,
                                "message" => __("Action '{$action}' executed for module '{$module}'."),
                            ];
                        } else {
                            throw new Exception(__("Unknown action for this module."));
                        }
                    } else {
                        throw new Exception(__("Unknown action for this module."));
                    }
                } else {
                    throw new Exception(__("Unknown action for this module."));
                }
            } else {
                throw new Exception(__("Unknown action for this module."));
            }

        } catch (Exception $e) {
            $data = [
                "error" => 1,
                "message" => $e->getMessage(),
            ];
        }

        $this->_sendHtml($data);
    }
    
    
    // Удаление модулей 
    
    public function moduledeleteAction() {
        $payload =['empty'=> 'empty'];
        if ($this->getRequest()->getParam('modulename')) {
            $module_name = $this->getRequest()->getParam('modulename');
            $module_version = $this->getRequest()->getParam('moduleversion');
            $moduleuninstall    = new Backoffice_Model_Moduleuninstall();
            $module_details   = $moduleuninstall->getModuleCode($module_name);
            $module_option_id = $module_details[0]['option_id'];
            $module_code      = $module_details[0]['code'];
            $baseUrl          = Core_Model_Directory::getBasePathTo("");
            $dir = $baseUrl."app/local/modules/".$module_name;
            $schema_path="xyz";
            if(is_dir($dir)){
                $schema_path = $dir."/resources/db/schema/*.php";
            }else{
                $dir = $baseUrl."app/sae/modules/".$module_name;
                $schema_path  = $dir."/resources/db/schema/*.php";
            }
            $var_apps=[
                $baseUrl."app/local/modules/".$module_name."/.htaccess",
                $baseUrl."app/sae/modules/".$module_name."/.htaccess",
                $baseUrl."app/local/modules/".$module_name."/.gitignore",
                $baseUrl."app/sae/modules/".$module_name."/.gitignore",
                $baseUrl."app/local/modules/".$module_name,
                $baseUrl."app/sae/modules/".$module_name,
                $baseUrl."var/apps/ionic/android/assets/www/modules/".$module_code,
                $baseUrl."var/apps/ionic/android/app/src/main/assets/www/modules/".$module_code,
                $baseUrl."var/apps/ionic/android/app/src/main/assets/www/features/".$module_code,
                $baseUrl."var/apps/ionic/ios/www/features/".$module_code,
                $baseUrl."var/apps/ionic/ios/www/modules/".$module_code,
                $baseUrl."var/apps/ionic/ios-noads/www/modules/".$module_code,
                $baseUrl."var/apps/overview/features/".$module_code,
                $baseUrl."var/apps/overview/modules/".$module_code,
                $baseUrl."var/apps/browser/features/".$module_code,
                $baseUrl."var/apps/browser/modules/".$module_code,
                $baseUrl."/overview/dist/app.bundle-min.js",
                $baseUrl."/browser/dist/app.bundle-min.js"
            ];
            $files = array();
            foreach (glob($schema_path) as $file) {
                $files[] = $file;
            }
            $tables="";
            foreach ($files as $key => $value) {
                $pieces      = explode("/", $value);
                $file_pieces = explode(".", $pieces[13]);
                $tables[]    = $file_pieces[0];
            }
            try{
                $moduleuninstall->clearScrap($module_option_id,$module_name);
                $moduleuninstall->tablesDeletion($tables);
                Nwicode_Feature::removeIcons($module_name);
                Nwicode_Feature::removeIcons("{$module_name}-flat");
                $layout_data = [1];
                $slug = $module_code;
                Nwicode_Feature::removeLayouts($module_option_id, $slug, $layout_data);
                Nwicode_Feature::uninstallFeature($module_code);
                foreach ($var_apps as $key => $value) {
                    Backoffice_Advanced_ModuleController::deleteDir($value);
                }
                $payload = [
                    "success"       => true,
                    "message"       => __("successfully deleted Module"),
                ];
            } catch(Exception $e) {
                $payload = [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            }
        }
        $this->_sendJson($payload);
    }
    
    public function clearcacheAction() {  
        try{
            Nwicode_Cache::__clearLog();
            Nwicode_Cache::__clearCache();
            Nwicode_Cache::__clearTmp();
            Application_Model_SourceQueue::clearPaths();
            Application_Model_ApkQueue::clearPaths();
            Nwicode_Minify::clearCache();
            $default_cache = Zend_Registry::get("cache");
            $default_cache->clean(Zend_Cache::CLEANING_MODE_ALL);
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            Nwicode_Autoupdater::configure($protocol.$this->getRequest()->getHttpHost());
            $payload = [
                "success"       => true,
                "message"       =>  __("Rebuilding application manifest files."),
                "server_usage"  => Nwicode_Cache::getDiskUsage(),
                "services"      => Nwicode_Service::getServices(),
            ];
        } catch(Exception $e) {
            $payload = [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
        $this->_sendJson($payload);
    }
    
    public static function deleteDir($dirPath) {
        try{
            if (! is_dir($dirPath)) {
                if(is_file($dirPath)){
                    chmod($dirPath,0755);
                    unlink($dirPath);
                }
                return;
                exit;
            }
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                chmod($file,0755);
                if (is_dir($file)) {
                    Backoffice_Advanced_ModuleController::deleteDir($file);
                } elseif (is_file($file)) {
                    unlink($file);
                }else {
                    break;
                }
            }
            rmdir($dirPath);
        }catch(Exception $e){
            $html = array(
                'error' => 1,
                'message' => $e->getMessage()
            );
        }
        return $html;
    }
    
    public function replace_in_file($FilePath, $OldText, $NewText)
    {
        $Result = array('status' => 'error', 'message' => '');
        if(file_exists($FilePath)===TRUE) {
            if(is_writeable($FilePath)) {
                try {
                    $FileContent = file_get_contents($FilePath);
                    $FileContent = str_replace($OldText, $NewText, $FileContent);
                    if(file_put_contents($FilePath, $FileContent) > 0){
                        $Result["status"] = __('Successfully Rolled back nwicode version to ').$NewText;
                    }
                    else{
                       $Result["message"] = __('Error while writing file');
                    }
                }
                catch(Exception $e){
                    $Result["message"] = __('Error : ').$e;
                }
            }
            else{
                $Result["message"] = 'File '.$FilePath.' is not writable !';
            }
        }
        else{
            $Result["message"] = 'File '.$FilePath.' does not exist !';
        }
        return $Result;
    }
    
    // Удаление модулей

}

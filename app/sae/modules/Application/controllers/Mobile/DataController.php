<?php

class Application_Mobile_DataController extends Application_Controller_Mobile_Default {

    public function findallAction() {

        $application = $this->getApplication();
        $pages = $application->getOptions();

        $request = $this->getRequest();
        $base_url = $request->getBaseUrl();

        $paths = array();
        $assets = array();

        $paths[] = __path("front/mobile/loadv3");
        $paths[] = __path("front/mobile/touched");
        $paths[] = __path("front/mobile/backgroundimages", array(
            'device_width' => $request->getParam("device_width"),
            'device_height' => $request->getParam("device_height"),
        ));
        $assets[] = $this->getApplication()->getHomepageBackgroundImageUrl();
        $assets[] = $this->getApplication()->getHomepageBackgroundImageUrl("hd");
        $assets[] = $this->getApplication()->getHomepageBackgroundImageUrl("tablet");

        foreach ($pages as $page) {

            try{
                $model = $page->getModel();
                if(class_exists($model)) {
                    $object = new $model();
                } else {
                    throw new Nwicode_Exception(__("Application_Mobile_DataController::findall, class: {$model} doesn't exists."));
                }


                if (!$page->isActive() OR (!$page->getIsAjax() AND $page->getObject()->getLink())) {
                    continue;
                }

                if(!$object->getTable() || is_a($object, "Push_Model_Message")) {
                    $feature = $page->getObject();

                    if(!$feature->isCacheable()) continue;
                    
                    $fpaths = $feature->getFeaturePaths($page);
                    if(is_array($fpaths)) $paths = array_merge($paths, $fpaths);

                    $fassets = $feature->getAssetsPaths($page);
                    if(is_array($fassets)) $assets = array_merge($assets, $fassets);
                } else {
                    $features = $object->findAll(array("value_id" => $page->getId()));

                    foreach ($features as $feature) {
                        if(!$feature->isCacheable()) continue;

                        $fpaths = $feature->getFeaturePaths($page);
                        if(is_array($fpaths)) $paths = array_merge($paths, $fpaths);

                        $fassets = $feature->getAssetsPaths($page);
                        if(is_array($fassets)) $assets = array_merge($assets, $fassets);
                    }
                }
            } catch(Exception $e) {
                # Catch not working modules silently.
            }

        }

        foreach($paths as $key => $path) {
            $path = trim($path);
            if(strlen($path) > 0 && strpos($path, "http") !== 0) {
                $path = $base_url . $path;
            }
            $paths[$key] = $path;
        }

        foreach($assets as $key => $path) {
            $path = trim($path);
            if(strlen($path) > 0 && strpos($path, "http") !== 0) {
                $path = $this->clean_url($base_url . $path);
            }
            $assets[$key] = $path;
        }

        sort($paths);
        sort($assets);

        $paths = array_values(array_filter(array_values(array_unique(array_values($paths)))));
        $assets = array_values(array_filter(array_values(array_unique(array_values($assets)))));

        $this->_sendJson(array(
            "paths" => is_array($paths) ? $paths : array(),
            "assets" => is_array($assets) ? $assets : array()
        ));
    }
    
    public function findappmetricakeyAction() {
        $application = $this->getApplication();
        $app_id = $application->getId();
        $db =Zend_Db_Table_Abstract::getDefaultAdapter();
        $appmetrica_key = $db->query('SELECT * FROM application WHERE app_id = ? ORDER BY appmetrica_key DESC LIMIT 1',array($app_id))->fetch()['appmetrica_key'];
        $html = array(
            'appmetrica_key' => $appmetrica_key,
        );
        $this->_sendHtml($html);
    }

    public function gettoursettingsAction() {
        $application = $this->getApplication();
        $app_id = $application->getId();
        $html = array(
            'enable_tour' => $application->getData('enable_tour'),
            'tour_uid' => $application->getData('tour_uid'),
            'tour_slide_1' => ($application->getData('tour_slide_1')!="")?$this->getRequest()->getBaseUrl()."/images/application".$application->getData('tour_slide_1'):"",
            'tour_slide_2' => ($application->getData('tour_slide_2')!="")?$this->getRequest()->getBaseUrl()."/images/application".$application->getData('tour_slide_2'):"",
            'tour_slide_3' => ($application->getData('tour_slide_3')!="")?$this->getRequest()->getBaseUrl()."/images/application".$application->getData('tour_slide_3'):"",
            'tour_slide_4' => ($application->getData('tour_slide_4')!="")?$this->getRequest()->getBaseUrl()."/images/application".$application->getData('tour_slide_4'):"",
            'tour_slide_5' => ($application->getData('tour_slide_5')!="")?$this->getRequest()->getBaseUrl()."/images/application".$application->getData('tour_slide_5'):"",
            'tour_title_1' => $application->getData('tour_title_1'),
            'tour_title_2' => $application->getData('tour_title_2'),
            'tour_title_3' => $application->getData('tour_title_3'),
            'tour_title_4' => $application->getData('tour_title_4'),
            'tour_title_5' => $application->getData('tour_title_5'),
            'tour_subtitle_1' => $application->getData('tour_subtitle_1'),
            'tour_subtitle_2' => $application->getData('tour_subtitle_2'),
            'tour_subtitle_3' => $application->getData('tour_subtitle_3'),
            'tour_subtitle_4' => $application->getData('tour_subtitle_4'),
            'tour_subtitle_5' => $application->getData('tour_subtitle_5'),
            'tour_title_color' => $application->getData('tour_title_color'),
            'tour_title_color_type' => $application->getData('tour_title_color_type'),
            'tour_subtitle_color' => $application->getData('tour_subtitle_color'),
            'tour_subtitle_color_type' => $application->getData('tour_subtitle_color_type'),
            'tour_header'  => $application->getData('tour_header'),
            'tour_tbs'  => $application->getData('tour_tbs'),
            'tour_lbt'  => $application->getData('tour_lbt'),
            'tour_tbc'  => $application->getData('tour_tbc'),
        );
        $this->_sendHtml($html);
    }
}

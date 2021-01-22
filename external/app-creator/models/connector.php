<?php
/**
 * @package     app-creator
 * @author      App Creator Team
 * @copyright   2013-2015 App Creator
 * @version     1.1.0
 */
class App_Creator_Connector {

    function __construct() {
        $this->_prepare();
    }

    public function template_redirect() {
        if (isset($_REQUEST['app-creator-api'])) {
            $this->_run();
            exit;
        }
    }

    protected function _prepare() {
        add_action('template_redirect', array(&$this, 'template_redirect'));
    }

    protected function _run() {

        if (!isset($_REQUEST['object']))
            throw new Exception('Error');

        ini_set('display_errors', 1);

        $objectname = $_REQUEST['object'];
        $classname = 'App_Creator_' . ucfirst($objectname);

        try {

            include_once APP_CREATOR_BASE_PATH . "/models/$objectname.php";

            if (class_exists($classname)) {
                $object = new $classname();
                $datas[$objectname] = $object->getDatas();
                $datas['status'] = 1;
            }
        } catch (Exception $e) {
            $datas = array(
                'status' => -1,
                'message' => $e->getMessage()
            );
        }

        $charset = get_option('blog_charset');
        if (!headers_sent()) {
            header('HTTP/1.1 200 OK', true);
            header("Content-Type: application/json; charset=$charset", true);
        }

        if(!function_exists('json_encode')) {
            if (!class_exists('Services_JSON')) {
                $dir = json_api_dir();
                require_once APP_CREATOR_BASE_PATH . "/library/JSON.php";
            }
            $json_service = new Services_JSON();
            $json = $json_service->encode($data);
        }
        echo json_encode($datas);
        die;
    }

}
<?php

use Nwicode\Version;

/**
 * Class System_Controller_Backoffice_Default
 */
class System_Controller_Backoffice_Default extends Backoffice_Controller_Default
{
    /**
     * @var string
     */
    static public $crmApiUrl = "http://key.appglobus.pro";
    static public $api_key_secret = "0usLiJzAx0ariSxm";

    /**
     *
     */
    public function findallAction()
    {
        $this->_sendJson($this->_findconfig());
    }

    /**
     * @throws Zend_Json_Exception
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        $params = $request->getBodyParams();
        if (!empty($params)) {
            try {
                $this->_save($params);
                $payload = [
                    "success" => true,
                    "message" => __("Info successfully saved")
                ];
            } catch (\Exception $e) {
                $payload = [
                    "error" => true,
                    "message" => $e->getMessage()
                ];
            }

        } else {
            $payload = [
                "error" => true,
                "message" => __("An error occurred while saving")
            ];
        }

        $this->_sendJson($payload);
    }

    /**
     * @return array
     */
    protected function _findconfig()
    {
        $values = (new System_Model_Config())
            ->findAll(["code IN (?)" => $this->_codes]);

        $data = [];
        foreach ($this->_codes as $code) {
            $data[$code] = [];
        }

        foreach ($values as $value) {
            $data[$value->getCode()] = [
                "code" => $value->getCode(),
                "label" => __($value->getLabel()),
                "value" => $value->getValue()
            ];
        }

        # Custom SMTP
        $api_model = new Api_Model_Key();
        $keys = $api_model::findKeysFor("smtp_credentials");
        $data["smtp_credentials"] = $keys->getData();

        return $data;
    }

    /**
     * @param $data
     * @return $this
     * @throws Exception
     * @throws Nwicode_Exception
     * @throws Zend_Exception
     */
    protected function _save($data, $license=0)
    {
        if (__getConfig('is_demo')) {
            // Demo version
            throw new \Nwicode\Exception("This is a demo version, these changes can't be saved");
        }

        # Required fields
        if (array_key_exists("main_domain", $data)) {
            // Raise error if empty!
            if (empty($data['main_domain']['value'])) {
                throw new \Nwicode\Exception('#797-00: ' . __('Main domain is required!'));
            }

            // If input matches https?:// extract host part before saving!
            if (preg_match('/^https?:\/\//', $data['main_domain']['value'])) {
                $data['main_domain']['value'] = parse_url($data['main_domain']['value'], PHP_URL_HOST);
            }
        }

        # Custom SMTP
        $this->_saveSmtp($data);

        foreach ($data as $code => $values) {
            if (empty($code)) {
                continue;
            }
            if (!in_array($code, $this->_codes)) {
                continue;
            }
            if ($code === 'app_default_identifier_android') {
                    $regexAndroid = "/^([a-z]{1}[a-z_]*){2,10}\.([a-z]{1}[a-z0-9_]*){1,30}((\.([a-z]{1}[a-z0-9_]*){1,61})*)?$/i";

                if (preg_match($regexAndroid, $values['value']) !== 1) {
                    throw new \Nwicode\Exception(__("Your package name is invalid, format should looks like com.mydomain.androidid"));
                }
            }

            if ($code === 'app_default_identifier_ios') {
                $regexIos = "/^([a-z]){2,10}\.([a-z-]{1}[a-z0-9-]*){1,30}((\.([a-z-]{1}[a-z0-9-]*){1,61})*)?$/i";

                if (preg_match($regexIos, $values['value']) !== 1) {
                    throw new \Nwicode\Exception(__("Your bundle id is invalid, format should looks like com.mydomain.iosid"));
                }
            }

            if ($code === 'favicon') {
                continue;
            }
            $config = new System_Model_Config();
            $config->find($code, "code");
            $config->setValue($values["value"])->save();
            if($code === 'nwicodecms_key' && $license) {
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $protocol = 'https://';
                } else {
                    $protocol = 'http://';
                }
                $client = new Zend_Http_Client($protocol.$_SERVER["HTTP_HOST"]."/install.php");
                $client->setMethod(Zend_Http_Client::POST);
                $client->setAdapter('Zend_Http_Client_Adapter_Curl');
                $client->setHeaders(["Content-type" => 'application/json']);
                $client->setParameterPost([
                   'api_type' => $licensekey,
                   'CLIENT_EMAIL' => 'test@test.tu',
                   'LICENSE_CODE' => $values['value'],
                   'ROOT_URL' => $protocol.$_SERVER["HTTP_HOST"],
                   'submit_ok' => 1,
                ]);
                $response = $client->request();
                $ttessqqqq =  $response->getBody();
                $content = json_decode($ttessqqqq ,true);
            }
            __set($code, $values['value']);
        }

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function _saveSmtp($data)
    {
        if (!isset($data["smtp_credentials"])) {
            return $this;
        }

        $_data = $data["smtp_credentials"];

        $api_provider = new Api_Model_Provider();
        $api_key = new Api_Model_Key();

        $provider = $api_provider->find("smtp_credentials", "code");
        if ($provider->getId()) {
            $keys = $api_key->findAll(["provider_id = ?" => $provider->getId()]);
            foreach ($keys as $key) {
                $code = $key->getKey();
                if (isset($_data[$code])) {
                    $key->setValue($_data[$code])->save();
                }
            }
        }

        return $this;
    }

    /**
     *
     */
    public function generateanalyticsAction()
    {
        try {
            Analytics_Model_Aggregate::getInstance()->run(time() - 60 * 60 * 24);
            Analytics_Model_Aggregate::getInstance()->run(time());
            Analytics_Model_Aggregate::getInstance()->run(time() + 60 * 60 * 24);

            $payload = [
                "success" => 1,
                "message" => __("Your analytics has been computed.")
            ];
        } catch (Exception $e) {
            $payload = [
                "error" => 1,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($payload);
    }
	
	/**
     *
     */
    public function generateanalyticsforperiodAction()
    {
        try {
            $data = Zend_Json::decode($this->getRequest()->getRawBody());
            if (count($data) !== 2) {
                throw new Exception("No period sent.");
            }

            $from = new Zend_Date($data['from'], __("MM/dd/yyyy"));
            $to = new Zend_Date($data['to'], __("MM/dd/yyyy"));

            $fromTimestamp = $from->toValue();
            $toTimestamp = $to->toValue();

            if ($fromTimestamp > $toTimestamp) {
                throw new Exception("Invalid period, end date is before start date.");
            }

            if ($toTimestamp - $fromTimestamp > 60 * 60 * 24 * 31) {
                throw new Exception("Period to long, please select less than one month.");
            }

            $currentTimestamp = $fromTimestamp;
            while ($currentTimestamp <= $toTimestamp) {
                Analytics_Model_Aggregate::getInstance()->run($currentTimestamp);
                $currentTimestamp += 60 * 60 * 24;
            }

            $data = [
                "success" => 1,
                "message" => __("Your analytics has been computed.")
            ];

        } catch (Exception $e) {
            $data = [
                "error" => 1,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendHtml($data);

    }
    public function checknwicodecmslicenseAction() {
        try {
            $licensekey = self::$api_key_secret;
            $client = new Zend_Http_Client();
            $client->setUri(self::$crmApiUrl."/apl_api/api.php");
            $client->setMethod(Zend_Http_Client::POST);
            $client->setAdapter('Zend_Http_Client_Adapter_Curl');
            $client->setHeaders(["Content-type" => 'application/json']);
            $client->setParameterPost([
               'api_key_secret' => $licensekey,
               'api_function' => 'search',
               'search_type' => 'license',
               'search_keyword' => System_Model_Config::getValueFor('nwicodecms_key'),
            ]);
            $response = $client->request();
            $ttessttt =  $response->getBody();
            $content = json_decode($ttessttt ,true);
            if(
                !isset($content["page_message"][0]) ||
                !isset($content["page_message"][0]["license_domain"]) ||
                (
                    isset($content["page_message"][0]["license_domain"]) &&
                    $content["page_message"][0]["license_domain"]!==$_SERVER["HTTP_HOST"]
                ) ||
                isset($content['error']) || $response->getStatus() !== 200
            ) {
                throw new Exception(__("Invalid license key"));
            }
            $data = [
                "message" => __("License is valid")
            ];
        } catch (Exception $e) {
            $data = [
                "error" => 1,
                "message" => $e->getMessage()
            ];
        }
        $this->_sendHtml($data);
    }
}
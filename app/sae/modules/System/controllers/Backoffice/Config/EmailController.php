<?php

/**
 * Class System_Backoffice_Config_EmailController
 */
class System_Backoffice_Config_EmailController extends System_Controller_Backoffice_Default
{
    /**
     * @var array
     */
    protected $_codes = [
        "support_name",
        "support_email",
        "support_link",
        "support_chat_code",
        "custom_buttonone",
        "custom_buttononename",
        "custom_buttontwo",
        "custom_buttontwoname",
        "custom_buttonthree",
        "custom_buttonthreename",
        "custom_html_code",
        "enable_custom_smtp",
        "enable_doforme_button",
        "show_supportform_inhome",
        "editor_design"
    ];

    /**
     *
     */
    public function loadAction()
    {
        $payload = [
            "title" => sprintf('%s > %s',
                __('Settings'),
                __('Communications')),
            "icon" => "fa-exchange",
        ];

        $this->_sendJson($payload);
    }

    public function testsmtpAction()
    {
        $request = $this->getRequest();
        $params = $request->getParams();

        try {
            if (!isset($params["email"])) {
                throw new Nwicode_Exception(__("E-mail is required in order to test SMTP"));
            }

            $mail = new Nwicode_Mail();
            $mail->setBodyHtml("This is a test e-mail.");
            $mail->addTo($params["email"]);
            $mail->test();

            $data = [
                "success" => true,
                "message" => __("A e-mail has been sent to %s, please check your inbox.", $params["email"])
            ];

        } catch (Exception $e) {
            $data = [
                "error" => true,
                "message" => $e->getMessage()
            ];
        }

        $this->_sendJson($data);
    }

}

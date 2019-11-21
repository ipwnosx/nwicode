<?php
/**
 * Class Application_Form_Customization_Publication_App
 */
class Application_Form_Customization_Publication_Tour extends Nwicode_Form_Abstract {

    public $color = "color-purple";

    public function init() {

        parent::init();

        $this
            ->setAction(__path("/application/customization_publication_app/tourpost"))
            ->setAttrib("id", "form-application-tour")
        ;

        /** Bind as a onchange form */
        self::addClass("onchange", $this);
        $ = $this->addSimpleImage("icon", __("Application icon"), __("Application icon"), array(
            "width" => 256,
            "height" => 256,
            "required" => true,
        ));
        $application_icon->setRequired(true);
    }


}
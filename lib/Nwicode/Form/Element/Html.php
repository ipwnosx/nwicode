<?php
/**
 * Class Nwicode_Form_Element_Html
 */
class Nwicode_Form_Element_Html extends Zend_Form_Element_Xhtml {

    public $helper = "formHtml";

    /**
     * @var bool
     */
    public $is_form_horizontal = true;

    /**
     * @param $boolean
     */
    public function setIsFormHorizontal($boolean) {
        $this->is_form_horizontal = $boolean;
    }

    /**
     * @var string
     */
    public $color = "color-blue";

    /**
     * @param $color
     */
    public function setColor($color) {
        $this->color = $color;
    }

	/**
	 * @throws Zend_Form_Exception
	 */
	public function init(){
        $this->getView()->addHelperPath('Nwicode/View/Helper/', 'Nwicode_View_Helper');
		$this->addPrefixPath('Nwicode_Form_Decorator_', 'Nwicode/Form/Decorator/', 'decorator');
		$this->setDecorators([
	  		'ViewHelper',
            [['controls' => 'HtmlTag'], [
                'tag'   => 'div',
                'class' => 'controls',
            ]],
            ['ControlGroup']
        ]);
	}
	
	/**
	 * @return Nwicode_Form_Element_Text
	 */
	public function setNewDesign($class = ""){
		$this->addClass('sb-form-html');

		return $this->setDecorators([
	  		'ViewHelper',
			[['wrapper'=>'HtmlTag'], [
				'class' => ""
            ]],
            ['ControlGroup', [
            	'class' => 'form-group sb-form-line '.$class
            ]]
        ]);
	  	
	}

	/**
	 * @param $new
	 * @return Nwicode_Form_Element_Text
	 */
	public function addClass($new) {
	    return Nwicode_Form_Abstract::addClass($new, $this);
	}
}
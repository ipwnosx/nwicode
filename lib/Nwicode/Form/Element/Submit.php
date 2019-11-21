<?php
/**
 * Class Nwicode_Form_Element_Submit
 */
class Nwicode_Form_Element_Submit extends Zend_Form_Element_Submit {

    /**
     * @var bool
     */
    public $is_form_horizontal = true;

    /**
     * @var string
     */
    public $color = "color-blue";

    /**
     * @param $boolean
     */
    public function setIsFormHorizontal($boolean) {
        $this->is_form_horizontal = $boolean;
    }

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

		$this
			->setAttrib('class', 'btn')
			->setAttrib('data-loading-text', __("Patientez ..."))
		;
		$this->setDecorators([
  			'ViewHelper',
			['HtmlTag', [
				'class'=>'form-actions'
            ]]
        ]);
	}

	/**
	 * @return Nwicode_Form_Element_Submit
	 */
	public function setNewDesign(){
		$this->addClass($this->color);
		return $this->setDecorators([
  			'ViewHelper',
			['HtmlTag', [
				'class' => 'sb-save-info-button'
            ]]
        ]);
	}

	/**
	 * @param $new
	 * @return Nwicode_Form_Element_Submit
	 */
    public function addClass($new) {
	    return Nwicode_Form_Abstract::addClass($new, $this);
	}

}
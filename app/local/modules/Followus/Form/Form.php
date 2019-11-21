<?php
class Followus_Form_Form extends Nwicode_Form_Abstract
{
    public function init() {

		parent::init();
		
        $this
			//->addNav('form-test-nav');
			->setAction(__path("/followus/application/editpost"))
			->setAttrib("id", "followus_chat_form");
		self::addClass('create', $this);
		

		$this->addSimpleHtml("breakline","<br>");			
		$this->addSimpleText('title', __('Title'));
        $richtext = $this->addSimpleTextarea('description',  __('Description'));
        $richtext->setRichtext();		
        $this->addSimpleSelect(
            'designt_type', 
            __('Design type'), array('0','1')
        );		

        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $jivo_chat_id = $this->addSimpleHidden("chat_id");
        $jivo_chat_id->setRequired(true);

		$this->addSubmit(__('Save'));
		$this->addSimpleHtml("breakline-2","<br>");	
	}

	//overwrite parent method
    public function addNav($name, $save_text = "OK", $display_back_button = true, $with_label = false)
    {
        $elements = [];

        $back_button = new Nwicode_Form_Element_Button("sb-back");
        $back_button->setAttrib("escape", false);
        $back_button->setLabel("<i class=\"fa fa-angle-left \"></i>");
        $back_button->addClass("pull-left feature-back-button default_button");
        $back_button->setColor($this->color);
        $back_button->setBackDesign();

        if ($display_back_button) {
            $elements[] = $back_button;
        }

        $submit_button = new Nwicode_Form_Element_Submit(__($save_text));
        $submit_button->addClass("pull-right default_button");
        $submit_button->setColor($this->color);
        $submit_button->setNewDesign();


		
        if ($with_label) {
            $submit_button->setLabel(__($save_text));
            $submit_button->setValue($name);
        }

        $elements[] = $submit_button;
        

        $this->addDisplayGroup($elements, $name);

        $nav_group = $this->getDisplayGroup($name);
        $nav_group->removeDecorator('DtDdWrapper');
        $nav_group->setAttrib("class", "sb-nav");

        return $nav_group;
    }

}
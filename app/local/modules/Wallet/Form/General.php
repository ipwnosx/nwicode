<?php
class Wallet_Form_General extends Nwicode_Form_Abstract
{
    public function init() {

		parent::init();
		
        $this
			->setAction(__path("/wallet/application/editpostgeneral"))
			->setAttrib("id", "wallet_form");
		self::addClass('create', $this);
		

		$this->addSimpleHtml("breakline","<br>");			

		

        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $wallet_id = $this->addSimpleHidden("wallet_id");
        $wallet_id->setRequired(true);

		$this->addSubmit(__('Save'));
	}



}
<?php

/**
 * Class Folder2_Form_Category
 */
class Folder2_Form_Category extends Nwicode_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path('/folder2/application/editpost'))
            ->setAttrib('id', 'form-folder-category');

        // Bind as a create form!
        self::addClass('create', $this);
        self::addClass('callback', $this);
        self::addClass('folderForm', $this);
        $this->addSimpleHtml("css",'
            <script src="/app/sae/design/desktop/flat/js/vanilla-picker.js"></script>
		');
        $this->addSimpleHtml('form_header', '<p></p>');

        $title = $this->addSimpleText('title', __('Title'));
        $title->setRequired(true);

        $subtitle = $this->addSimpleText('subtitle', __('Subtitle'));

        $layout = $this->addSimpleSelect('layout_id', __('Override layout'), [
            '-1' => __('Inherit global layout'),
            '1' => __('Layout #1'),
            '2' => __('Layout #2'),
            '3' => __('Layout #3'),
            '4' => __('Layout #4'),
            '5' => __('Layout #5'),
            '6' => __('Layout #6'),
        ]);

        //$thumbnailIconSize = $this->addSimpleSlider('icon_size', __('Icons size'), [], true);

        $show_cover = $this->addSimpleCheckbox('show_cover', __('Show cover'));
        $show_title = $this->addSimpleCheckbox('show_title', __('Show title'));
        $cardcustomcolor = __("Card custom color");
        $this->addSimpleHidden('cardcustomcolor');
        $this->addSimpleHtml("color_butoncardcustomcolor",'
            <div class="col-md-6"><label for="basiccardcustomcolor" class="sb-form-line-title optional">'.$cardcustomcolor.'</label></div>
            <div class="col-md-4"><a href="#" class="button" id="basiccardcustomcolor"></a></div>
            <script type="text/javascript">
                new Picker({
                    parent: document.querySelector("#basiccardcustomcolor"),
                    value: document.querySelector("#cardcustomcolor")});
                    var parentBasiccardcustomcolor = document.querySelector(\'#basiccardcustomcolor\');
                    var parentBasicvaluecardcustomcolor = document.getElementById(\'cardcustomcolor\');
                    popupBasiccardcustomcolor = new Picker(parentBasiccardcustomcolor, parentBasicvaluecardcustomcolor);
                    popupBasiccardcustomcolor.onChange = function(color) {
                        parentBasiccardcustomcolor.style.background = color.rgbaString,
                        parentBasicvaluecardcustomcolor.setAttribute(\'value\', color.rgbaString);
                    };
            </script>
        ');

        $picture = $this->addSimpleImage(
            'picture',
            __('Cover'),
            __('Import a cover image'),
            [
                'width' => 960,
                'height' => 600,
                'required' => true
            ]);
        $picture
            ->addClass('default_button')
            ->addClass('form_button');

        $thumbnail = $this->addSimpleImage(
            'thumbnail',
            __('Thumbnail'),
            __('Import a thumbnail image'),
            [
                'width' => 512,
                'height' => 512,
                'required' => true
            ]);
        $thumbnail
            ->addClass('default_button')
            ->addClass('form_button');

        $categoryId = $this->addSimpleHidden('category_id');
        $valueId = $this->addSimpleHidden('value_id');

        $this->addSubmit(__('Save'))
            ->addClass('default_button')
            ->addClass('pull-right')
            ->addClass('submit_button');
    }
    public function populate($values)
	{
		//Default settings
		if (!isset($values["cardcustomcolor"]))  $values["cardcustomcolor"]="rgba(0,204,255,0.535)";
		parent::populate($values);
		
	    $this->addSimpleHtml("styles",'
            <style>
            #basiccardcustomcolor {
                background-color: '.$values["cardcustomcolor"].';
                position: absolute !important;
                border: 1px solid #00000040;
                width: 20px;
                min-width: 20px;
                max-width: 20px;
                height: 20px;
                min-height: 20px;
                max-height: 20px;
                border-radius: 4px;
            }
            </style>
        ');
	}
}
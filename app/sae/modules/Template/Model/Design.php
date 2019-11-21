<?php
use Nwicode\Request;
/**
 * Class Template_Model_Design
 */
class Template_Model_Design extends Core_Model_Default
{

    /**
     *
     */
    const PATH_IMAGE = '/images/templates';

    /**
     * @var array
     */
    public static $variables = [];
    public static $lastException = null;

    /**
     * @var
     */
    protected $_blocks;

    /**
     * Template_Model_Design constructor.
     * @param array $params
     * @throws Zend_Exception
     */
    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->_db_table = 'Template_Model_Db_Table_Design';
        return $this;
    }

    /**
     * @param $variables
     */
    public static function registerVariables($variables)
    {
        if (!is_array($variables)) {
            $variables = [$variables];
        }
        foreach ($variables as $variable) {
            self::$variables[] = $variable;
        }
    }

    /**
     * @param $application
     * @return string
     */
    public static function getCssPath($application)
    {
        /** Determines if the App has been updated or not. */
		$block_app = new Template_Model_Block_App();											

        $path = rpath("var/cache/css");
        $basePath = path("var/cache/css");
        $file = $application->getId() . ".css";

        $rebuild = filter_var($application->getGenerateScss(), FILTER_VALIDATE_BOOLEAN);

        // If we should regen the SCSS!
        if (!is_file("{$basePath}/{$file}") || $rebuild) {
            $application
                ->setGenerateScss(0)
                ->save();
			$new_scss = $block_app->isNewScss($application->getId());
            self::generateCss($application, false, false, $new_scss);
        }

        return "{$path}/{$file}";

    }

    /**
     * @param $application
     * @return array
     */
    public static function getVariables($application)
    {
        return self::generateCss($application, false, true, true);
    }


    /**
     * Return generated scss with custom blocks
     * needed for color design mode
     */
    public static function generateCustomCssForPreview($application) {
        $blocks = $application->getBlocks();
        $variables = [];

        foreach ($blocks as $block) {

            $block_id = (strlen(dechex($block->getId())) == 2) ? dechex($block->getId()) : "0" . dechex($block->getId());


            if ($block->getColorVariableName() && $block->getColor()) {
                $block_pos = "01";
                $hex = "#" . $block_id . "00" . $block_pos;

                $variables[$block->getColorVariableName()] = $hex;
            }
            if ($block->getBackgroundColorVariableName() && $block->getBackgroundColor()) {
                $block_pos = "02";
                $hex = "#" . $block_id . "00" . $block_pos;

                $variables[$block->getBackgroundColorVariableName()] = $hex;
            }
            if ($block->getBorderColorVariableName() && $block->getBorderColor()) {
                $block_pos = "03";
                $hex = "#" . $block_id . "00" . $block_pos;

                $variables[$block->getBorderColorVariableName()] = $hex;
            }

            if ($block->getImageColorVariableName() && $block->getImageColor()) {
                $block_pos = "04";
                $hex = "#" . $block_id . "00" . $block_pos;

                $variables[$block->getImageColorVariableName()] = $hex;
            }

            foreach ($block->getChildren() as $child) {
                $child_id = (strlen(dechex($child->getId())) == 2) ? dechex($child->getId()) : "0" . dechex($child->getId());

                if ($child->getColorVariableName() && $child->getColor()) {
                    $child_pos = "01";
                    $hex = "#" . $block_id . $child_id . $child_pos;

                    $variables[$child->getColorVariableName()] = $hex;
                }
                if ($child->getBackgroundColorVariableName() && $child->getBackgroundColor()) {
                    $child_pos = "02";
                    $hex = "#" . $block_id . $child_id . $child_pos;

                    $variables[$child->getBackgroundColorVariableName()] = $hex;
                }
                if ($child->getBorderColorVariableName() && $child->getBorderColor()) {
                    $child_pos = "03";
                    $hex = "#" . $block_id . $child_id . $child_pos;

                    $variables[$child->getBorderColorVariableName()] = $hex;
                }
            }

        }

        $content = [];

        $scss_files = [
            "ionic.nwicode.variables-opacity.scss",
            "ionic.nwicode.style.scss"
        ];

        foreach ($scss_files as $file) {
            $f = fopen(Core_Model_Directory::getBasePathTo("var/apps/browser/scss/{$file}"), "r");
            if ($f) {
                while (($line = fgets($f)) !== false) {
                    preg_match("/([\$a-zA-Z0-9_-]*)/", $line, $matches);
                    if (!empty($matches[0]) && !empty($variables[$matches[0]])) {
                        $line = "{$matches[0]}: {$variables[$matches[0]]} !default;";
                    }
                    $content[] = $line;
                }
            }
        }

        $scss = implode("\n", $content);
        $custom_app = $scss . "\n" . $ionic_css ."\n" . $application->getCustomScss() ;
        return $custom_app;
    }


    /**
     * Возвращает массив стилей ионика
     */
    public static function getIonicColors($application){
        $blocks = $application->getBlocks();
		/*create ionic4 colors*/
		$ionic_css = array();
		foreach ($blocks as $block) {
			if ($block->getData('code')!="ionic_4") continue;
			$children = $block->getChildren() ? $block->getChildren() : [$block];
			
			foreach ($children as $child) {
				//Не знаю почему не работает, поэтому возьмем прям из базы и замени стандартные
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
					->from(['tba'=>'template_block_app'])
					->where('tba.app_id = ?', $application->getId())
					->where('tba.block_id = ?', $child->getId());
				$ionic_colors = $db->fetchRow($select);
				
				
				//для ресета сделаем
				$ionic_colors_backup['ion_color'] = $child->getData('ion_color');
				$ionic_colors_backup['ion_color_contrast'] = $child->getData('ion_color_contrast');
				$ionic_colors_backup['ion_color_shade'] = $child->getData('ion_color_shade');
				$ionic_colors_backup['ion_color_tint'] = $child->getData('ion_color_tint');
				//Заменим основной блок
				if ($ionic_colors['ion_color']) $child->setIonColor($ionic_colors['ion_color']);
				if ($ionic_colors['ion_color_contrast']) $child->setIonColorContrast($ionic_colors['ion_color_contrast']);
				if ($ionic_colors['ion_color_shade']) $child->setIonColorShade($ionic_colors['ion_color_shade']);
				if ($ionic_colors['ion_color_tint']) $child->setIonColorTint($ionic_colors['ion_color_tint']);			
				
				/*main colors*/
				if ($child->getCode()=="ionic_primary_color") { 
                    $ionic_css["--ion-color-primary"] = $child->getData('ion_color'); 
                    $ionic_css["--ion-color-primary-rgb"] = $child->toRgb($child->getData('ion_color'),true,","); 
                    $ionic_css["--ion-color-primary-contrast"] = $child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-primary-contrast-rgb"] = $child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-primary-shade"] = $child->getData('ion_color_shade');
                    $ionic_css["--ion-color-primary-tint"] = $child->getData('ion_color_tint');
                }

				if ($child->getCode()=="ionic_secondary_color") {
                    $ionic_css["--ion-color-secondary"] = $child->getData('ion_color');
                    $ionic_css["--ion-color-secondary-rgb"] = $child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-secondary-contrast"] = $child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-secondary-contrast-rgb"] = $child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-secondary-shade"] = $child->getData('ion_color_shade');
                    $ionic_css["--ion-color-secondary-tint"] = $child->getData('ion_color_tint');
                }

				if ($child->getCode()=="ionic_tertiary_color") {
                    $ionic_css["--ion-color-tertiary"] = $child->getData('ion_color');
                    $ionic_css["--ion-color-tertiary-rgb"] = $child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-tertiary-contrast"] = $child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-tertiary-contrast-rgb"] = $child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-tertiary-shade"] = $child->getData('ion_color_shade');
                    $ionic_css["--ion-color-tertiary-tint"] = $child->getData('ion_color_tint');
                }

				if ($child->getCode()=="ionic_success_color") {
                    $ionic_css["--ion-color-success"]=$child->getData('ion_color');
                    $ionic_css["--ion-color-success-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-success-contrast"]=$child->getData('ion_color_contrast'); 
                    $ionic_css["--ion-color-success-contrast-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,","); 
                    $ionic_css["--ion-color-success-shade"]=$child->getData('ion_color_shade'); 
                    $ionic_css["--ion-color-success-tint"]=$child->getData('ion_color_tint');
                }
                if ($child->getCode()=="ionic_warning_color") {
                    $ionic_css["--ion-color-warning"]=$child->getData('ion_color');
                    $ionic_css["--ion-color-warning-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-warning-contrast"]=$child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-warning-contrast-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-warning-shade"]=$child->getData('ion_color_shade');
                    $ionic_css["--ion-color-warning-tint"]=$child->getData('ion_color_tint');
                }
				if ($child->getCode()=="ionic_danger_color") {
                    $ionic_css["--ion-color-danger"]=$child->getData('ion_color');
                    $ionic_css["--ion-color-danger-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-danger-contrast"]=$child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-danger-contrast-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-danger-shade"]=$child->getData('ion_color_shade');
                    $ionic_css["--ion-color-danger-tint"]=$child->getData('ion_color_tint');
                }

				if ($child->getCode()=="ionic_dark_color") {
                    $ionic_css["--ion-color-dark"]=$child->getData('ion_color');
                    $ionic_css["--ion-color-dark-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-dark-contrast"]=$child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-dark-contrast-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-dark-shade"]=$child->getData('ion_color_shade');
                    $ionic_css["--ion-color-dark-tint"]=$child->getData('ion_color_tint');
                }

				if ($child->getCode()=="ionic_medium_color") {
                    $ionic_css["--ion-color-medium"]=$child->getData('ion_color');
                    $ionic_css["--ion-color-medium-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-medium-contrast"]=$child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-medium-contrast-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-medium-shade"]=$child->getData('ion_color_shade');
                    $ionic_css["--ion-color-medium-tint"]=$child->getData('ion_color_tint');
                }

				if ($child->getCode()=="ionic_light_color") {
                    $ionic_css["--ion-color-light"]=$child->getData('ion_color');
                    $ionic_css["--ion-color-light-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-color-light-contrast"]=$child->getData('ion_color_contrast');
                    $ionic_css["--ion-color-light-contrast-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-color-light-shade"]=$child->getData('ion_color_shade');
                    $ionic_css["--ion-color-light-tint"]=$child->getData('ion_color_tint');
                }
				
				//text color and background color
				if ($child->getCode()=="ionic_text_color") {
                    $ionic_css["--ion-text-color"]=$child->getData('ion_color');
                    $ionic_css["--ion-text-color-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-background-color"]=$child->getData('ion_color_tint');
                    $ionic_css["--ion-background-color-rgb"]=$child->toRgb($child->getData('ion_color_tint'),true,",");
                    $ionic_css["--ion-placeholder-color"]=$child->getData('ion_color_contrast');
                }

			
				//calculate stepped color
				if ($child->getCode()=="ionic_text_color") {
					$endcolor = $child->getData('ion_color');	//end - text color
					$startcolor = $child->getData('ion_color_tint');	//start - background color
					//Convert ot 6-digits
					$endcolor = str_replace("#","",$endcolor);
					if (strlen($endcolor)==3) $endcolor = $endcolor[0] . $endcolor[0] . $endcolor[1] . $endcolor[1] . $endcolor[2] . $endcolor[2];
					$startcolor = str_replace("#","",$startcolor);
					if (strlen($startcolor)==3) $startcolor = $startcolor[0] . $startcolor[0] . $startcolor[1] . $startcolor[1] . $startcolor[2] . $startcolor[2];
					
					$startcolor = hexdec($startcolor);
					$endcolor = hexdec($endcolor);
					
					$theR0 = ($startcolor & 0xff0000) >> 16;
					$theG0 = ($startcolor & 0x00ff00) >> 8;
					$theB0 = ($startcolor & 0x0000ff) >> 0;
					$theR1 = ($endcolor & 0xff0000) >> 16;
					$theG1 = ($endcolor & 0x00ff00) >> 8;
					$theB1 = ($endcolor & 0x0000ff) >> 0;					
					$stepped_color = array();
					$shift = 50;
					for ($i = 1; $i <= 20; $i++) {
						$theR = self::interpolate($theR0, $theR1, $i, 20);
						$theG = self::interpolate($theG0, $theG1, $i, 20);
						$theB = self::interpolate($theB0, $theB1, $i, 20);
						$theVal = ((($theR << 8) | $theG) << 8) | $theB;			

						$stepped_color['--ion-color-step-'.$shift]='#'.sprintf("%06X", $theVal);
						$shift = $shift + 50;
					}
					
					//store to scss
					foreach($stepped_color as $color_name=>$color_value) {
                        $ionic_css[$color_name]=$color_value;

					}
					
				}
				
				//item color
				if ($child->getCode()=="ionic_item_color") {
                    $ionic_css["--ion-item-color"]=$child->getData('ion_color'); 
                    $ionic_css["--ion-item-color-rgb"]=$child->toRgb($child->getData('ion_color'),true,",");
                    $ionic_css["--ion-item-background"]=$child->getData('ion_color_contrast'); 
                    $ionic_css["--ion-item-background-rgb"]=$child->toRgb($child->getData('ion_color_contrast'),true,",");
                    $ionic_css["--ion-item-border-color"]=$child->getData('ion_color_tint'); 
                    $ionic_css["--ion-item-border-color-rgb"]=$child->toRgb($child->getData('ion_color_tint'),true,",");
                    $ionic_css["--ion-item-background-activated"]=$child->getData('ion_color_shade'); 
                    $ionic_css["--ion-item-background-activated-rgb"]=$child->toRgb($child->getData('ion_color_shade'),true,",");
				}
			}
        }
        
        return $ionic_css;
    }

    /**
     * @param Application_Model_Application $application
     * @param bool $javascript
     * @param bool $return_variables
     * @param bool $new_scss
     * @return bool|string|array
     */
    public static function generateCss($application, $javascript = false, $return_variables = false, $new_scss = true)
    {

        $variables = [];
        $blocks = $application->getBlocks();

        if (!$javascript) {
            foreach ($blocks as $block) {

                if ($block->getColorVariableName() && $block->getColorRGBA()) {
                    $variables[$block->getColorVariableName()] = $block->getColorRGBA();
                }
                if ($block->getBackgroundColorVariableName() && $block->getBackgroundColorRGBA()) {
                    $variables[$block->getBackgroundColorVariableName()] = $block->getBackgroundColorRGBA();
                }
                if ($block->getBorderColorVariableName() && $block->getBorderColorRGBA()) {
                    $variables[$block->getBorderColorVariableName()] = $block->getBorderColorRGBA();
                }
                if ($block->getImageColorVariableName() && $block->getImageColorRGBA()) {
                    $variables[$block->getImageColorVariableName()] = $block->getImageColorRGBA();
                }

                foreach ($block->getChildren() as $child) {
                    if ($child->getColorVariableName() && $child->getColorRGBA()) {
                        $variables[$child->getColorVariableName()] = $child->getColorRGBA();
                    }
                    if ($child->getBackgroundColorVariableName() && $child->getBackgroundColorRGBA()) {
                        $variables[$child->getBackgroundColorVariableName()] = $child->getBackgroundColorRGBA();
                    }
                    if ($child->getBorderColorVariableName() && $child->getBorderColorRGBA()) {
                        $variables[$child->getBorderColorVariableName()] = $child->getBorderColorRGBA();
                    }
                    if ($child->getImageColorVariableName() && $child->getImageColorRGBA()) {
                        $variables[$child->getImageColorVariableName()] = $child->getImageColorRGBA();
                    }
                }

            }
        } else {
            foreach ($blocks as $block) {

                $block_id = (strlen(dechex($block->getId())) == 2) ? dechex($block->getId()) : "0" . dechex($block->getId());


                if ($block->getColorVariableName() && $block->getColor()) {
                    $block_pos = "01";
                    $hex = "#" . $block_id . "00" . $block_pos;

                    $variables[$block->getColorVariableName()] = $hex;
                }
                if ($block->getBackgroundColorVariableName() && $block->getBackgroundColor()) {
                    $block_pos = "02";
                    $hex = "#" . $block_id . "00" . $block_pos;

                    $variables[$block->getBackgroundColorVariableName()] = $hex;
                }
                if ($block->getBorderColorVariableName() && $block->getBorderColor()) {
                    $block_pos = "03";
                    $hex = "#" . $block_id . "00" . $block_pos;

                    $variables[$block->getBorderColorVariableName()] = $hex;
                }

                if ($block->getImageColorVariableName() && $block->getImageColor()) {
                    $block_pos = "04";
                    $hex = "#" . $block_id . "00" . $block_pos;

                    $variables[$block->getImageColorVariableName()] = $hex;
                }

                foreach ($block->getChildren() as $child) {
                    $child_id = (strlen(dechex($child->getId())) == 2) ? dechex($child->getId()) : "0" . dechex($child->getId());

                    if ($child->getColorVariableName() && $child->getColor()) {
                        $child_pos = "01";
                        $hex = "#" . $block_id . $child_id . $child_pos;

                        $variables[$child->getColorVariableName()] = $hex;
                    }
                    if ($child->getBackgroundColorVariableName() && $child->getBackgroundColor()) {
                        $child_pos = "02";
                        $hex = "#" . $block_id . $child_id . $child_pos;

                        $variables[$child->getBackgroundColorVariableName()] = $hex;
                    }
                    if ($child->getBorderColorVariableName() && $child->getBorderColor()) {
                        $child_pos = "03";
                        $hex = "#" . $block_id . $child_id . $child_pos;

                        $variables[$child->getBorderColorVariableName()] = $hex;
                    }
                }

            }

        }

        // Prepend google font
        $fontFamily = $application->getFontFamily();
        $fontImport = "";
        if (!empty($fontFamily)) {
            $replace = str_replace("+", " ", $fontFamily);

            $fontImport = Request::get("https://fonts.googleapis.com/css?family={$fontFamily}", [
										
                "subset" => "latin,greek,cyrillic",
            ], null, null, [
                "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.80 Safari/537.36"
            ]);

            if (Request::$statusCode == 200) {
                $variables['$font-family'] = "'$replace', sans-serif";
            } else {
                $fontImport = "/** Unable to fetch Google Font {$fontFamily} */";
            }
        }

        $content = [];

        $scss_files = [
            "ionic.nwicode.variables-opacity.scss",
            "ionic.nwicode.style.scss"
        ];

        foreach ($scss_files as $file) {
            $f = fopen(Core_Model_Directory::getBasePathTo("var/apps/browser/scss/{$file}"), "r");
            if ($f) {
                while (($line = fgets($f)) !== false) {
                    preg_match("/([\$a-zA-Z0-9_-]*)/", $line, $matches);
                    if (!empty($matches[0]) && !empty($variables[$matches[0]])) {
                        $line = "{$matches[0]}: {$variables[$matches[0]]} !default;";
                    }
                    $content[] = $line;
                }
            }
        }

        /** Return only vars */
        if ($return_variables) {
            return $variables;
        }

        $scss = implode("\n", $content);

		/*create ionic4 colors*/
		$ionic_css = "";
		foreach ($blocks as $block) {
			if ($block->getData('code')!="ionic_4") continue;
			$children = $block->getChildren() ? $block->getChildren() : [$block];
			
			foreach ($children as $child) {
				//Не знаю почему не работает, поэтому возьмем прям из базы и замени стандартные
				$db = Zend_Db_Table::getDefaultAdapter();
				$select = $db->select()
					->from(['tba'=>'template_block_app'])
					->where('tba.app_id = ?', $application->getId())
					->where('tba.block_id = ?', $child->getId());
				$ionic_colors = $db->fetchRow($select);
				
				
				//для ресета сделаем
				$ionic_colors_backup['ion_color'] = $child->getData('ion_color');
				$ionic_colors_backup['ion_color_contrast'] = $child->getData('ion_color_contrast');
				$ionic_colors_backup['ion_color_shade'] = $child->getData('ion_color_shade');
				$ionic_colors_backup['ion_color_tint'] = $child->getData('ion_color_tint');
				//Заменим основной блок
				if ($ionic_colors['ion_color']) $child->setIonColor($ionic_colors['ion_color']);
				if ($ionic_colors['ion_color_contrast']) $child->setIonColorContrast($ionic_colors['ion_color_contrast']);
				if ($ionic_colors['ion_color_shade']) $child->setIonColorShade($ionic_colors['ion_color_shade']);
				if ($ionic_colors['ion_color_tint']) $child->setIonColorTint($ionic_colors['ion_color_tint']);			
				
				/*main colors*/
				if ($child->getCode()=="ionic_primary_color") $ionic_css .= " --ion-color-primary: ".$child->getData('ion_color')."; --ion-color-primary-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-primary-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-primary-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-primary-shade: ".$child->getData('ion_color_shade')."; --ion-color-primary-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_secondary_color") $ionic_css .= " --ion-color-secondary: ".$child->getData('ion_color')."; --ion-color-secondary-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-secondary-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-secondary-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-secondary-shade: ".$child->getData('ion_color_shade')."; --ion-color-secondary-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_tertiary_color") $ionic_css .= " --ion-color-tertiary: ".$child->getData('ion_color')."; --ion-color-tertiary-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-tertiary-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-tertiary-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-tertiary-shade: ".$child->getData('ion_color_shade')."; --ion-color-tertiary-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_success_color") $ionic_css .= " --ion-color-success: ".$child->getData('ion_color')."; --ion-color-success-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-success-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-success-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-success-shade: ".$child->getData('ion_color_shade')."; --ion-color-success-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_warning_color") $ionic_css .= " --ion-color-warning: ".$child->getData('ion_color')."; --ion-color-warning-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-warning-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-warning-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-warning-shade: ".$child->getData('ion_color_shade')."; --ion-color-warning-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_danger_color") $ionic_css .= " --ion-color-danger: ".$child->getData('ion_color')."; --ion-color-danger-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-danger-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-danger-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-danger-shade: ".$child->getData('ion_color_shade')."; --ion-color-danger-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_dark_color") $ionic_css .= " --ion-color-dark: ".$child->getData('ion_color')."; --ion-color-dark-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-dark-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-dark-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-dark-shade: ".$child->getData('ion_color_shade')."; --ion-color-dark-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_medium_color") $ionic_css .= " --ion-color-medium: ".$child->getData('ion_color')."; --ion-color-medium-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-medium-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-medium-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-medium-shade: ".$child->getData('ion_color_shade')."; --ion-color-medium-tint: ".$child->getData('ion_color_tint').";\n";
				if ($child->getCode()=="ionic_light_color") $ionic_css .= " --ion-color-light: ".$child->getData('ion_color')."; --ion-color-light-rgb: ".$child->toRgb($child->getData('ion_color'),true,",")."; --ion-color-light-contrast: ".$child->getData('ion_color_contrast')."; --ion-color-light-contrast-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",")."; --ion-color-light-shade: ".$child->getData('ion_color_shade')."; --ion-color-light-tint: ".$child->getData('ion_color_tint').";\n";
				
				//text color and background color
				if ($child->getCode()=="ionic_text_color") $ionic_css .= " --ion-text-color: ".$child->getData('ion_color')."; --ion-text-color-rgb: ".$child->toRgb($child->getData('ion_color'),true,",").";\n";
				if ($child->getCode()=="ionic_text_color") $ionic_css .= " --ion-background-color: ".$child->getData('ion_color_tint')."; --ion-background-color-rgb: ".$child->toRgb($child->getData('ion_color_tint'),true,",").";\n";
				if ($child->getCode()=="ionic_text_color") $ionic_css .= " --ion-placeholder-color: ".$child->getData('ion_color_contrast').";\n";
			
				//calculate stepped color
				if ($child->getCode()=="ionic_text_color") {
					$endcolor = $child->getData('ion_color');	//end - text color
					$startcolor = $child->getData('ion_color_tint');	//start - background color
					//Convert ot 6-digits
					$endcolor = str_replace("#","",$endcolor);
					if (strlen($endcolor)==3) $endcolor = $endcolor[0] . $endcolor[0] . $endcolor[1] . $endcolor[1] . $endcolor[2] . $endcolor[2];
					$startcolor = str_replace("#","",$startcolor);
					if (strlen($startcolor)==3) $startcolor = $startcolor[0] . $startcolor[0] . $startcolor[1] . $startcolor[1] . $startcolor[2] . $startcolor[2];
					
					$startcolor = hexdec($startcolor);
					$endcolor = hexdec($endcolor);
					
					$theR0 = ($startcolor & 0xff0000) >> 16;
					$theG0 = ($startcolor & 0x00ff00) >> 8;
					$theB0 = ($startcolor & 0x0000ff) >> 0;
					$theR1 = ($endcolor & 0xff0000) >> 16;
					$theG1 = ($endcolor & 0x00ff00) >> 8;
					$theB1 = ($endcolor & 0x0000ff) >> 0;					
					$stepped_color = array();
					$shift = 50;
					for ($i = 1; $i <= 20; $i++) {
						$theR = self::interpolate($theR0, $theR1, $i, 20);
						$theG = self::interpolate($theG0, $theG1, $i, 20);
						$theB = self::interpolate($theB0, $theB1, $i, 20);
						$theVal = ((($theR << 8) | $theG) << 8) | $theB;			

						$stepped_color['--ion-color-step-'.$shift]='#'.sprintf("%06X", $theVal);
						$shift = $shift + 50;
					}
					
					//store to scss
					foreach($stepped_color as $color_name=>$color_value) {
						$ionic_css .= $color_name.": ".$color_value.";\n";
					}
					
				}
				
				//item color
				if ($child->getCode()=="ionic_item_color") {
					$ionic_css .= " --ion-item-color: ".$child->getData('ion_color')."; --ion-item-color-rgb: ".$child->toRgb($child->getData('ion_color'),true,",").";\n";
					$ionic_css .= " --ion-item-background: ".$child->getData('ion_color_contrast')."; --ion-item-background-rgb: ".$child->toRgb($child->getData('ion_color_contrast'),true,",").";\n";
					$ionic_css .= " --ion-item-border-color: ".$child->getData('ion_color_tint')."; --ion-item-border-color-rgb: ".$child->toRgb($child->getData('ion_color_tint'),true,",").";\n";
					$ionic_css .= " --ion-item-background-activated: ".$child->getData('ion_color_shade')."; --ion-item-background-activated-rgb: ".$child->toRgb($child->getData('ion_color_shade'),true,",").";\n";
				}
			}
		}
		
		if ($ionic_css!="") $ionic_css = "\n:root {".$ionic_css."}\n";
		
		//placeholder color
		$ionic_css .="input::placeholder, textarea::placeholder {color:var(--ion-placeholder-color) !important;}\n";
		
		//background color
		//$ionic_css .=".inner-scroll {background: var(--ion-background-color) !important;}\n";  
		
		//overwrite old item color
		$ionic_css .="ion-card .item {border-color: var(--ion-item-border-color) !important;}\n";  		
		$ionic_css .="ion-card .item {background-color: var(--ion-item-background) !important;}\n";  		
		$ionic_css .="ion-card .item {color: var(--ion-item-color) !important;}\n";  		
		$ionic_css .="ion-list .item {border-color: var(--ion-item-border-color) !important;}\n";  		
		$ionic_css .="ion-list .item {background-color: var(--ion-item-background) !important;}\n";  		
		$ionic_css .="ion-list .item {color: var(--ion-item-color) !important;}\n";  	
		$ionic_css .="ion-list .list {background-color: var(--ion-item-background) !important;}\n";  
		$ionic_css .="ion-card .card {background-color: var(--ion-item-background) !important;}\n"; 
		
        /** With custom from app */
        $custom_app = $scss. "\n" . $ionic_css;
        
		
		
		if (!$javascript) {
            $custom_app = $scss . "\n" . $ionic_css ."\n" . $application->getCustomScss() ;
        }

        $compiler = Nwicode_Scss::getCompiler();
        $compiler->addImportPath(Core_Model_Directory::getBasePathTo("var/apps/browser/lib/ionic/scss"));
        $compiler->addImportPath(Core_Model_Directory::getBasePathTo("var/apps/browser/scss"));

        // Import custom modules SCSS files!
        foreach (Nwicode_Assets::$assets_scss as $scssFile) {
            $path = Core_Model_Directory::getBasePathTo($scssFile);
            $custom_app .= file_get_contents($path);
        }

        $result = true;
        try {
            $css = $compiler->compile('
                @import "_variables.scss";
                @import "_mixins.scss";
                ' . $custom_app
            );
        } catch (\Exception $e) {
            /** Meanwhile, fallback without custom scss */
            $css = $compiler->compile('
                @import "_variables.scss";
                @import "_mixins.scss";
                ' . $scss
            );
            $result = false;
            self::$lastException = $e->getMessage();
        }

        $css = $fontImport . "\n" . $css;

        if ($javascript) {
            return $css;
        } else {
            $folder = Core_Model_Directory::getBasePathTo("var/cache/css");
            $file = $application->getId() . ".css";
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            file_put_contents("{$folder}/{$file}", $css);
        }

        return $result;
    }

	
	// return the interpolated value between pBegin and pEnd
	public static function interpolate($pBegin, $pEnd, $pStep, $pMax) {
		if ($pBegin < $pEnd) {
			return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
		} else {
			return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
		}
	}
  	
	
    /**
     * @param null $where
     * @return array
     */
    public function findAllWithCategory($where = null)
    {
        $all_templates = $this->findAll($where, ['position ASC', 'name ASC']);
        $template_a_category = $this->getTable()->findAllWithCategory();
        $final_templates = [];

        foreach ($all_templates as $template) {

            $tmp_category_ids = [];
            foreach ($template_a_category as $template_category) {
                if ($template->getDesignId() == $template_category["design_id"])
                    $tmp_category_ids[] = $template_category["category_id"];
            }
            $template->setCategoryIds($tmp_category_ids);

            $final_templates[] = $template;
        }

        return $final_templates;
    }

    /**
     * @return mixed
     */
    public function getBlocks()
    {

        if (!$this->_blocks) {
            $block = new Template_Model_Block();
            $this->_blocks = $block->findByDesign($this->getId());
        }

        return $this->_blocks;

    }

    /**
     * @param $name
     * @return Template_Model_Block
     */
    public function getBlock($name)
    {

        foreach ($this->getBlocks() as $block) {
            if ($block->getCode() == $name) return $block;
        }
        return new Template_Model_Block();

    }

    /**
     * @param null $data_key
     * @return string
     */
    public function getOverview($data_key = null)
    {
        $data = (empty($data_key)) ?
            $this->getData('overview') : $this->getData($data_key);

        if ($this->getVersion() == 2) {
            return Core_Model_Directory::getPathTo($data);
        }
        return Core_Model_Directory::getPathTo(self::PATH_IMAGE . $data);
    }

    /**
     * @param bool $base
     * @return string
     */
    public function getBackgroundImage($base = false)
    {
        return $base ? Core_Model_Directory::getBasePathTo(self::PATH_IMAGE . $this->getData('background_image')) : Core_Model_Directory::getPathTo($this->getData('background_image'));
    }

    /**
     * @param bool $base
     * @return string
     */
    public function getBackgroundImageHd($base = false)
    {
        return $base ? Core_Model_Directory::getBasePathTo(self::PATH_IMAGE . $this->getData('background_image_hd')) : Core_Model_Directory::getPathTo($this->getData('background_image_hd'));
    }

    /**
     * @param bool $base
     * @return string
     */
    public function getBackgroundImageTablet($base = false)
    {
        return $base ? Core_Model_Directory::getBasePathTo(self::PATH_IMAGE . $this->getData('background_image_tablet')) : Core_Model_Directory::getPathTo($this->getData('background_image_tablet'));
    }

}

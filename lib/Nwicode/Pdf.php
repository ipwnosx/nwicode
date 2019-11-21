<?php

/**
 * Class Nwicode_Pdf
 */
class Nwicode_Pdf extends Zend_Pdf
{
    /**
     * @param mixed $param1
     * @param mixed $param2
     * @return Nwicode_Pdf_Page
     */
    public function newPage($param1, $param2 = null)
    {
        require_once 'Nwicode/Pdf/Page.php';

        if ($param2 === null) {
            return new Nwicode_Pdf_Page($param1, $this->_objFactory);
        } else {
            return new Nwicode_Pdf_Page($param1, $param2, $this->_objFactory);
        }
    }

    /**
     * @param $text
     * @param $font
     * @param $font_size
     * @return float|int
     */
    public function getTextWidth($text, $font, $font_size)
    {
        $drawing_text = iconv('', 'UTF-8', $text);
        $characters = [];
        for ($i = 0; $i < strlen($drawing_text); $i++) {
            $characters[] = (ord($drawing_text[$i++]) << 8) | ord($drawing_text[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $text_width = (array_sum($widths) / $font->getUnitsPerEm()) * $font_size;

        return $text_width;
    }
}

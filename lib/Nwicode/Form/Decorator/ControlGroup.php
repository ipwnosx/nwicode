<?php
/**
 * Class Nwicode_Form_Decorator_ControlGroup
 */
class Nwicode_Form_Decorator_ControlGroup extends Zend_Form_Decorator_Abstract {

    /**
     * @param string $content
     * @return string
     */
    public function render($content){
        $class = $this->getOption('class') ? $this->getOption('class'): "control-group";
        $title = $this->getOption('title') ? $this->getOption('title'): null;

        $element = $this->getElement();
        $errors = $element->getMessages();
        if(!empty($errors)) {
            $class .= " error";
        }

        $output = '<div class="' . $class . '" ';
        if($this->getOption('id')) {
            $output .= ' id="'.$this->getOption('id').'" ';
        }
        if($title != null) {
            $output .= 'title="'.$title.'" ';
        }
        $output .= '>' . $content . '</div>';

        return $output;
    }

}
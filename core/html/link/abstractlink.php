<?php

namespace Zippy\Html\Link;

use Zippy\Html\HtmlComponent;
use Zippy\Interfaces\Binding;

/**
 * Базовый   класс для  компонентов HTML ссылок
 *
 */
abstract class AbstractLink extends \Zippy\Html\HtmlContainer
{
    protected $value = null;
    protected $disabled = false;
    protected $htmlvalue = false;

    public function __construct($id) {
        parent::__construct($id);
        $this->setAttribute("href", "javascript:void(0);");
    }

    /**
     * Устанавливает  текст  ссылки. В случае  тэга  <IMG> вместо  текста  задает
     * путь к  изображению (атрибут src)
     *
     * @param string   $text
     */
    public function setValue($text,$htmlvalue=false) {
        $this->value = $text;
        $this->htmlvalue = $htmlvalue;
        return $this;
    }
    public function setText($text,$htmlvalue=false) {
        $this->setValue($text,$htmlvalue) ;
    }
    
    public function getValue() {
        if ($this->value instanceof Binding) {
            return $this->value->getValue();
        } else {
            return $this->value;
        }
    }

    /**
     * @see HtmlComponent
     */
    public function beforeRender() {
        $HtmlTag = $this->getTag();
        $children = $HtmlTag->children();

        if ($this->value != null && $children->count() == 0) {
            if($this->htmlvalue) {
              $HtmlTag->html($this->getValue());              
            }else {
              $HtmlTag->text($this->getValue());    
            }
            
        }
        if ($children->count() == 1 && $this->value != null) {
            $children[0]->attr('src', $this->getValue());
        }
    }

    /**
     * Устанавливает состояние disabled при котором ссылка отображается как текст
     */
    public function setDisabled($disabled = true) {
        $this->disabled = $disabled;
    }

    public function RenderImpl() {
        parent::RenderImpl();
    }

}

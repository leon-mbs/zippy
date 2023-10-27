<?php

namespace Zippy\Html;

use Zippy\Binding\SimpleBinding;
use Zippy\Interfaces\Binding;

/**
 *  Компонент  для строчного  тэга типа  SPAN и  т.д.
 *
 */
class Label extends HtmlComponent
{
    private $value;

    private $html = false;

    /**
     * Конструктор
     * @param string ID елемента
     * @param string  Текстовое  содержание
     */
    public function __construct($id, $text = null, $html = false) {
        parent::__construct($id);

        $this->value = $text;
        $this->html = $html;
        // $this->setOption(OPTION_INSERT_HTML);
    }

    /**
     * @see HtmlComponent
     */
    public function RenderImpl() {

  
        if ($this->getText() === null) {
            return;
        }

        $HtmlTag = $this->getTag();
        if ($this->html) {
            $HtmlTag->html($this->getText());
        } else {
            $HtmlTag->text($this->getText());
        }
    }



    /**
     * Установить  текст
     */
    public function setText($text, $html = false) {
        $this->value = $text;
        $this->html = $html;

        if(\Zippy\WebApplication::$app->getRequest()->isAjaxRequest()) {
            $js= "$('#{$this->id}').text('{$text}')" ;
            if ($this->html) {
                $js= "$('#{$this->id}').html('{$text}')";
            }



            \Zippy\WebApplication::$app->getResponse()->addAjaxResponse($js) ;
        }



    }

    public function setHtml($text) {
        $this->setText($text,true) ;       
    }    
    
    /**
     * Прочитать  текст
     * @return  string
     */
    public function getText() {
        if ($this->value instanceof Binding) {
            return $this->value->getValue();
        } else {
            return $this->value;
        }
    }



}

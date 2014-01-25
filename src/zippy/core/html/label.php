<?php

namespace Zippy\Html;

use \Zippy\Binding\SimpleBinding;
use \Zippy\Interfaces\Binding;
use \Zippy\Interfaces\AjaxRender;

/**
 *  Компонент  для строчного  тэга типа  SPAN и  т.д.
 *  
 */
class Label extends HtmlComponent implements AjaxRender
{

        private $value;
        private $ajaxvalue;
        private $html = false;

        /**
         * Конструктор
         * @param  string ID елемента
         * @param  string  Текстовое  содержание
         */
        public function __construct($id, $text = null, $html = false)
        {
                parent::__construct($id);

                $this->value = $text;
                $this->html = $html;
                // $this->setOption(OPTION_INSERT_HTML);
        }

        /**
         * @see HtmlComponent
         */
        public function RenderImpl()
        {


                if (strlen($this->ajaxvalue) > 0) {
                        \Zippy\WebApplication::$app->getResponse()->addJavaScript($this->ajaxvalue, true);
                        return;
                }
                if ($this->getText() == null)
                        return;

                $HtmlTag = $this->getTag();
                if ($this->html) {
                        $HtmlTag->html($this->getText());
                } else {
                        $HtmlTag->text($this->getText());
                }
        }

        /**
         * Реализация интерфейса AjaxRender
         * @see AjaxRender
         */
        public function AjaxAnswer()
        {
                $text = $this->getText();
                return "$('#{$this->id}').text('{$text}')";
        }

        /**
         * Установить  текст
         */
        public function setText($text, $html = false)
        {
                $this->value = $text;
                $this->html = $html;
                $this->ajaxvalue = "";
        }

        /**
         * Прочитать  текст
         * @return  string
         */
        function getText()
        {
                if ($this->value instanceof Binding) {
                        return $this->value->getValue();
                } else {
                        return $this->value;
                }
        }

        /**
         * Добавления значения  которое  подгрузится с  помощю AJAX 
         * после загрузки страницы
         * 
         * @param mixed $url   Адрес  страницы   с  данными
         * @param mixed $html    если  true жданные  вставятся в  DOM  как  html
         */
        public function setAjaxText($url, $html = false)
        {

                $this->ajaxvalue = "$.get('{$url}', function(data) {  
                    $('#{$this->id}')." . ($html ? "html" : "text") . "(data) });";
        }

}


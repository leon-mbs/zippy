<?php

namespace Zippy\Html\Form;


use \Zippy\Interfaces\AjaxRender;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt;
 */
class TextInput extends HtmlFormDataElement implements AjaxRender
{


    /**
     * Конструктор
     * @param mixed  ID
     * @param Значение элемента  или  поле  привязанного объекта
     */
    public function __construct($id, $value = null) {
        parent::__construct($id);
        $this->setValue($value);
        $this->setAttribute("name", $this->id);
    }

    /**
     * Возвращает  текстовое  значение
     * @return  string
     */
    public function getText() {
        return $this->getValue();
    }

    /**
     * Устанавливает  текстовое  значение
     * @param string
     */
    public function setText($text) {
        $this->setValue($text);
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl() {
        // $this->checkInForm();

        $this->setResponseData();
    }

    protected function setResponseData() {
        $this->setAttribute("value", ($this->getValue()));
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData() {

        $this->setValue($_REQUEST[$this->id]);

    }


    /**
     * @see AjaxRender
     */
    public function AjaxAnswer() {

        $text = $this->getValue();
        return "$('#{$this->id}').val('{$text}')";
    }


    public function clean() {
        $this->setText('');
    }
}

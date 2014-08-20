<?php

namespace Zippy\Html\Form;

use \Zippy\Validator\Validator;
use \Zippy\Interfaces\Validated;
use \Zippy\Interfaces\AjaxRender;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt;
 */
class TextInput extends HtmlFormDataElement implements Validated, AjaxRender
{

    protected $validators = array();

    /**
     * Конструктор
     * @param mixed  ID
     * @param Значение елеента  или  поле  привязанного объекта
     */
    public function __construct($id, $value = null)
    {
        parent::__construct($id);
        $this->setValue($value);
        $this->setAttribute("name", $this->id);
    }

    /**
     * Возвращает  текстовое  значение
     * @return  string
     */
    public function getText()
    {
        return $this->getValue();
    }

    /**
     * Устанавливает  текстовое  значение
     * @param  string
     */
    public function setText($text)
    {
        $this->setValue($text);
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        // $this->checkInForm();

        $this->setResponseData();
    }

    protected function setResponseData()
    {
        $this->setAttribute("value", ($this->getValue()));
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData()
    {
        $this->setValue($_REQUEST[$this->id]);
        foreach ($this->validators as $validator) {
            $validator->validate($this, $this->getValue());
        }
    }

    /**
     * @see Validated
     */
    public function addValidator(Validator $validator)
    {
        $this->validator[] = $validator;
    }

    /**
     * @see AjaxRender
     */
    public function AjaxAnswer()
    {

        $text = $this->getValue();
        return "$('#{$this->id}').val('{$text}')";
    }

}

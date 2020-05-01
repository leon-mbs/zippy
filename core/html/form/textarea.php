<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;

/**
 * Компонент  тэга  &lt;textarea&gt;
 */
class TextArea extends TextInput
{

    /**
     * @see  HtmlFormSubmitElement
     */
    public function setResponseData() {
        $this->getTag()->text($this->getValue());
    }

    /**
     * @see AjaxRender
     */
    public function AjaxAnswer() {
        $text = $this->getValue();
        $responseJS = "$('#{$this->id}').text('{$text}')";

        WebApplication::$app->getRespose()->addAjaxResponse($responseJS);
    }

}

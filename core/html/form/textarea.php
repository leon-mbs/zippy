<?php

namespace Zippy\Html\Form;

use Zippy\WebApplication;

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


    public function setText($text='') {
        $this->setValue($text);


        if(\Zippy\WebApplication::$app->getRequest()->isAjaxRequest()) {
            $js= "$('#{$this->id}').text('{$text}')" ;


            \Zippy\WebApplication::$app->getResponse()->addAjaxResponse($js) ;
        }

    }
}

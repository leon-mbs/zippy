<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Interfaces\Binding;
use \Zippy\Interfaces\ChangeListener;
use \Zippy\Interfaces\AjaxChangeListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\AjaxRender;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Компонент  тэга  &lt;select&gt;
 */
class DropDownChoice extends HtmlFormDataElement implements ChangeListener, AjaxChangeListener, Requestable, AjaxRender
{

    private $optionlist;
    private $event;

    /**
     * Конструктор
     * @param  mixed  ID
     * @param  array  Массив  значений
     * @param  Текущее значение  елемента
     */
    public function __construct($id, $optionlist = array(), $value = -1)
    {
        parent::__construct($id);
        $this->setValue($value);

        $this->optionlist = $optionlist;
    }

    /**
     * 3
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        //    $this->checkInForm();


        $this->setAttribute("name", $this->id);
        $this->setAttribute("id", $this->id);

        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;

            if ($this->event->isajax == false) {

                $url = $this->owner->getURLNode() . '::' . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $this->setAttribute("onchange", "javascript:{ $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit();}");
            } else {
                $url = $this->owner->getURLNode() . "::" . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $this->setAttribute("onchange", " $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true');");
            }
        }


        $this->setResponseData();
    }

    private function setResponseData()
    {

        $list = $this->optionlist instanceOf Binding ? $this->optionlist->getValue() : $this->optionlist;
        $tag = $this->getTag();
        $options = "";
        foreach ($list as $key => $value) {
            // if($item instanceof SelectOption)
            $option = "<option value=\"{$key}\" ";

            if ($key == $this->getValue()) {
                $option .= " selected ";
            }

            $option .= ">{$value}</option>";
            $options .= $option;
            //$tag->appendChild($option) ;
        }
        $tag->append($options);
        //$html = $tag->html();

        if (count($list) == 0) {
              //WebApplication::$app->getResponse()->addJavaScript("$('#" . $this->id . " :nth-child(" . $this->getValue() . ")').attr('selected', 'selected') ;", true);
               $js = "$('#" . $this->id . " option').each(function() {    if($(this).val() == '" . $this->getValue() . "') {        $(this).prop(\"selected\", true);   }});";

                WebApplication::$app->getResponse()->addJavaScript($js, true);
               // WebApplication::$app->getResponse()->addJavaScript("$(\"#" . $this->id . "\" ).filter('[value=1]').prop('selected', true)", true);
              //  WebApplication::$app->getResponse()->addJavaScript("$(\"#" . $this->id . ">option[value='" . $this->getValue() . "']\").prop('selected','selected')", true);
              //  WebApplication::$app->getResponse()->addJavaScript("$(\"#" . $this->id . ">option[value='" . $this->getValue() . "']\").attr('selected','selected')", true);


        }
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData()
    {
        $this->setValue($_REQUEST[$this->id]);
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {
        $this->OnChange();
    }

    /**
     * @see AjaxRender
     */
    public function AjaxAnswer()
    {

        $list = $this->optionlist instanceOf Binding ? $this->optionlist->getValue() : $this->optionlist;

        $js = "$('#{$this->id}').empty();";
        foreach ($list as $key => $value) {
            $js .= "$('#{$this->id}').append('<option value=\"{$key}\">{$value}</option>');";
        }
        return $js;
    }

    /**
     * @see  ChangeListener
     */
    public function setChangeHandler(EventReceiver $receiver, $handler)
    {
        $this->event = new Event($receiver, $handler);
    }

    /**
     * @see  AjaxChangeListener
     */
    public function setAjaxChangeHandler(EventReceiver $receiver, $handler)
    {
        $this->setChangeHandler($receiver, $handler);
        $this->event->isajax = true;
    }

    /**
     * @see ChangeListener
     */
    public function OnChange()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

    protected function getTag($tagname = "")
    {
        return parent::getTag('select');
    }

    public function setOptionList($optionlist)
    {
        if(is_array($optionlist)){
            $this->optionlist = $optionlist;
            $this->setValue(-1);
        }
    }

    /**
     * Возвращает  массив  списка  комбобокса
     *
     */
    public function getOptionList()
    {
        return $list = $this->optionlist instanceOf Binding ? $this->optionlist->getValue() : $this->optionlist;
    }

    /**
     * Добавляет  пункт  в  список
     *
     * @param mixed $value
     * @param mixed $text
     */
    public function addOption($value, $text)
    {
        if ($this->optionlist instanceOf Binding)
            return;
        $this->optionlist[$value] = $text;
    }
    


}

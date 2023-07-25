<?php

namespace Zippy\Html\Form;

use Zippy\WebApplication;
use Zippy\Interfaces\Binding;
use Zippy\Interfaces\ChangeListener;
use Zippy\Interfaces\Requestable;
use Zippy\Interfaces\EventReceiver;
use Zippy\Event;

/**
 * Компонент  тэга  &lt;input type=&quot;checkbox&quot;&gt;
 */
class CheckBox extends HtmlFormDataElement implements ChangeListener, Requestable
{
    private $event;

    /**
     * Конструктор
     * @param mixed  ID
     * @param Значение елеента  или  поле  привязанного объекта
     */
    public function __construct($id, $value = false) {
        parent::__construct($id);
        $this->setValue($value);
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl() {

        if ($this->getValue() == true || $this->getValue() == 1) {
            $this->setAttribute("checked", "On");
        } else {
            $this->setAttribute("checked", null);
        }

        $this->setAttribute("name", $this->id);
        // if set event
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;
            //  $this->attributes["onclick"]="javascript:{ $('#".$formattr["id"]."_hf').val('submit1') ; $('#".$formattr["id"]."').submit();}";

            $url = $this->owner->getURLNode() . '::' . $this->id;
            $url = substr($url, 2 + strpos($url, 'q='));
            if ($this->event->isajax == false) {

                $this->setAttribute("onchange", "javascript:{ $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "_s').trigger('click');}");
            } else {
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $this->setAttribute("onchange", " $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true');");
            }
        }
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData() {
        $this->setValue(isset($_REQUEST[$this->id]));

    }

    /**
     * @see Requestable
     */
    public function RequestHandle() {
        $this->OnEvent();
    }

    /**
     * @see  ChangeListener
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = false) {

        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
    }

    /**
     * @see ChangeListener
     */
    public function OnEvent() {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

    /**
     * Устанавливает  checkbox
     * @param boolean
     */
    public function setChecked($checked) {
        $checked = $checked == 1 ? true : $checked;
        $checked = $checked === 'true' ? true : $checked;
        $this->setValue($checked);
    }

    /**
     * Установлен  ли checkbox
     */
    public function isChecked() {
        return $this->getValue() === true;
    }

    public function clean() {
        $this->setChecked(false);
    }
}

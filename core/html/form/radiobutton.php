<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Binding\PropertyBinding;
use \Zippy\Interfaces\Binding;
use \Zippy\Interfaces\ChangeListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Компонент  тэга  &lt;input type=&quot;radio&quot;&gt;
 */
class RadioButton extends HtmlFormDataElement implements ChangeListener, Requestable
{

    private $itemvalue, $event, $groupname;

    /**
     * Конструктор
     * @param  mixed  ID
     * @param  mixed  привязка выбранного  значения для  группы
     * @param  mixed  значение для  радиокнопки
     * @param  mixed  устанавливает имя  группы  для  радиокнопки. Если  не  задаоно имя  группы  берется
     * с  $binding.
     */
    public function __construct($id, PropertyBinding $binding, $itemvalue, $groupname = null)
    {
        parent::__construct($id);
        $this->setValue($binding);
        $this->itemvalue = $itemvalue;
        $this->groupname = $groupname != null ? $groupname : $this->value->getPropertyName();
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        $v=  $this->getValue();
        if ($v !== null && $v == $this->itemvalue) {
            $this->setAttribute("checked", "checked");
        } else {
            $this->setAttribute("checked", null);
        }

        //имя  свойства  объекта  из  модели  используется для  групировки
        $this->setAttribute("name", $this->groupname);
        $this->setAttribute("value", $this->itemvalue);
        // if set event
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;
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
    public function getRequestData()
    {
        $this->setValue($_REQUEST[$this->groupname]);
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {
        $this->OnEvent();
    }

    /**
     * @see  ChangeListener
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = true)
    {

        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
    }

    /**
     * @see ChangeListener
     */
    public function OnEvent()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

     public function clean(){
        $this->setValue(-1);
     }    
}

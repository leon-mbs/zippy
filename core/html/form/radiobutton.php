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

        if ($this->getValue() != null && $this->getValue() == $this->itemvalue) {
            $this->setAttribute("checked", "On");
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
            $this->setAttribute("onchange", "javascript:{  $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit();}");
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
        $this->OnChange();
    }

    /**
     * @see  ChangeListener
     */
    public function setChangeHandler(EventReceiver $receiver, $handler)
    {
        $this->event = new Event($receiver, $handler);
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

}

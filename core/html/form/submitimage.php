<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Event;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Html\HtmlComponent;

/**
 * Компонент  тэга  &lt;input type=&quot;image&quot;&gt; для отправки  формы
 */
class SubmitImage extends HtmlComponent implements ClickListener, Requestable
{

    private $event;

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {

        if ($this->getFormOwner()  == null) {
            throw new \Zippy\Exception("Element '" . $this->id . "' outside   FORM tag");
        }

        $formid = $this->getFormOwner()->id;
        //  $this->attributes["onclick"]="javascript:{ $('#".$formattr["id"]."_hf').val('submit1') ; $('#".$formattr["id"]."').submit();}";
        $url = $this->owner->getURLNode() . '::' . $this->id;
        $url = substr($url, 2 + strpos($url, 'q='));
        $this->setAttribute("onclick", "javascript:{if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "');}");
    }

    /**
     * Возвращает  кординаты   клика  в  виде  массива  x и y
     * @return  array
     */
    public function getXY()
    {
        return $this->getValue();
    }

    /**
     * @see  SubmitDataRequest
     */
    public function getRequestData()
    {
        $this->setValue(array('x' => $_REQUEST['x'], 'y' => $_REQUEST['y']));
    }

    /**
     * @see  Requestable
     */
    public function RequestHandle()
    {
        $this->OnClick();
    }

    /**
     * @see  ClickListener
     */
    public function setClickHandler(EventReceiver $receiver, $handler)
    {
        $this->event = new Event($receiver, $handler);
    }

    /**
     * Вызывает  событие  при  клике   мышкой
     */
    public function OnClick()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

}

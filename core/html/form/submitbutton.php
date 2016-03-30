<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Event;
use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;

/**
 * Компонент  тэга  &lt;input type=&quot;submit&quot;&gt; для отправки  формы
 */
class SubmitButton extends HtmlComponent implements ClickListener, Requestable
{
      
    private $event;

    /**
     * @see HtmlComponent
     */
    public function RenderImpl()
    {

        if ( $this->getFormOwner() == null) {
            throw new \Zippy\Exception("Element '" . $this->id . "' outside   FORM tag");
        }

        $formid =  $this->getFormOwner()->id;
        //  $this->attributes["onclick"]="javascript:{ $('#".$formattr["id"]."_hf').val('submit1') ; $('#".$formattr["id"]."').submit();}";
        $url = $this->owner->getURLNode() . '::' . $this->id;
        $url = substr($url, 2 + strpos($url, 'q='));
        $this->setAttribute("onclick", "javascript:{ if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); $('#" . $formid . "_s').trigger('click');}");
        $this->setAttribute("type", 'button');
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {
        $this->OnClick();
    }

    /**
     * @see ClickListener
     */
    public function setClickHandler(EventReceiver $receiver, $handler)
    {
        $this->event = new Event($receiver, $handler);
    }

    /**
     * @see ClickListener
     */
    public function OnClick()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

}

/**
* @todo возмоджность  вставки  в  оnclick  обработчика  отмены
*/


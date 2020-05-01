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

    public function __construct($id) {
        parent::__construct($id);

        $this->setAttribute("name", $this->id);
    }

    /**
     * @see HtmlComponent
     */
    public function RenderImpl() {

        if ($this->getFormOwner() == null) {
            throw new \Zippy\Exception("Element '" . $this->id . "' outside   FORM tag");
        }
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;
            //  $this->attributes["onclick"]="javascript:{ $('#".$formattr["id"]."_hf').val('submit1') ; $('#".$formattr["id"]."').submit();}";
            $url = $this->owner->getURLNode() . '::' . $this->id;
            $url = substr($url, 2 + strpos($url, 'q='));
            if ($this->event->isajax == false) {
                $this->setAttribute("onclick", "javascript:{if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); $('#" . $formid . "_s').trigger('click');event.returnValue=false; return false;}");
            } else {
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $this->setAttribute("onclick", "if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true');");
            }
        }
        $this->setAttribute("type", 'button');
    }

    /**
     * @see Requestable
     */
    public function RequestHandle() {
        $this->OnEvent();
    }

    /**
     * @see ClickListener
     */
    public function onClick(EventReceiver $receiver, $handler, $ajax = false) {
        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
    }

    /**
     * @see ClickListener
     */
    public function OnEvent() {
        if ($this->event != null && strlen($this->getAttribute('disabled')) == 0) {
            $this->event->onEvent($this);
        }
    }

}

/**
 * @todo возмоджность  вставки  в  оnclick  обработчика  отмены
 */


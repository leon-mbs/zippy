<?php

namespace Zippy\Html\Link;

use \Zippy\WebApplication;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Event;

/**
 * Елемент-ссылка  которая  отправляет  форму
 *
 */
class SubmitLink extends AbstractLink implements ClickListener, Requestable
{

    private $event;

    /**
     * Конструктор
     * @param  string ID компонента
     * @param  EventReceiver Объект с методом  обработки  события
     * @param  string Имя  мтода-обработчика
     */
    public function __construct($id, EventReceiver $receiver = null, $handler = null, $ajax = false)
    {
        parent::__construct($id);

        if (is_object($receiver) && strlen($handler) > 0) {
            $this->onClick($receiver, $handler, $ajax);
        }
    }

    /**
     * @see HtmlComponent
     */
    public function RenderImpl()
    {
        parent::RenderImpl();
        if ($this->getFormOwner() == null) {
            throw new \Zippy\Exception("Element '" . $this->id . "' outside   FORM tag");
        }
        $formid = $this->getFormOwner()->id;

        if ($this->disabled == true) {
            $this->setAttribute("href", "");
            $this->setAttribute("onclick", "");
            return;
        }

        $url = $this->owner->getURLNode() . '::' . $this->id;
        $url = substr($url, 2 + strpos($url, 'q='));
        if ($this->event->isajax == false) {
            $this->setAttribute("onclick", "javascript:{if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); $('#" . $formid . "').submit();event.returnValue=false; return false;}");
        } else {
            $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
            $this->setAttribute("onclick", "if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true');");
        }


        //    $this->setAttribute("onclick","javascript:{ var q = $('#".$formid."_q').attr('value');$('#".$formid."_q').attr('value',q+'::".$this->id."');$('#".$formid."').submit();return  false;}");
    }

    /**
     * @see  Requestable
     */
    public function RequestHandle()
    {
        $this->OnEvent();
    }

    /**
     * @see ClickListener
     */
    public function OnEvent()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

    public function onClick(EventReceiver $receiver, $handler, $ajax = false)
    {
        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
    }

}

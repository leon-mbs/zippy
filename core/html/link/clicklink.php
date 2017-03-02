<?php

namespace Zippy\Html\Link;

use \Zippy\WebApplication;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\EventReceiver;
 
use \Zippy\Interfaces\Requestable;
use \Zippy\Event;

/**
 * Ссылка  вызыващая  обработчик не  имеющая  возможности  копирования  адреса  ссылки
 *
 */
class ClickLink extends AbstractLink implements ClickListener, Requestable
{

    protected $event;

    /**
     * Конструктор
     * @param  string ID компонента
     * @param  EventReceiver Объект с методом  обработки  события
     * @param  string Имя  метода-обработчика
     */
    public function __construct($id,   $receiver = null, $handler = null, $ajax = false)
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


        if ($this->disabled == true) {
            $this->setAttribute("href", "");
            //$this->setAttribute("onclick", "");
            return;
        }
        if ($this->event == null) {
            $this->setAttribute("href", "");
          //  $this->setAttribute("onclick", "");
            return;
        }
        
        $this->setAttribute("href", "javascript:void(0);");
        if ($this->event->isajax == false) {
            $url = $this->owner->getURLNode() . "::" . $this->id;
            $this->setAttribute("onclick", "if(beforeZippy('{$this->id}') ==false) return false;window.location='{$url}';event.returnValue=false; return false;");
        } else {
            $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
            $this->setAttribute("onclick", "if(beforeZippy('{$this->id}') ==false) return false;getUpdate('{$url}');event.returnValue=false; return false;");
        }
    }

    /**
     * @see  Requestable
     */
    public function RequestHandle()
    {
        $this->OnEvent();
        // WebApplication::getApplication()->setReloadPage();
    }

    /**
     * @see  ClickListener
     */
    public function onClick(EventReceiver $receiver, $handler, $ajax = false)
    {
        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
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

}

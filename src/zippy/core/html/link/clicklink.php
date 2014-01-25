<?php

namespace Zippy\Html\Link;

use \Zippy\WebApplication;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Interfaces\AjaxClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Event;

/**
 * Ссылка  вызыващая  обработчик не  имеющая  возможности  копирования  адреса  ссылки
 *
 */
class ClickLink extends AbstractLink implements ClickListener, AjaxClickListener, Requestable
{

        protected $event;

        /**
         * Конструктор
         * @param  string ID компонента
         * @param  EventReceiver Объект с методом  обработки  события
         * @param  string Имя  метода-обработчика
         */
        public function __construct($id, EventReceiver $receiver = null, $handler = null)
        {
                parent::__construct($id);

                if (is_object($receiver) && strlen($handler) > 0) {
                        $this->setClickHandler($receiver, $handler);
                }
        }

        /**
         * @see HtmlComponent
         */
        public function RenderImpl()
        {
                if ($this->event == null) {
                        $this->setAttribute("href", "");
                        $this->setAttribute("onclick", "");
                        return;
                }

                if ($this->disabled == true) {
                        $this->setAttribute("href", "");
                        $this->setAttribute("onclick", "");
                        return;
                }
                $this->setAttribute("href", "javascript:void(0);");
                if ($this->event->isajax == false) {
                        $url = $this->owner->getURLNode() . "::" . $this->id;
                        $this->setAttribute("onclick", "if(beforeZippy('{$this->id}') ==false) return false;window.location='{$url}';event.returnValue=false; return false;");
                } else {
                        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
                        $this->setAttribute("onclick", "getUpdate('{$url}');event.returnValue=false; return false;");
                }
        }

        /**
         * @see  Requestable
         */
        public function RequestHandle()
        {
                $this->OnClick();
                // WebApplication::getApplication()->setReloadPage();
        }

        /**
         * @see  ClickListener
         */
        public function setClickHandler(EventReceiver $receiver, $handler)
        {
                $this->event = new Event($receiver, $handler);
        }

        /**
         * @see  AjaxClickListener
         */
        public function setAjaxClickHandler(EventReceiver $receiver, $handler)
        {
                $this->setClickHandler($receiver, $handler);
                $this->event->isajax = true;
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


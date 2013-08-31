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

                if (WebApplication::$context["currentform"] == null) {
                        throw new \Zippy\Exception("Element '" . $this->id . "' outside   FORM tag");
                }
                $formid = WebApplication::$context["currentform"];

                if ($this->disabled == true) {
                        $this->setAttribute("href", "");
                        $this->setAttribute("onclick", "");
                        return;
                }

                $url = $this->owner->getURLNode() . '::' . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $this->setAttribute("onclick", "javascript:{if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); $('#".$formid."').submit();event.returnValue=false; return false;}");
                //    $this->setAttribute("onclick","javascript:{ var q = $('#".$formid."_q').attr('value');$('#".$formid."_q').attr('value',q+'::".$this->id."');$('#".$formid."').submit();return  false;}");
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
         * @see ClickListener
         */
        public function OnClick()
        {
                if ($this->event != null) {
                        $this->event->onEvent($this);
                }
        }

}


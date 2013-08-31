<?php

namespace Zippy\Html\Link;

use \Zippy\WebApplication;
use \Zippy\Event;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Interfaces\AjaxClickListener;
use \Zippy\Interfaces\Requestable;

/**
 * Елемент-ссылка  которая  отправлят  форму  с  помощью  AJAX
 *
 */
class AjaxSubmitLink extends AbstractLink implements AjaxClickListener, Requestable
{

        private $event;

        /**
         * @see  HtmlComponent
         */
        public function RenderImpl()
        {

                if (WebApplication::$context["currentform"] == null) {
                        throw new \Zippy\Exception("Element '" . $this->id . "' outside   FORM tag");
                }
                $formid = WebApplication::$context["currentform"];

                $url = $this->owner->getURLNode() . "::" . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $this->setAttribute("onclick", "if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/index.php?ajax=true');");
        }

        /**
         * @see Requestable
         */
        public function RequestHandle()
        {
                $this->OnClick();
        }

        /**
         * @see AjaxClickListener
         */
        public function setAjaxClickHandler(EventReceiver $receiver, $handler)
        {
                $this->event = new Event($receiver, $handler);

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


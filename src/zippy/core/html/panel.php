<?php

namespace Zippy\Html;

/**
 *  Компонент  выполняющий   роль  контейнера  для  других  компонентов.
 *  Используется для управления  группой   компонентов, например  скрытия  методом  SetVisible
 *
 */
class Panel extends HtmlContainer implements \Zippy\Interfaces\ClickListener, \Zippy\Interfaces\AjaxClickListener, \Zippy\Interfaces\Requestable
{

        protected $event = null;

        public function RenderImpl()
        {
                parent::RenderImpl();

                if ($this->event == null)
                        return;

                if ($this->event->isajax == false) {
                        $url = $this->owner->getURLNode() . "::" . $this->id;
                        $this->setAttribute("onclick", "window.location='{$url}';event.returnValue=false; return false;");
                } else {
                        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
                        $this->setAttribute("onclick", "getUpdate('{$url}');event.returnValue=false; return false;");
                }
        }

        /*
          public function setVisible($visible)
          {
          $this->visible = $visible;
          }

          public function isVisible()
          {
          return $this->visible;
          }

         */

        /**
         * @see  Requestable
         */
        public function RequestHandle()
        {
                parent::RequestHandle();
                $this->OnClick();
                // WebApplication::getApplication()->setReloadPage();
        }

        /**
         * @see  ClickListener
         */
        public function setClickHandler(\Zippy\Interfaces\EventReceiver $receiver, $handler)
        {
                $this->event = new \Zippy\Event($receiver, $handler);
        }

        /**
         * @see  AjaxClickListener
         */
        public function setAjaxClickHandler(\Zippy\Interfaces\EventReceiver $receiver, $handler)
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


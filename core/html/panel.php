<?php

namespace Zippy\Html;

/**
 *  Компонент  выполняющий   роль  контейнера  для  других  компонентов.
 *  Используется для управления  группой   компонентов, например  скрытия  методом  SetVisible
 *
 */
class Panel extends HtmlContainer implements \Zippy\Interfaces\ClickListener, \Zippy\Interfaces\Requestable, \Zippy\Interfaces\AjaxRender
{

    protected $event = null;

    public function RenderImpl() {
        parent::RenderImpl();

        if ($this->event == null) {
            return;
        }

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
    public function RequestHandle() {
        parent::RequestHandle();
        $this->OnEvent();
        // WebApplication::getApplication()->setReloadPage();
    }

    /**
     * @see  ClickListener
     */
    public function onClick(\Zippy\Interfaces\EventReceiver $receiver, $handler, $ajax = false) {
        $this->setClickHandler($receiver, $handler);
        $this->event->isajax = $ajax;
    }

    /**
     * @see ClickListener
     */
    public function OnEvent() {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

    /**
     * @see AjaxRender
     * рендлерит  панель  вместе  с  содержимы  для  ajax  ответа.
     * В  панели  не  должно быть клиентских компонентов  требующих инициализацию
     * с  помощью javascript.
     */
    public function AjaxAnswer() {
        $HtmlTag = pq('[zippy="' . $this->id . '"]');
        $html = $HtmlTag->html();

        $html = json_encode($html);

        $js = "var _h =  {$html} ;   ";

        $js .= "$('#{$this->id}').html(_h);";

        return $js;
    }

}

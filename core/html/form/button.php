<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Exceptions\Exception;
use \Zippy\Event;
use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\AjaxClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;

/**
 * Компонент  тэга  &lt;input type=&quot;button&quot;&gt; для отправки  формы
 */
class Button extends HtmlComponent implements ClickListener, AjaxClickListener, Requestable
{

    private $event;

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
        $this->setAttribute("type", 'button');
        if ($this->event == null)
            return;

        if ($this->event->isajax == false) {
            $url = $this->owner->getURLNode() . "::" . $this->id;
            $this->setAttribute("onclick", "window.location='{$url}';");
        } else {
            $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
            $this->setAttribute("onclick", "getUpdate('{$url}');");
        }
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

<?php

namespace Zippy\Html\Link;

use \Zippy\WebApplication;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\EventReceiver;

use \Zippy\Interfaces\Requestable;
use \Zippy\Event;

/**
 * Ссылка  для ортировки заголовков  таблиц
 *
 */
class SortLink extends AbstractLink implements ClickListener, Requestable
{
    public $fileld = "";
    public $dir = "";

    protected $event;

    /**
     * Конструктор
     * @param string ID компонента
     * @param fileld поле сортировки
     * @param EventReceiver Объект с методом  обработки  события
     * @param string Имя  метода-обработчика
     */
    public function __construct($id, $fileld, $receiver = null, $handler = null, $ajax = false) {
        parent::__construct($id);
        $this->fileld = $fileld;
        if (is_object($receiver) && $handler != null) {
            $this->onClick($receiver, $handler, $ajax);
        }
    }

    /**
     * @see HtmlComponent
     */
    public function RenderImpl() {
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
            $this->setAttribute("onclick", "window.location='{$url}';event.returnValue=false; return false;");
        } else {
            $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
            $this->setAttribute("onclick", "getUpdate('{$url}');event.returnValue=false; return false;");
        }
        if (strlen($this->dir) == 0) {
            return;
        }

        $HtmlTag = $this->getTag();
        $content = $HtmlTag->text();
        if ($this->dir == "asc") {
            $HtmlTag->html($content . " <i class=\"fa fa-sort-up\"></i>");
        } else {
            if ($this->dir == "desc") {
                $HtmlTag->html($content . " <i class=\"fa fa-sort-down\"></i>");
            } else {
                $HtmlTag->text($content);
            }
        }


    }

    /**
     * @see  Requestable
     */
    public function RequestHandle() {
        if (strlen($this->dir) == 0) {
            $this->dir = "asc";
        } else {
            if ($this->dir == "asc") {
                $this->dir = "desc";
            } else {
                $this->dir = "asc";
            }
        }

        $this->OnEvent();
        // WebApplication::getApplication()->setReloadPage();
    }

    /**
     * @see  ClickListener
     */
    public function onClick(EventReceiver $receiver, $handler, $ajax = false) {
        $this->event = new Event($receiver, $handler);
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
     * сброс сортировки
     *
     */
    public function Reset() {

        $this->field = "";
        $this->dir = "";

    }

}

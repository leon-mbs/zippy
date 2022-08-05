<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Requestable;
use \Zippy\WebApplication;
use \Zippy\HtpRequest;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Класс  отображающий  разбивку  списка  по страницам
 * По умолячанию используется Pager из TB
 */
class Pager extends HtmlComponent implements Requestable
{

    private $datalist;
    private $ajax;
    protected $event = null;


    /**
     * Конструктор
     * @param string ID
     * @param DataList Объект  использующий  paginator
     * @param ajax исппользовать  ajax
     */
    public function __construct($id, \Zippy\Html\DataList\AbstractList $datalist, $ajax = false) {
        parent::__construct($id);
        $this->datalist = $datalist;
        $this->ajax = $ajax;
    }

    /**
     * Формирует  URL для  ссылки  в  списке  страниц
     */

    /**
     * @see HtmlComponent
     */
    protected final function RenderImpl() {
        $content = $this->getContent($this->datalist->getPageCount(), $this->datalist->getCurrentPage());
        $this->getTag()->html($content);
    }

    /**
     * Рендерит  список   страниц
     */
    public function getContent($pages, $currentpage) {
        if ($pages == 1) {
            return "";
        }


        $content = "";


        if ($currentpage > 1) {
            $content .= "<td align='left'><a      href=\"javascript:void(0);\" onclick=\"" . $this->getUrl($currentpage - 1) . "\"> &#171; вперед</a></td>";
        }
        if ($currentpage < $pages) {
            $content .= " <td align='right'> <a    href=\"javascript:void(0);\" onclick=\"" . $this->getUrl($currentpage + 1) . "\"> назад  &#187; </a></td>";
        }


        $content = $content . "</ul>";
        $countall = $this->datalist->getAllRowsCount();
        $show = $currentpage * $this->datalist->getPageSize();
        if ($pages == $currentpage) {
            $show = $countall;
        }
        if ($countall <= $this->datalist->getPageSize()) {
            $show = $countall;
        }


        if ($countall > 0) {
            $content = "<table class=\"w-100\"  ><tr><td width=\"125px\" valign='middle'>{$show}   строк з {$countall} &nbsp;&nbsp;&nbsp;&nbsp;</td> {$content}</tr></table>";
        }

        return $content;
    }

    /**
     * @see  Requestable
     */
    public final function RequestHandle() {
        $p = WebApplication::$app->getRequest()->request_params[$this->id];

        if ($this->event != null) {
            $this->event->onEvent($this, $p[0]);
        }

        $this->datalist->setCurrentPage($p[0]);
        $this->datalist->Reload(false);
    }

    /**
     * устанавливает  обработчик  собыиий  на  переход  по  страницамю
     * в  обработчике  событий кроме  sender  передается  второй параметр - номер  выбраной страницы  пагинацтора
     * @param EventReceiver $receiver
     * @param mixed $handler
     */
    public function onPage(EventReceiver $receiver, $handler) {
        $this->event = new Event($receiver, $handler);

    }

    private function getUrl($pageno) {
        $url = $this->owner->getURLNode();

        if ($this->ajax == true) {
            $url .= "::" . $this->id . ":" . $pageno . "&ajax=true";
            $onclick = "getUpdate('{$url}');event.returnValue=false; return false;";
        } else {
            $url .= "::" . $this->id . ":" . $pageno;
            $onclick = "window.location='{$url}';event.returnValue=false; return false;";
        }

        return $onclick;
    }

    /**
     * Сбрасываем  в  начало
     */
    public final function Reset() {
        $this->datalist->setCurrentPage(1);
    }

    /**
     * Устанавливает нажписи на  кнопках
     *
     * @param mixed $prev
     * @param mixed $next
     */
    public function setLabes($prev, $next) {
        $this->prev = $prev;
        $this->next = $next;
    }


}

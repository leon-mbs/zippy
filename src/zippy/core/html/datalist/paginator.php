<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Requestable;
use \Zippy\WebApplication;
use \Zippy\HtpRequest;

/**
 * Класс  отображающий  разбивку  списка  по страницам
 */
class Paginator extends HtmlComponent implements Requestable
{

    private $datalist;
    private $size = 20;

    /**
     * Конструктор
     * @param string ID
     * @param  DataList Объект  использующий  paginator
     */
    public function __construct($id, AbstractList $datalist)
    {
        parent::__construct($id);
        $this->datalist = $datalist;
    }

    /**
     * Формирует  URL для  ссылки  в  списке  страниц
     */

    /**
     * @see HtmlComponent
     */
    protected final function RenderImpl()
    {
        $content = $this->getContent($this->datalist->getPageCount(), $this->datalist->getCurrentPage());
        $this->getTag()->html($content);
    }

    /**
     * Рендерит  список   страниц
     */
    public function getContent($pages, $currentpage)
    {
        $content = "<ul class=\"paginator\">";
        if ($pages == 1)
            return "";

        if ($currentpage > 1) {
            $content .= "<li class=\" first\"><a  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl(1) . "\">&nbsp;</a></li>";
        }

        if ($currentpage > 1) {
            //  $content .= "<li class=\"disabled\"><a  href=\"javascript:void(0);\" >...</a></li>";
        }

        for ($i = 1; $i <= $pages; $i++) {
            if ($currentpage == $i) {
                $content .= "<li class=\"active\"><a  href=\"javascript:void(0);\" > {$i} </a></li>";
            } else {
                $content .= "<li><a  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($i) . "\"> {$i} </a></li>";
            }
        }

        if ($currentpage < $pages) {
            //  $content .= "<li><a  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($currentpage + 1) . "\">&nbsp;</a></li>";
        }

        if ($currentpage < $pages) {
            $content .= "<li class=\" last\"><a  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($pages) . "\">&nbsp;</a></li>";
        }

        return $content . "</ul>";
    }

    /**
     * @see  Requestable
     */
    public final function RequestHandle()
    {
        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        $this->datalist->setCurrentPage($p[0]);
        $this->datalist->Reload();
    }

    private function getUrl($pageno)
    {
        $url = $this->owner->getURLNode();
        $url .= "::" . $this->id . ":" . $pageno;
        $onclick = "window.location='{$url}';event.returnValue=false; return false;";

        return $onclick;
    }

    /**
     * Сбрасываем  в  начало
     */
    public final function Reset()
    {
        $this->datalist->setCurrentPage(1);
    }

    /**
     * Устанавливает  размер  пагинатора
     * 
     * @param mixed $size
     */
    public final function setSize($size)
    {
        if ($size > 5) {
            $this->size = $size;
        }
    }

}

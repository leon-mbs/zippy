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
    private $maxbuttons = 14;
    private $firstButton = 1;

    /**
     * Конструктор
     * @param string ID
     * @param  DataList Объект  использующий  paginator
     */
    public function __construct($id, \Zippy\Html\DataList\AbstractList $datalist)
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
        if ($pages == 1)  return "";

        if($this->getAttribute('data-maxbtn') > 0){
            $this->maxbuttons = $this->getAttribute('data-maxbtn') -1;
        }
        $content = "<ul class=\"pagination\">";


        if($currentpage - $this->firstButton > $this->maxbuttons){

            $this->firstButton =  $currentpage  - $this->maxbuttons;
        }
        if($currentpage < $this->firstButton){
            $this->firstButton = $currentpage-1;
        }

        if ($this->firstButton > 1) {
           $content .= "<Li><a   href='void(0);' onclick=\"" . $this->getUrl(1) . "\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
           $content .= "<Li><a   href='void(0);' onclick=\"" . $this->getUrl($currentpage - 1) . "\"><span aria-hidden=\"true\">&lsaquo;</span></a></li>";
           $content .= "<li ><a  href=\"javascript:void(0);\" >&hellip;</a></li>";
         }



        for ($i = $this->firstButton; $i <= $this->firstButton + $this->maxbuttons; $i++) {
            if($i > $pages) break;
            if ($currentpage == $i) {
                $content .= "<li class=\"active\"><a  href=\"javascript:void(0);\" > {$i} </a></li>";
            } else {
                $content .= "<li><a  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($i) . "\"> {$i} </a></li>";
            }
        }

        if($pages > $this->firstButton + $this->maxbuttons){
               $content .= "<li ><a  href=\"javascript:void(0);\" >&hellip;</a></li>";
               $content .= "<li><a href='void(0);' onclick=\"" . $this->getUrl($currentpage + 1) . "\"aria-label=\"Next\">       <span aria-hidden=\"true\">&rsaquo;</span></a></li>";
               $content .= "<li><a href='void(0);' onclick=\"" . $this->getUrl($pages) . "\"aria-label=\"Next\">       <span aria-hidden=\"true\">&raquo;</span></a></li>";

        }

        return $content . "</ul>";
    }

    /**
     * @see  Requestable
     */
    public final function RequestHandle()
    {
        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        $this->OnPage($p[0]);
        $this->datalist->setCurrentPage($p[0]);
        $this->datalist->Reload(false);
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
     * @param mixed $maxbuttons
     */
    public final function setMaxButtons($maxbuttons)
    {
        if ($maxbuttons > 1) {
            $this->maxbuttons = $maxbuttons-1;
        }
    }

    /**
    * перегружается  для получения  номера страницы при  клике  на  паггинатор
    *
    * @param mixed $pageno
    */
    public function OnPage($pageno){

    }
}

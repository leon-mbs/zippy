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
 */
class Paginator extends HtmlComponent implements Requestable
{

    private $datalist;
    private $maxbuttons = 14;
    private $firstButton = 1;
    private $ajax;
    protected $event=null;
  
    /**
     * Конструктор
     * @param string ID
     * @param  DataList Объект  использующий  paginator
     * @param  ajax исппольховать  ajax
     */
    public function __construct($id, \Zippy\Html\DataList\AbstractList $datalist, $ajax = false)
    {
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
        if ($pages == 1)
            return "";

        if ($this->getAttribute('data-maxbtn') > 0) {
            $this->maxbuttons = $this->getAttribute('data-maxbtn') - 1;
        }
        $content = "<ul class=\"pagination\">";


        if ($currentpage - $this->firstButton > $this->maxbuttons) {

            $this->firstButton = $currentpage - $this->maxbuttons;
        }
        if ($currentpage < $this->firstButton) {
            $this->firstButton = $currentpage - 1;
        }

        if ($this->firstButton > 1) {
            $content .= "<Li class=\"page-item\" ><a  class=\"page-link\"   href='void(0);' onclick=\"" . $this->getUrl(1) . "\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
            $content .= "<Li class=\"page-item\" ><a   class=\"page-link\"   href='void(0);' onclick=\"" . $this->getUrl($currentpage - 1) . "\"><span aria-hidden=\"true\">&lsaquo;</span></a></li>";
            $content .= "<li class=\"page-item\"  ><a  class=\"page-link\"   href=\"javascript:void(0);\" >&hellip;</a></li>";
        }



        for ($i = $this->firstButton; $i <= $this->firstButton + $this->maxbuttons; $i++) {
            if ($i > $pages)
                break;
            if ($currentpage == $i) {
                $content .= "<li class=\"page-item active\"><a   class=\"page-link\"  href=\"javascript:void(0);\" > {$i} </a></li>";
            } else {
                $content .= "<li class=\"page-item\" ><a   class=\"page-link\"  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($i) . "\"> {$i} </a></li>";
            }
        }

        if ($pages > $this->firstButton + $this->maxbuttons) {
            $content .= "<li class=\"page-item\" ><a class=\"page-link\" href=\"javascript:void(0);\" >&hellip;</a></li>";
            $content .= "<li class=\"page-item\" ><a   class=\"page-link\" href='void(0);' onclick=\"" . $this->getUrl($currentpage + 1) . "\"aria-label=\"Next\">       <span aria-hidden=\"true\">&rsaquo;</span></a></li>";
            $content .= "<li class=\"page-item\" ><a   class=\"page-link\" href='void(0);' onclick=\"" . $this->getUrl($pages) . "\"aria-label=\"Next\">       <span aria-hidden=\"true\">&raquo;</span></a></li>";
        }

        return $content . "</ul>";
    }

    /**
     * @see  Requestable
     */
    public final function RequestHandle()
    {
        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        
        if ($this->event != null) {
            $this->event->onEvent($this,$p[0]);
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
    public function onPage(EventReceiver $receiver, $handler)
    {
        $this->event = new Event($receiver, $handler);
    
    }
 
    private function getUrl($pageno)
    {
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
            $this->maxbuttons = $maxbuttons - 1;
        }
    }

    

}

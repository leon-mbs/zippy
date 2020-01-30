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
 * По умолячанию используется Pagination из TB
 */
class Paginator extends HtmlComponent implements Requestable
{

    private $datalist;
    private $maxbuttons = 10;
    private $firstButton = 1;
    private $ajax;
    protected $event=null;
  
    /**
     * Конструктор
     * @param string ID
     * @param  DataList Объект  использующий  paginator
     * @param  ajax исппольховать  ajax
     */
    public function __construct($id, \Zippy\Html\DataList\AbstractList $datalist, $pagesize=0, $ajax = false)
    {
        parent::__construct($id);
        $this->datalist = $datalist;
        $this->ajax = $ajax;
        if($pagesize>0){
           $this->datalist->setPageSize($pagesize); 
        }
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
        if ($pages <= 1)
            return "";
        if ($currentpage > $pages)
            $currentpage =1;

 
        $content = "<ul class=\"pagination\">";

        
        
        $iLeft=(int)$this->maxbuttons/2; 
        $iRight=$iLeft;

        
        if($pages <= $iRight+$iRight+1){
            for($i=1; $i<=$pages ; $i++)
            {
              
                 if ($currentpage == $i) {
                    $content .= "<li class=\"page-item active\"><a   class=\"page-link\"  href=\"javascript:void(0);\" > {$i} </a></li>";
                } else {
                    $content .= "<li class=\"page-item\" ><a   class=\"page-link\"  href=\"javascript:void(0);\"   onclick=\"" . $this->getUrl($i) . "\"> {$i} </a></li>";
                }               
                
            }         
        }
        else
        if($currentpage > $iLeft && $currentpage < ($pages-$iRight))
        {
            $content .= "<Li class=\"page-item\" ><a  class=\"page-link\"   href='void(0);' aria-label=\"Previous\"  onclick=\"" . $this->getUrl(1) . "\"><span    aria-hidden=\"true\" >&laquo;</span></a></li>";
            
            for($i=$currentpage-$iLeft; $i<=$currentpage+$iRight; $i++)
            {
              
                 if ($currentpage == $i) {
                    $content .= "<li class=\"page-item active\"><a   class=\"page-link\"  href=\"javascript:void(0);\" > {$i} </a></li>";
                } else {
                    $content .= "<li class=\"page-item\" ><a   class=\"page-link\"  href=\"javascript:void(0);\"   onclick=\"" . $this->getUrl($i) . "\"> {$i} </a></li>";
                }               
                
            }
            $content .= "<li class=\"page-item\" ><a   class=\"page-link\" href='void(0);' aria-label=\"Next\"  onclick=\"" . $this->getUrl($pages) . "\">    <span   aria-hidden=\"true\">&raquo;</span></a></li>";
   
        }
        elseif($currentpage<=$iLeft)
        {
                
            $iSlice = 1+$iLeft-$currentpage;
            for($i=1; $i<=$currentpage+($iRight+$iSlice); $i++)
            {
                if ($currentpage == $i) {
                    $content .= "<li class=\"page-item active\"><a   class=\"page-link\"  href=\"javascript:void(0);\" > {$i} </a></li>";
                } else {
                    $content .= "<li class=\"page-item\" ><a   class=\"page-link\"  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($i)  . "\"> {$i} </a></li>";
                }  
                               
            }
            $content .= "<li class=\"page-item\" ><a   class=\"page-link\" href='void(0);' aria-label=\"Next\"  onclick=\"" . $this->getUrl($pages)   . "\">    <span   aria-hidden=\"true\">&raquo;</span></a></li>";
            
        } 
        else
        {
            $content .= "<Li class=\"page-item\" ><a  class=\"page-link\"   href='void(0);' aria-label=\"Previous\"  onclick=\"" . $this->getUrl(1) . "\"><span    aria-hidden=\"true\" >&laquo;</span></a></li>";
      
            $iSlice = $iRight-($pages - $currentpage);
             
            for($i=$currentpage-($iLeft+$iSlice); $i<=$pages; $i++)
            {
                 if ($currentpage == $i) {
                                $content .= "<li class=\"page-item active\"><a   class=\"page-link\"  href=\"javascript:void(0);\" > {$i} </a></li>";
                 } else {
                                $content .= "<li class=\"page-item\" ><a   class=\"page-link\"  href=\"javascript:void(0);\"  onclick=\"" . $this->getUrl($i) . "\"> {$i} </a></li>";
                 } 
            }                
                  
        }          
        
        $content = $content . "</ul>";
        $countall = $this->datalist->getAllRowsCount();
        $show =  $currentpage *  $this->datalist->getPageSize();
        if($pages ==$currentpage) $show = $countall;
        if($countall <=  $this->datalist->getPageSize()) $show = $countall;
               
        
        $content = "<table  ><tr><td valign='middle'>{$show} строк из {$countall} &nbsp;&nbsp;&nbsp;&nbsp;</td><td align='right'> {$content}</td></tr></table>";
        
        return $content  ;
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

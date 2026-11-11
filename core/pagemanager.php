<?php

namespace Zippy;

use Zippy\Html\WebPage;

/**
 * Менеджер  страниц. Содержит состояние объектов классов
 * страниц  и их  историю.
 *
 */
class PageManager
{
 

    private $pages = [];
    private $currpage ='' ;
    private $prevpage ='' ;
  
 
    /**
    * 
    * 
    * @param WebPage $page
    */
    final public function addPage(WebPage $page) {
         $this->prevpage = $this->currpage ;  
         $pname = get_class($page)  ;
         $this->currpage = $pname;
         $this->pages[$pname] = $page;   
    }
    /**
     * Обновляет  персистентные  данные  страницы.
     * @param WebPage  Экземпляр класса страницы
  
     */
    final public function updatePage(WebPage $page) {

        $page->beforeSaveToSession();
       
        $pname = get_class($page)  ;
   
        $this->pages[$pname] = $page;
    
    }
    /**
     * Возвращает  из сессии   страницу   
     * @param  Индекс (строковое имя) страницы
     * @return  WebPage   страница
     */
    final public function getPage($pname) {
           
           ;
   
           if( !isset( $this->pages[$pname] )) {
               return null;
           }
      
            $page = $this->unpack($this->pages[$pname]);
  

            if ($page instanceof WebPage) {
                $page->afterRestoreFromSession();
            }  
            return $page;
       
    }

    /**
     * Возвращает  из сессии последнюю добавленую страницу
     * @return  WebPage  страница
     */
    final public function getLastPage() {
        
         return $this->unpack( $this->pages[$this->currpage] );
    }

  
    final public function getPrevPage()  {
      
        return $this->unpack( $this->pages[$this->prevpage] );
 
    }

  

    public function __sleep() {
       
                //упаковываем страницы
        $pl = array();
        foreach ($this->pages as $n => $p) {
            if ($p instanceof WebPage) {
                $pl[] = $n;
            }
        }
        foreach ($pl as $n) {
            $this->pages[$n] = $this->pack($this->pages[$n]);
        }   
        
         
        return array('pages','currpage','prevpage' );
    }
   /*
    public function __wakeup() {
        if (is_array($this->pages)) {
            return;
        }
      
    }

    */


    private function pack($page) {
      return gzcompress(serialize($page));
    }

    private function unpack($page) {
        if ($page instanceof WebPage) {
               return $page;
        }
       
       
        $p = @gzuncompress($page);
        if (strlen($p) == 0) {
            return null;
        }
        return unserialize($p);
    }

}

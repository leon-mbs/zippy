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
    public const HISTORY_SIZE = 50;

    private $pages = array();
    private $index = 0;
    private $pchain = array();
    private $curpage = '';
    private $prevpage = '';

    /**
     * Добавляет новую  страницу  (объект  WebPage) в  список.
     * @param WebPage $page страница
     * @deprecated
     */
    final public function putPage(WebPage $page) {
        $page->beforeSaveToSession();


        $this->pages[++$this->index] = ($page);
        if ($this->index > self::HISTORY_SIZE) {
            $this->pages[$this->index - self::HISTORY_SIZE] = null;
        }

        $prevpage = $this->unpack($this->pages[$this->index - 1]);
        if ($prevpage instanceof \Zippy\Html\WebPage) {

            if (get_class($prevpage) != get_class($page)) {
                //  если страница  изменилась запоминаем

                $this->pchain[] = get_class($page);
            }
        }

        return $this->index;
    }
    /**
     * Обновляет  персистентные  данные  страницы.
     * @param WebPage $page Экземпляр класса страницы
     */
    final public function updatePage(WebPage $page) {

        $page->beforeSaveToSession();
        $pname = get_class($page)    ;
        if($this->curpage != $pname) {
            $this->prevpage = $this->curpage;
            $this->pchain[] = $pname;
        }
        $this->curpage = $pname    ;
        $this->pages[$pname] = $page;
        return $pname;
    }
    /**
     * Возвращает  из сессии  страницу  по  номеру   и  версии
     * @param Integer  Номер  страницы
     * @param int  Версия  страницы
     * @return  WebPage   страница
     */
    final public function getPage($number) {
        if (isset($this->pages[$number])) {

            if ($this->pages[$number] instanceof WebPage) {
                $page = ($this->pages[$number]);
            } else {
                $page = $this->unpack($this->pages[$number]);
            }


            if ($page instanceof WebPage) {
                $page->afterRestoreFromSession();
            } else {
                $page = null;
            }
            return $page;
        }

        return null;
    }

    /**
     * Возвращает  из сессии последнюю добавленую страницу
     * @return  WebPage  страница
     */
    final public function getLastPage() {
        return array_pop($this->pchain);
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
        return array('pages', 'index', 'pchain','curpage','prevpage');
    }

    public function __wakeup() {
        if (is_array($this->pages)) {
            return;
        }
        // $this->pages = @unserialize(@gzuncompress($this->pages));
    }

    /**
     * возвращает класс предыдущей страницы
     *
     */
    public function getPrevPage() {
        array_pop($this->pchain);
        return array_pop($this->pchain);
    }


    private function pack($page) {
        return gzcompress(serialize($page));
    }

    private function unpack($page) {
        $p = @gzuncompress($page);
        if (strlen($p) == 0) {
            return null;
        }
        return unserialize($p);
    }

}

<?php

namespace Zippy\Html\DataList;

use Zippy\Html\HtmlContainer;

/**
 *   Базовый клас  для компонентов - списков(таблиц) данных
 */
abstract class AbstractList extends HtmlContainer
{
    protected $pagesize = PHP_INT_MAX;
    protected $currentpage = 1;
    protected $DataSource;
    protected $pagerowscount = 0;
    protected $rowscount = -1;
    protected $sortf = null;
    protected $sortd = null;

    /**
     * Конструктор
     * @param ID компонента
     * @param Zippy\Interfaces\DataSource Источник данных
     */
    public function __construct($id, $DataSource) {
        HtmlContainer::__construct($id);
        $this->DataSource = $DataSource;
        $this->pagesize = PHP_INT_MAX;
    }

    /**
     * Возвращает  количество  всех  строк  в  наборе
     */
    public function getAllRowsCount() {

        if ($this->rowscount == -1) {
            $this->rowscount = $this->DataSource->getItemCount();
        }
        return $this->rowscount;
    }

    /**
     * Возвращает  количество  всех  строк  на  текущей  странице
     */
    public function getPageRowsCount() {
        return $this->pagerowscount;
    }

    /**
     *  Возвращает  размер  страницы
     */
    public function getPageSize() {
        return $this->pagesize;
    }

    /**
     * Устанавливает размер  страницы  данных
     * @param int Количество строк в странице данных
     */
    public function setPageSize($pagesize) {
        if ($pagesize > 0) {
            $this->pagesize = $pagesize;
        } else {
            $this->pagesize = 20;
        }

        $this->currentpage = 1;
        // $this->Reload();
    }

    /**
     * Возвращает  номер  текущей  страницы
     * @return int
     */
    public function getCurrentPage() {
        return $this->currentpage;
    }

    /**
     * Устанавливает  текущую  страницу
     * @param int Номер  страницы
     * @see Paginator
     */
    public function setCurrentPage($page) {
        if ($page > 0 && $page <= $this->getPageCount()) {
            $this->currentpage = $page;
        } else {
            $this->currentpage = 1;
        }

        // $start = ($this->currentpage - 1) * $this->pagesize;
        // $this->setPageRows($start, $this->pagesize);
        //  $this->Reload();
    }

    /**
     * Количество  страниц  в  списке
     */
    final public function getPageCount() {

        $rowcount = $this->getAllRowsCount();
        return ceil($rowcount / $this->pagesize);
    }

    /**
     * Обновляет  данные  с  провайдера
     */
    public function Reload($resetpage = true) {

        if ($resetpage) {
            $this->setCurrentPage(1);
            $this->rowscount = -1;
        }
    }

    /**
     * Возвращает  ссылку  на  источник данных
     *
     */
    public function getDataSource() {
        return $this->DataSource;
    }

    public function setDataSource($ds) {
        $this->DataSource = $ds;
    }


    /**
     * Возвращает  данные  текущей страницы
     */
    protected function getItems() {
        $list = $this->DataSource->getItems($this->pagesize * ($this->currentpage - 1), $this->getPageSize(), $this->sortf, $this->sortd);
        $this->pagerowscount = count($list);
        return is_array($list) ? $list : array();
    }

    /**
     * Возвращает  массив  строк
     * return  array
     */
    public function getDataRows() {
        $list = array();
        foreach ($this->components as $child) {
            if ($child instanceof DataRow) {
                $list[] = $child;
            }
        }
        return $list;
    }

    /**
     * @see HtmlComponent
     */
    public function Render() {

        $this->beforeRender();
        $this->RenderImpl();
        $this->afterRender();
    }

    /**
     * Устанавливает  имя  поля  и направление  сортировки.
     * Установленные  параметры  передаютя  провайдеру  данных
     */
    final public function setSorting($field, $dir = 'asc') {
        $this->sortf = $field;
        $this->sortd = $dir;
    }

    final public function getSorting() {
        return array('field'=>$this->sortf,'dir'=>$this->sortd);
    }




}

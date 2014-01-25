<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlContainer;

/**
 *   Базовый клас  для компонентов - списков(таблиц) данных
 */
abstract class AbstractList extends HtmlContainer
{

        protected $pagesize = PHP_INT_MAX, $currentpage = 1;
        public $DataSource;
        protected $pagerowscount = 0;
        protected $sortf = null, $sortd = 'asc';

        /**
         * Конструктор
         * @param  ID компонента
         * @param  Zippy\Interfaces\DataSource Источник данных 
         */
        public function __construct($id, $DataSource)
        {
                HtmlContainer::__construct($id);
                $this->DataSource = $DataSource;
                $this->pagesize = 100;
        }

        /**
         * Возвращает  количество  всех  строк  в  наборе
         */
        public function getAllRowsCount()
        {
                return $this->DataSource->getItemCount();
        }

        /**
         * Возвращает  количество  всех  строк  на  текущей  странице
         */
        public function getPageRowsCount()
        {
                return $this->pagerowscount;
        }

        /**
         *  Возвращает  размер  страницы
         */
        public function getPageSize()
        {
                return $this->pagesize;
        }

        /**
         * Устанавливает размер  страницы  данных
         * @param int Количество строк в странице данных
         */
        public function setPageSize($pagesize)
        {
                $this->pagesize = $pagesize;
                $this->currentpage = 1;
                // $this->Reload();
        }

        /**
         * Возвращает  номер  текущей  страницы
         * @return int
         */
        public function getCurrentPage()
        {
                return $this->currentpage;
        }

        /**
         * Устанавливает  текущую  страницу
         * @param int Номер  страницы
         * @see Paginator
         */
        public function setCurrentPage($page)
        {
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
        public final function getPageCount()
        {
                //$rowcount = $this->getAllRowsCount();
                $countall = $this->getAllRowsCount();
                return ceil($countall / $this->pagesize);
        }

        /**
         * Обновляет  данные  с  провайдера
         */
        public function Reload()
        {
                
        }

        /**
         * Возвращает  ссылку  на  источник данных
         * 
         */
        public function getDataSource()
        {
                return $this->DataSource;
        }

        /**
         * Возвращает  данные  текущей страницы
         */
        protected function getItems()
        {
                $list = $this->DataSource->getItems($this->pagesize * ($this->currentpage - 1), $this->getPageSize(), $this->sortf, $this->sortd);
                $this->pagerowscount = count($list);
                return is_array($list) ? $list : array();
        }

        /**
         * Возвращает  массив  строк
         * return  array
         */
        public function getDataRows()
        {
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
        public function Render()
        {

                $this->beforeRender();
                $this->RenderImpl();
                $this->afterRender();
        }

        /**
         * Устанавливает  имя  поля  и направление  сортировки.
         * Установленные  параметры  передаютя  провайдеру  данных
         */
        public final function setSorting($field, $asc = 'asc')
        {
                $this->sortf = $field;
                $this->sortd = $asc;
        }

}


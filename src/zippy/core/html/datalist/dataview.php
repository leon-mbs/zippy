<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Paginable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Класс  вывода  табличных  данных
 * может  использватся  с  тэгом  TABLE или  вложенными  DIV
 * например 
 * &lt;table zippy="id">
 *   &lt;tr zippy="rowid" >
 *  Элементы  строки  формируются разработчиком  при  имплементации  обработчика
 *  события  rowevent
 *
 */
class DataView extends AbstractList
{

        private $rowevent = null;
        private $selectedRow = 0;
        private $selectedclass = "";

        /**
         * Конструктор
         * @param  mixed  ID  компонента
         * @param  mixed  ID  строки
         * @param  mixed   Провадер  данных для  таблицы
         */
        public function __construct($id, $DataSource, EventReceiver $receiver, $handler)
        {
                AbstractList::__construct($id, $DataSource);

                $this->rowevent = new Event($receiver, $handler);
        }

        /**
         * Обновляет  данные  с  провайдера
         */
        public function Reload()
        {
                $this->components = array();

                $list = $this->getItems();
                $i = 1;

                foreach ($list as $item) {           //$datarow = new DataRow($this->id,$list[0]);
                        $datarow = new DataRow($this->id, $item, $i++);
                        $this->add($datarow);
                        $this->rowevent->onEvent($datarow); //вызов  обработчика добавляющего  данные  или   елементы  в  строку
                        $datarow->updateChildId();
                        if ($item->getID() == $this->selectedRow && $this->selectedclass != "") {
                                $datarow->setAttribute('class', $this->selectedclass);
                        }
                }
        }

        /**
         * добавляет  строку   в  таблицу  данных
         * переопределяется в дочерних для добавления компонентов строки
         */
        //public abstract function addRowElements(DataRow $row,\Zippy\Interfaces\DataItem $item);

        /**
         * @see HtmlComponent
         */
        public final function RenderImpl()
        {
                // $this->UpdateData() ;

                $rowtag = pq('[zippy=' . $this->id . ']');

                if ($rowtag->size() == 0) {
                        throw new \Zippy\Exception(sprintf(ERROR_MARKUP_NOTFOUND, $this->id));
                }

                $currtag = $rowtag;
                $c = $this->getPageRowsCount();
                $html_ = $rowtag->htmlOuter();
                $html = "";
                for ($i = 1; $i <= $c; $i++) {
                        $html .= $html_;
                }
                $rowtag->replaceWith($html);

                $rows = pq('[zippy=' . $this->id . ']'); //массив копий
                $i = 1;

                foreach ($rows as $row) {
                        $_id = '_' . $i++;
                        pq($row)->attr("zippy", $this->id . $_id);

                        $id = pq($row)->attr("id");
                        if (strlen($id) > 0) {
                                pq($row)->attr("id", $id . $_id);
                        }

                        $children = pq('[zippy=' . $this->id . $_id . ']  [zippy]');
                        foreach ($children as $child) {
                                $zippy = pq($child)->attr("zippy");

                                pq($child)->attr("zippy", $zippy . $_id);

                                $id = pq($child)->attr("id");
                                if (strlen($id) > 0) {
                                        pq($child)->attr("id", $id . $_id);
                                }
                        }
                }


                foreach ($this->getChildComponents() as $component) {


                        $component->Render();
                }

                //    parent::RenderImpl();
        }

        /**
         *  Устанавливает  выделеную строку. 
         * Строка выделяется  добавлением CSS класса  заданного
         * методом setSelectedClass
         * 
         * @param mixed $id   ID  выделяемой строки
         */
        public function setSelectedRow($id)
        {
                $this->selectedRow = $id;
        }

        /**
         * Возвращает  номер  выделеной  строки
         * 
         */
        public function getSelectedRow()
        {
                return $this->selectedRow;
        }

        /**
         * Устанавливает CSS  класс  для   выбранной   строки
         * 
         * @param mixed $selectedclass
         */
        public function setSelectedClass($selectedclass)
        {
                $this->selectedclass = $selectedclass;
        }

}


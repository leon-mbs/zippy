<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Paginable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;
use \Zippy\WebApplication;

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
class DataView extends AbstractList implements \Zippy\Interfaces\Requestable
{

    private $rowevent = null;
    private $selectedRow = null;
    private $selectedclass = "";
    private $cellclickevent = null;

    /**
     * Конструктор
     * @param mixed  ID  компонента
     * @param mixed   Провадер  данных для  таблицы
     */
    public function __construct($id, $DataSource, EventReceiver $receiver, $handler) {
        AbstractList::__construct($id, $DataSource);

        $this->rowevent = new Event($receiver, $handler);
    }

    /**
     * Обновляет  данные  с  провайдера
     */
    public function Reload($resetpage = true) {
        parent::Reload($resetpage);

        $this->components = array();

        $list = $this->getItems();
        $i = 1;

        foreach ($list as $item) {           //$datarow = new DataRow($this->id,$list[0]);
            $datarow = new DataRow($this->id, $item, $i++, $i - 1 + $this->pagesize * ($this->currentpage - 1));
            $this->add($datarow);
            if ($this->rowevent instanceof Event) {
                $this->rowevent->onEvent($datarow); //вызов  обработчика добавляющего  данные  или   елементы  в  строку
            }
            $datarow->updateChildId();
            if ($this->selectedRow instanceof DataRow) {
                if ($datarow->getAllNumber() == $this->selectedRow->getAllNumber() && $this->selectedclass != "") {
                    $datarow->setAttribute('class', $this->selectedclass);
                }
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
    public final function RenderImpl() {
        // $this->UpdateData() ;


        $rowtag = pq('[zippy=' . $this->id . ']');

        if ($rowtag->size() == 0) {
            throw new \Zippy\Exception(sprintf(ERROR_MARKUP_NOTFOUND, $this->id));
        }

        $ids = array();
        $children = pq('[zippy=' . $this->id . ']  [zippy]');
            foreach ($children as $child) {
                $ids[] = pq($child)->attr("zippy");
            }           
        
        $c = $this->getPageRowsCount();
        $html_ = $rowtag->htmlOuter();
        $html = "";
        for ($i = 1; $i <= $c; $i++) {
            $id = $this->id .'_'.$i;
            
            $rep = "zippy=\"{$id}\" id=\"{$id}\" ";
            $h = str_replace("zippy=\"{$this->id}\"",$rep,$html_) ;
            $h = str_replace("zippy='{$this->id}'",$rep,$h) ;
            
            foreach($ids as $cid){
               $id = $cid .'_'.$i;
               $rep = "zippy=\"{$id}\" id=\"{$id}\" ";
               $h = str_replace("zippy=\"{$cid}\"",$rep,$h) ;
               $h = str_replace("zippy='{$cid}'",$rep,$h) ;
             
            }
            if ($this->cellclickevent instanceof \Zippy\Event) {
                $url = $this->getURLNode() . ':' . ($i  );
                $onclick = "window.location='{$url}'";
           
                $style = "cursor:pointer; ";
                $h = str_replace("<tr","<tr style=\"{$style}\" onclick=\"{$onclick}\" ",$h) ;
            }        
            $html .= $h;
              
        }
        $rowtag->replaceWith($html);
         
        
        /*
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

            if ($this->cellclickevent instanceof \Zippy\Event) {
                $url = $this->getURLNode() . ':' . ($i - 1);
                $onclick = "window.location='{$url}'";
                $onclick = "  " . $onclick . "  ";
                $style = "  cursor:pointer; ";
                $row->setAttribute('style', $style);
                $row->setAttribute('onclick', $onclick);
                pq($row)->attr("style", $style);
                pq($row)->attr("onclick", $onclick);
            }

        }
        
        */
        $p = $this->getPageOwner() ;
        if($p instanceof \Zippy\Html\WebPage) {
            $p->updateTag() ;
   
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
     * @param mixed  Выделяемая строка
     */
    public function setSelectedRow(DataRow $row = null) {

        $this->selectedRow = $row;

    }

    /**
     * Возвращает  номер  выделеной  строки
     *
     */
    public function getSelectedRow() {
        return $this->selectedRow;
    }

    /**
     * Устанавливает CSS  класс  для   выбранной   строки
     *
     * @param mixed $selectedclass
     */
    public function setSelectedClass($selectedclass) {
        $this->selectedclass = $selectedclass;
    }

    public final function setCellClickEvent(\Zippy\Interfaces\EventReceiver $receiver, $handler) {
        $this->cellclickevent = new \Zippy\Event($receiver, $handler);
    }

    public final function RequestHandle() {
        parent::RequestHandle();

        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        if ($this->cellclickevent instanceof \Zippy\Event) {


            $srow = null;
            foreach ($this->getDataRows() as $row) {
                if ($row->getNumber() == $p[0]) {
                    $srow = $row;
                }
            }

            $this->cellclickevent->onEvent($srow );

        }

    }
}

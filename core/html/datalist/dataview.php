<?php

namespace Zippy\Html\DataList;

use Zippy\Html\HtmlComponent;
use Zippy\Interfaces\EventReceiver;
use Zippy\Event;
use Zippy\WebApplication;

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
    private $lastnumber = 0;

    /**
     * Конструктор
     * @param mixed $id ID  компонента
     * @param mixed  $DataSource Провадер  данных для  таблицы
     * @param EventReceiver  $receiver Объект
     * @param mixed  $handler Обработчик
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
        $this->lastnumber++;

        foreach ($list as $item) {           //$datarow = new DataRow($this->id,$list[0]);
            $datarow = new DataRow($this->id, $item, $this->lastnumber++);
            $this->add($datarow);
            if ($this->rowevent instanceof Event) {
                $this->rowevent->onEvent($datarow); //вызов  обработчика добавляющего  данные  или   елементы  в  строку
            }
            $datarow->updateChildId();
            if ($this->selectedRow instanceof DataRow) {
                if ($datarow->getNumber() == $this->selectedRow->getNumber() && $this->selectedclass != "") {
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
    final public function RenderImpl() {
        // $this->UpdateData() ;

              
        $rowtag = WebApplication::$dom->find('[zippy=' . $this->id . ']');

        if ($rowtag->count() == 0) {
            throw new \Zippy\Exception(sprintf(ERROR_MARKUP_NOTFOUND, $this->id));
        }

        $ids = array();
        $children = WebApplication::$dom->find('[zippy=' . $this->id . ']  [zippy]');
        foreach ($children as $child) {
            $ids[] = $child->attr("zippy");
        }

        $rows = $this->getDataRows();
        $rowtag= $rowtag->first() ;
        $tn= $rowtag->tagName;
        $attr="";
        foreach($rowtag->attributes as $a){
          $attr = $attr. " {$a->nodeName}=\"{$a->nodeValue}\" ";
        };        
        
        $html_ = "<{$tn} {$attr}  >".    $rowtag->html() . "</{$tn}>";
        $html = "";
        foreach ($rows as $row) {
            $i = $row->getNumber() ;
            $id = $this->id .'_'.$i;

            $rep = "zippy=\"{$id}\" id=\"{$id}\" ";
            $h = str_replace("zippy=\"{$this->id}\"", $rep, $html_) ;
            $h = str_replace("zippy='{$this->id}'", $rep, $h) ;

            foreach($ids as $cid) {
                $id = $cid .'_'.$i;
                $rep = "zippy=\"{$id}\" id=\"{$id}\" ";
                $h = str_replace("zippy=\"{$cid}\"", $rep, $h) ;
                $h = str_replace("zippy='{$cid}'", $rep, $h) ;

            }
            if ($this->cellclickevent instanceof \Zippy\Event) {
                $url = $this->getURLNode() . ':' . ($i);
                $onclick = "window.location='{$url}'";

                $style = "cursor:pointer; ";
                $h = str_replace("<tr", "<tr style=\"{$style}\" onclick=\"{$onclick}\" ", $h) ;
            }
            $html .= $h;

        }
//        $rowtag->substituteWith($html);

        $parent=$rowtag->parent();
        $rowtag->precede($html);

        $rowtag->destroy();
//        $parent->appendWith($html);
   
        $p = $this->getPageOwner() ;
        if($p instanceof \Zippy\Html\WebPage) {
            // $p->updateTag() ;

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
     * @param DataRow $row Выделяемая строка
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

    final public function setCellClickEvent(\Zippy\Interfaces\EventReceiver $receiver, $handler) {
        $this->cellclickevent = new \Zippy\Event($receiver, $handler);
    }

    final public function RequestHandle() {
        parent::RequestHandle();

        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        if ($this->cellclickevent instanceof \Zippy\Event) {


            $srow = null;
            foreach ($this->getDataRows() as $row) {
                if ($row->getNumber() == $p[0]) {
                    $srow = $row;
                }
            }

            $this->cellclickevent->onEvent($srow);

        }

    }
}

<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Requestable;
use \Zippy\WebApplication;

/**
 * Класс  вывода  табличных данных. Использует  собственное  формирование строк  и  столбцов
 * по  массиву  строк данных.
 * Предназначен  для  тэга TABLE. Может  автоматически  формировать  **пагинатор  и  сортировку.
 */
class DataTable extends AbstractList implements Requestable
{

    private $columns = array();
    private $datalist = array();
    private $cellevent = null;
    private $cellclickevent = null;
    private $selectedrow = 0;
    private $selectedclass = "";
    private $maxbuttons = 10;
    private $firstButton = 1;
    private $header = true, $paginator = false, $useajax = false;

    public function __construct($id, $DataSource, $header = true, $paginator = false, $useajax = false)
    {
        AbstractList::__construct($id, $DataSource);
        $this->header = $header;
        $this->paginator = $paginator;
        $this->useajax = $useajax;
    }

    /**
     * @see HtmlComponent
     */
    public final function RenderImpl()
    {
        $tag = $this->getTag('table');

        pq($tag)->append($this->renderHeader());
        pq($tag)->append($this->renderData());
        pq($tag)->append($this->renderFooter());
    }

    /**
     * Добавляет  столбец  к  таблице.
     */
    public final function AddColumn(Column $column)
    {
        $this->columns[$column->fieldname] = $column;
    }

    /**
     * Обновляет  данные  с  провайдера
     */
    public function Reload($resetpage = true)
    {
        parent::Reload($resetpage);

        $this->datalist = $this->getItems();
    }

    /**
     * @see  Requestable
     */
    public final function RequestHandle()
    {
        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        if ($p[0] == 'sort') {
            $this->sortf = $p[1];
            $this->sortd = $p[2];
            $this->currentpage = 1;
            $this->Reload();
        }
        if ($p[0] == 'pag') {
            $this->currentpage = $p[1];
            $this->Reload(false);
        }
        if ($p[0] == 'cellclick' && $this->cellclickevent instanceof \Zippy\Event) {
            $items = array_values($this->datalist);
            $this->cellclickevent->onEvent($this, array('dataitem' => $items[$p[2] - 1], 'field' => $p[1], 'rownumber' => $p[2]));
            $this->setSelectedrow($p[2]);
        }

        if ($this->useajax) {
            WebApplication::$app->getResponse()->addAjaxResponse($this->AjaxAnswer());
        }
    }

    /**
     * Устанавливает  обработчик на  событие  прорисовки  ячейки.
     */
    public final function setCellDrawEvent(\Zippy\Interfaces\EventReceiver $receiver, $handler)
    {
        $this->cellevent = new \Zippy\Event($receiver, $handler);
    }

    /**
     * Устанавливает  обработчик на  click ячейки.
     */
    public final function setCellClickEvent(\Zippy\Interfaces\EventReceiver $receiver, $handler)
    {
        $this->cellclickevent = new \Zippy\Event($receiver, $handler);
    }

    /**
     * Формирование  заголовка  таблицы
     */
    private function renderHeader()
    {
        if (!$this->header) {
            return "";
        }

        $row = "<tr  >";

        foreach ($this->columns as $column) {

            if (!$column->visible) {
                continue;
            }

            $css = strlen($column->headerclass) > 0 ? "class=\"{$column->headerclass}\"" : "";



            if ($column->sortable) {
                $sort = "";
                if ($column->fieldname === $this->sortf) {
                    if ($this->sortd === 'asc') {
                        $sort = 'fa fa-sort-asc';
                    } else {
                        $sort = 'fa fa-sort-desc';
                    }
                }
                $url = $this->getURLNode() . ':sort:' . $column->fieldname . ':' . ($this->sortd === 'asc' ? 'desc' : 'asc');
                $onclick = "window.location='{$url}'";
                if ($this->useajax) {
                    $onclick = "getUpdate('{$url}&ajax=true');event.returnValue=false; return false;";
                }
                $row .= ( "<th   {$css} style=\"white-space: nowrap;cursor:pointer;\" onclick=\"{$onclick}\" ><span>{$column->title}</span> <i class=\"{$sort}\"></i></th>");
            } else {

                $row .= ( "<th   {$css} ><span>{$column->title}</span></th>");
            }
        }
        $row .="</tr>";
        return $row;
    }

    /**
     * Прорисовка  строк  с  данными.
     */
    private function renderData()
    {

        if (count($this->datalist) == 0) {
            return ""; //"<tr  ><td  align=\"center\" colspan=\"" . count($this->columns) . "\" >" . MSG_DATATABLE_NODATA . "</td></tr>";
        }
        $rownumber = 0;
        $rows = "";
        foreach ($this->datalist as $item) {     //цикл  по  строкам
            $rownumber++;

            if ($this->selectedrow == $rownumber && $this->selectedclass != "") {
                $row = "<tr class=\"{$this->selectedclass}\" >";
            } else {
                $row = "<tr  >";
            }


            foreach ($this->columns as $fieldname => $column) {       //цикл  по  полям
                if (!$column->visible) {
                    continue;
                }

                $data = strlen($item->{$fieldname}) > 0 ? $item->{$fieldname} : $column->defaultdata;
                $css = strlen($column->rowclass) > 0 ? "class=\"{$column->rowclass}\"" : "";
                $onclick = "";
                $style = "";


                $url = $this->getURLNode() . ':cellclick:' . $fieldname . ':' . $rownumber;
                if ($column->clickable) {

                    $onclick = "window.location='{$url}'";
                }
                if ($column->clickable && $this->useajax) {
                    $onclick = "getUpdate('{$url}&ajax=true');event.returnValue=false; return false;";
                }

                if (strlen($onclick) > 0) {
                    $onclick = " onclick=\"" . $onclick . "\" ";
                    $style = "style=\"cursor:pointer;\"";
                }


                if ($this->cellevent instanceof \Zippy\Event) {
                    $userdata = $this->cellevent->onEvent($this, array('dataitem' => $item, 'field' => $fieldname, 'rownumber' => $rownumber));
                    if ($userdata !== null && $userdata !== false) {
                        $data = $userdata;
                    }
                }

                $row .= ( "<td  {$css} {$onclick} {$style}>{$data}</td>");
            }
            $row .="</tr>";
            $rows .= $row;
        }
        return $rows;
    }

    /**
     * Прорисовывает  строку  с  пагинатором.
     */
    private function renderFooter()
    {
        if (!$this->paginator) {
            return "";
        }
        if (count($this->columns) == 0) {
            return "";
        }
        $currentpage = $this->currentpage;

        $content = '<ul class="pagination">';
        $pages = $this->getPageCount();
        if ($pages <= 1)
            return '';

        if ($currentpage - $this->firstButton > $this->maxbuttons) {

            $this->firstButton = $currentpage - $this->maxbuttons;
        }
        if ($currentpage < $this->firstButton) {
            $this->firstButton = $currentpage - 1;
        }

        if ($this->firstButton > 1) {
            $content .= "<li  class=\"page-item\"><a   class=\"page-link\"  href='void(0);' onclick=\"" . $this->getPaginatorLink(1) . "\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
            $content .= "<li class=\"page-item\"><a   class=\"page-link\"  href='void(0);' onclick=\"" . $this->getPaginatorLink($currentpage - 1) . "\"><span aria-hidden=\"true\">&lsaquo;</span></a></li>";
            $content .= "<li  class=\"page-item\"><a   class=\"page-link\" href=\"javascript:void(0);\" >&hellip;</a></li>";
        }



        for ($i = $this->firstButton; $i <= $this->firstButton + $this->maxbuttons; $i++) {
            if ($i > $pages)
                break;
            if ($currentpage == $i) {
                $content .= "<li class=\"page-item active\"><a  class=\"page-link\"  href=\"javascript:void(0);\" > {$i} </a></li>";
            } else {
                $content .= "<li  class=\"page-item\"><a   class=\"page-link\" href=\"javascript:void(0);\"  onclick=\"" . $this->getPaginatorLink($i) . "\"> {$i} </a></li>";
            }
        }

        if ($pages > $this->firstButton + $this->maxbuttons) {
            $content .= "<li  class=\"page-item\" ><a   class=\"page-link\" href=\"javascript:void(0);\" >&hellip;</a></li>";
            $content .= "<li  class=\"page-item\"><a  class=\"page-link\" href='void(0);' onclick=\"" . $this->getPaginatorLink($currentpage + 1) . "\"aria-label=\"Next\">       <span aria-hidden=\"true\">&rsaquo;</span></a></li>";
            $content .= "<li  class=\"page-item\"><a  class=\"page-link\" href='void(0);' onclick=\"" . $this->getPaginatorLink($pages) . "\"aria-label=\"Next\">       <span aria-hidden=\"true\">&raquo;</span></a></li>";
        }
        
        $countall = $this->getAllRowsCount();
        $show =  $currentpage * $this->pagesize;
        if($pages ==$currentpage) $show = $countall;
        if($countall <= $this->pagesize) $show = $countall;
        
        $content = "<table  ><tr><td valign='middle'>{$show} строк с  {$countall} &nbsp;&nbsp;&nbsp;&nbsp;</td><td align='right'> {$content}</td></tr></table>";
        return "<tr ><td class=\"footercell\"  colspan=\"" . count($this->columns) . "\" >{$content}</ul></td></tr>";
    }

    /**
     * Возвращает  ссылку  для  пагинатора.
     */
    private function getPaginatorLink($pageno)
    {
        $url = $this->getURLNode() . ':pag:' . $pageno;
        if ($this->useajax) {
            $onclick = "getUpdate('{$url}&ajax=true');event.returnValue=false; return false;";
        } else {
            $onclick = "window.location='{$url}';event.returnValue=false; return false;";
        }
        return $onclick;
    }

    /**
     *  Устанавливает  выделеную строку.
     * Строка выделяется  добавлением CSS класса  заданного
     * методом setSelectedClass
     *
     * @param mixed $id   ID  выделяемой строки
     */
    public function setSelectedrow($number)
    {
        $this->selectedrow = $number;
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

    /**
     * Удаляет  все столбцы
     *
     */
    public function removeAllColumns()
    {
        $this->columns = array();
    }

}

// класс  параметров  столбца
class Column
{

    public function __construct($fieldname, $title, $sortable = false, $visible = true, $clickable = false, $headerclass = "", $rowclass = "", $defaultdata = "")
    {
        $this->fieldname = $fieldname;
        $this->visible = $visible;
        $this->sortable = $sortable;
        $this->clickable = $clickable;
        $this->title = $title;
        $this->headerclass = $headerclass;
        $this->defaultdata = $defaultdata;
        $this->rowclass = $rowclass;
    }

    public $visible = true;
    public $sortable = false;
    public $clickable = false;
    public $fieldname = ""; //  имя свойства  выводимого итема
    public $title = ""; //заголовок
    public $headerclass = ""; // css класс  заголовка
    public $rowclass = ""; // css класс  ячейки
    public $defaultdata = " "; //значение  по  умолчанию

}

/**
* @todo  вычисляемое   поле  ???
* @todo  ссылку
* @todo  изображение
*
*/


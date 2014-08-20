<?php

namespace Zippy\Html\DataList;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\AjaxRender;
use \Zippy\WebApplication;

/**
 * Класс  вывода  табличных данных. Использует  собственное  формирование строк  и  столбцов
 * по  массиву  строк данных.
 * Предназначен  для  тэга TABLE. Может  автоматически  формировать  пагинатор  и  сортировку.
 */
class DataTable extends AbstractList implements Requestable, AjaxRender
{

    private $columns = array();
    private $datalist = array();
    private $cellevent = null;
    private $cellclickevent = null;
    private $selectedrow = 0;
    private $selectedclass = "";

    public function __construct($id, $DataSource, $options = array())
    {
        AbstractList::__construct($id, $DataSource);
        $this->setOptions(array_merge(array('header' => true, 'ajax' => false, 'paginator' => false), $options));
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
     * @param type $field Имя  поля
     * @param type $caption  Заголовок
     * @param type $options 
     */
    public final function AddColumn($field, $caption, $options = array())
    {
        $this->columns[$field] = array("caption" => $caption, "enabled" => true, "options" => array_merge(array("defaultvalue" => " "), $options));
    }

    /**
     * Обновляет  данные  с  провайдера
     */
    public function Reload()
    {
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
        }
        if ($p[0] == 'pag') {
            $this->currentpage = $p[1];
        }
        if ($p[0] == 'cellclick' && $this->cellclickevent instanceof \Zippy\Event) {
            $items = array_values($this->datalist);
            $this->cellclickevent->onEvent($this, array('dataitem' => $items[$p[2] - 1], 'field' => $p[1], 'rownumber' => $p[2]));
        }
        $this->Reload();
        if ($this->options['ajax'] == true) {
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
        if ($this->options['header'] !== true) {
            return "";
        }

        $row = "<tr  >";

        foreach ($this->columns as $fieldname => $column) {

            if ($column['enabled'] != true) {
                continue;
            }
            $width = isset($column['options']['width']) ? " width=\"{$column['options']['width']}\" " : '';

            $sort = "";

            if (isset($column['options']['sorted']) && $column['options']['sorted'] == true) {
                $sort = 'nosort';
                if ($fieldname === $this->sortf) {
                    if ($this->sortd === 'asc') {
                        $sort = 'asc';
                    } else {
                        $sort = 'desc';
                    }
                }
                $url = $this->getURLNode() . ':sort:' . $fieldname . ':' . ($sort === 'asc' ? 'desc' : 'asc');
                $onclick = "window.location='{$url}'";
                if ($this->options['ajax'] == true) {
                    $onclick = "getUpdate('{$url}&ajax=true');event.returnValue=false; return false;";
                }
                $row .= ( "<th class=\"headercell\" {$width} style=\"cursor:pointer;\" onclick=\"{$onclick}\" ><span>{$column['caption']}</span> <span class=\"{$sort}\">&nbsp;&nbsp;&nbsp;</span></th>");
            } else {

                $row .= ( "<th class=\"headercell\" {$width} ><span>{$column['caption']}</span></th>");
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
            return "<tr  ><td  align=\"center\" colspan=\"" . count($this->columns) . "\" >" . MSG_DATATABLE_NODATA . "</td></tr>";
        }
        $rownumber = 0;
        $rows = "";
        foreach ($this->datalist as $item) {     //цикл  по  строкам
            $rownumber++;
            $oddeven = ($rownumber % 2) == 1 ? 'odd' : 'even';
            if ($this->selectedrow == $rownumber && $this->selectedclass != "") {
                $row = "<tr class=\"{$this->selectedclass}\" >";
            } else {
                $row = "<tr  >";
            }


            foreach ($this->columns as $fieldname => $column) {       //цикл  по  полям
                if ($column['enabled'] != true) {
                    continue;
                }

                $data = strlen($item->{$fieldname}) > 0 ? $item->{$fieldname} : $column['options']['defaultvalue'];
                $align = isset($column['options']['align']) ? " align=\"{$column['options']['align']}\" " : '';
                $onclick = "";
                $url = $this->getURLNode() . ':cellclick:' . $fieldname . ':' . $rownumber;
                if ($column['options']['click'] === true) {

                    $onclick = "window.location='{$url}'";
                }
                if ($column['options']['ajaxclick'] === true) {
                    $onclick = "getUpdate('{$url}&ajax=true');event.returnValue=false; return false;";
                }

                if (strlen($onclick) > 0) {
                    $onclick = " onclick=\"" . $onclick . "\" ";
                    if (isset($column['options']['style'])) {
                        $column['options']['style'] .= "cursor:pointer;";
                    } else {
                        $column['options']['style'] = "cursor:pointer;";
                    }
                }
                $style = isset($column['options']['style']) ? " style=\"{$column['options']['style']}\" " : '';

                if ($this->cellevent instanceof \Zippy\Event) {
                    $userdata = $this->cellevent->onEvent($this, array('dataitem' => $item, 'field' => $fieldname, 'rownumber' => $rownumber));
                    if ($userdata !== null && $userdata !== false) {
                        $data = $userdata;
                    }
                }

                $row .= ( "<td  {$align} {$onclick} {$style}>{$data}</td>");
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
        if ($this->options['paginator'] !== true) {
            return "";
        }

        $content = "";
        $pages = $this->getPageCount();
        if (count($pages) <= 1)
            return '';
        if ($this->currentpage > 1) {
            $content .= "<span class=\"paginator-first\" onclick=\"" . $this->getPaginatorLink(1) . "\">&nbsp;&nbsp;&nbsp;</span>";
        }

        if ($this->currentpage > 1) {
            $content .= "<span class=\"paginator-prev\" onclick=\"" . $this->getPaginatorLink($this->currentpage - 1) . "\">&nbsp;&nbsp;&nbsp;</span>";
        }

        for ($i = 1; $i <= $pages; $i++) {
            if ($this->currentpage == $i) {
                $content .= " {$i} ";
            } else {
                $content .= " <a style=\"text-decoration:none\"  href=\"javascript:void(0);\" onclick=\"" . $this->getPaginatorLink($i) . "\">{$i}</a> ";
            }
        }

        if ($this->currentpage < $pages) {
            $content .= "<span class=\"paginator-next\" onclick=\"" . $this->getPaginatorLink($this->currentpage + 1) . "\">&nbsp;&nbsp;&nbsp;</span>";
        }

        if ($this->currentpage < $pages) {
            $content .= "<span class=\"paginator-last\" onclick=\"" . $this->getPaginatorLink($pages) . "\">&nbsp;&nbsp;&nbsp;</span>";
        }

        return "<tr ><td class=\"footercell\" align=\"center\" colspan=\"" . count($this->columns) . "\" >{$content}</td></tr>";
    }

    /**
     * Возвращает  ссылку  для  пагинатора.
     */
    private function getPaginatorLink($pageno)
    {
        $url = $this->getURLNode() . ':pag:' . $pageno;
        if ($this->options['ajax'] == true) {
            $onclick = "getUpdate('{$url}&ajax=true');event.returnValue=false; return false;";
        } else {
            $onclick = "window.location='{$url}';event.returnValue=false; return false;";
        }
        return $onclick;
    }

    /**
     * @see AjaxRender
     */
    public function AjaxAnswer()
    {
        $content = $this->renderHeader() . $this->renderData() . $this->renderFooter();

        $content = addslashes($content);
        $js = "$('table[zippy={$this->id}]').html('{$content}');";

        return $js;
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

}

/**
* @todo  вычисляемое   поле  ???
* @todo  ссылку
* @todo  изображение
* 
*/


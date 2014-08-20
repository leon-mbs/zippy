<?php

namespace ZCL\RepGen;

/**
 * Класс-генератор  отчетов и печатных  форм  документов  
 */
class RepGen
{

    private $template;
    private $headerData = array();

    /**
     * Конструктор
     * 
     * @param mixed $template - путь  к   файлу  шаблона
     * @param mixed $headerData   Данные для "шапки"  отчета
     * @return RepGen
     */
    public function __construct($template, array $headerData)
    {
        $this->template = $template;
        $this->headerData = $headerData;
    }

    /**
     * Генерация простого отчета
     * 
     * @param array $ds  Массив строк  данных (ассоциативных массивов)
     * @param array $detailsSummaryFields  Список  полей в  итогах,  для  которых производится суммирование  о по  соответсвующим колонкам
     */
    public function generateSimple(array $ds, $detailsSummaryFields = array())
    {
        $content = @file_get_contents($this->template);
        if (strlen($content) == 0) {
            return "";
        }
        foreach ($this->headerData as $field => $data) {
            $content = str_replace("{{$field}}", $data, $content);
        }
        $doc = \phpQuery::newDocumentHTML($content);

        $summary = array();
        $_details = "";
        $Tag = pq('[band="detail_row"]');


        if (count($ds) > 0) {


            $html = $Tag->htmlOuter();
            foreach ($ds as $data) {
                $row = $html;
                foreach ($data as $field => $value) {
                    $row = str_replace("{" . $field . "}", $value, $row);
                    foreach ($detailsSummaryFields as $sname) {
                        if ($sname == $field) {
                            $summary[$sname] = $summary[$sname] + $value;
                        }
                    }
                }
                $_details .= $row;
            }
        }
        $Tag->replaceWith($_details);
        //выводим   итоги если   есть
        $Tag = pq('[band="detail_summary"]');
        $html = $Tag->htmlOuter();
        foreach ($summary as $field => $data) {
            $html = str_replace("{{$field}}", $data, $html);
        }
        $Tag->replaceWith($html);

        $html = pq('body')->html();
        //очищаем  незаполненные   поля

        foreach ($ds as $field => $data) {
            $html = str_replace("{{$field}}", "", $html);
        }
        return $html;
    }

    /**
     * Генерация отчета   с  группирвками
     * 
     * @param array $ds  Массив строк  данных (ассоциативных массивов)
     * @param array $detailsSummaryFields  Список  полей в  итогах,  для  которых производится суммирование  о по  соответсвующим колонкам
     * @param array $groupFileds          Поля группировки
     * @param array $groupSummaryFields   Поля промежуточных  итогов  по  группам
     */
    public function generateGroups(array $ds, $groupFields, $detailsSummaryFields = null, $groupSummaryFields = null)
    {
        $content = @file_get_contents($this->template);
        if (strlen($content) == 0) {
            return "";
        }
        foreach ($this->headerData as $field => $data) {
            $content = str_replace("{{$field}}", $data, $content);
        }
        $doc = \phpQuery::newDocumentHTML($content);
        $summary = array();
        $summarygrp = array();
        $group_data = array();

        if (count($ds) > 0) {
            //если  есть  группировка
            if (is_array($groupFields) && count($groupFields) > 0) { //  grouping
                foreach ($ds as $data) {
                    $_key = "";
                    foreach ($groupFields as $name) {
                        $_key .= $data[$name];    // формируем  уникальный ключ  группы
                    }
                    if (!isset($group_data[$_key])) {
                        $group_data[$_key]['__summary'] = array();
                        $group_data[$_key]['__header'] = array();
                        $group_data[$_key]['__rows'] = array();

                        foreach ($groupSummaryFields as $sname) {
                            $group_data[$_key]['__summary'][$sname] = 0;
                        }
                        foreach ($groupFields as $sname) {
                            $group_data[$_key]['__header'][$sname] = $data[$sname];
                        }
                    }

                    //присваивам  строку  своей  группе
                    $group_data[$_key]['__rows'][] = $data;





                    foreach ($data as $field => $value) {
                        //вычисление  итоговых  полей  по  всему  отчту
                        foreach ($detailsSummaryFields as $sname) {
                            if ($sname == $field) {
                                $summary[$sname] = $summary[$sname] + $value;
                            }
                        }
                        //вычисление  итоговых  полей  по  группе
                        foreach ($groupSummaryFields as $sname) {
                            if ($sname == $field) {
                                $group_data[$_key]['__summary'][$sname] = $group_data[$_key]['__summary'][$sname] + $value;
                            }
                        }
                    }
                }

                $detailrowtag = pq('[band="detail_row"]')->htmlOuter();
                $groupheadertag = pq('[band="group_header"]')->htmlOuter();
                $groupsummarytag = pq('[band="group_summary"]')->htmlOuter();
                $detailummarytag = pq('[band="detail_summary"]')->htmlOuter();

                //цикл  по  группам
                foreach ($group_data as $_key => $group) {

                    //заголовок  группы
                    $_groupheadertag = $groupheadertag;
                    foreach ($group['__header'] as $field => $data) {
                        $_groupheadertag = str_replace("{{$field}}", $data, $_groupheadertag);
                    }

                    $html .= $_groupheadertag;



                    //детализация
                    foreach ($group['__rows'] as $row) {
                        $_detailrowtag = $detailrowtag;
                        foreach ($row as $field => $data) {
                            $_detailrowtag = str_replace("{{$field}}", $data, $_detailrowtag);
                        }
                        $html .= $_detailrowtag;
                    }




                    //итоги  группы
                    $_groupsummarytag = $groupsummarytag;
                    foreach ($group['__summary'] as $field => $data) {
                        $_groupsummarytag = str_replace("{{$field}}", $data, $_groupsummarytag);
                    }
                    $html .= $_groupsummarytag;
                }


                foreach ($summary as $field => $data) {
                    $detailummarytag = str_replace("{{$field}}", $data, $detailummarytag);
                }
                $html .= $detailummarytag;

                pq('[band="detail_row"]')->remove();
                pq('[band="detail_header"]')->remove();
                pq('[band="group_header"]')->remove();
                pq('[band="group_summary"]')->remove();

                pq('[band="detail_summary"]')->replaceWith($html);
            }
        }
        $html = pq('body')->html();
        //очищаем  незаполненрые   поля
        foreach ($ds as $field => $data) {
            $html = str_replace("{{$field}}", "", $html);
        }

        return $html;
    }

    /**
     * Генерация   кросс-таблиц
     * 
     * @param mixed $detail двумерный массив с детализацией
     * @param mixed $left вертикальный заголовок
     * @param mixed $top  горизонтальный заголовок
     * @param mixed $right  вертикальные итоги
     * @param mixed $bottom горизрнтальные итоги
     */
    public function generatePivot(array $detail, array $left, array $top, array $right, array $bottom)
    {
        $content = @file_get_contents($this->template);
        if (strlen($content) == 0) {
            return "";
        }
        foreach ($this->headerData as $field => $data) {
            $content = str_replace("{{$field}}", $data, $content);
        }
        $doc = \phpQuery::newDocumentHTML($content);

        $Tag = pq('[band="top"]');
        $html = $Tag->htmlOuter();
        $_top = "";

        foreach ($top as $item) {

            $_top .= str_replace("{acc_c}", $item, $html);
        }
        $Tag->replaceWith($_top);


        $Tag = pq('[band="bottom"]');
        $html = $Tag->htmlOuter();

        $_bottom = "";
        foreach ($bottom as $item) {

            $_bottom .= str_replace("{c_o}", $item, $html);
        }
        $Tag->replaceWith($_bottom);

        $Tag = pq('[band="data"]');
        $datahtml = $Tag->htmlOuter();

        $Tag = pq('[band="middle"]');
        $html = $Tag->htmlOuter();
        $middle = "";
        for ($i = 0; $i < count($left); $i++) {
            $_html = str_replace("{d_o}", $right[$i], $html);
            $_html = str_replace("{acc_d}", $left[$i], $_html);

            $_data = "";
            for ($j = 0; $j < count($left); $j++) {
                $_data .= str_replace("{ob}", $detail[$i][$j], $datahtml);
            }
            $middle .= str_replace($datahtml, $_data, $_html);
        }
        $Tag->replaceWith($middle);

        $html = pq('body')->html();

        $html = str_replace("{acc_c}", "&nbsp;", $html);
        $html = str_replace("{acc_d}", "&nbsp;", $html);
        $html = str_replace("{c_o}", "&nbsp;", $html);
        $html = str_replace("{d_o}", "&nbsp;", $html);
        $html = str_replace("{ob}", "&nbsp;", $html);
        $html = str_replace(">0.00", "&nbsp;", $html);
        return $html;
    }

}

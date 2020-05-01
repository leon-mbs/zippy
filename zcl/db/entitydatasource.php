<?php

namespace ZCL\DB;

/**
 * Реализует  интерфейс  провайдера  дланных для  Entity
 * позволяет  автоматизировать  стандартные операции  с выборками  из  БД
 * @see  \Zippy\Interfaces\DataSource
 */
class EntityDataSource implements \Zippy\Interfaces\DataSource
{

    private $class;  //Имя  класса  унаследованного  от  Entity
    private $where;  //выражение для  WHERE
    private $order;  //выражение для  ORDER BY
    private $count;  //количество

    //   private $top;    //ограничение  количества

    public function __construct($class, $where = "", $order = "", $count = 0) {
        $this->class = $class;
        $this->where = $where;
        $this->order = $order;
        $this->count = $count;
    }

    public function getItemCount() {
        $class = $this->class;
        return $class::findCnt($this->where);
    }

    public function getItems($start = -1, $count = -1, $sortfield = null, $desc = null) {
        if ($this->count > 0) {  // приоритет если  задано
            $count = $this->count;
            $start = 0;
        }
        $orderby = strlen($sortfield) > 0 ? $sortfield . ' ' . $desc : "";

        if (strlen($this->order) > 0 && strlen($sortfield) == 0) {
            $orderby = $this->order;
        }
        $class = $this->class;
        return $class::find($this->where, $orderby, $count, $start);
    }

    public function getItem($id) {
        $class = $this->class;
        return $class::load($id);
    }

    public function setWhere($where = '') {
        $this->where = $where;
    }

    public function setOrder($order) {
        $this->order = $order;
    }

}

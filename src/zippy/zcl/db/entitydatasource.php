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

    //   private $top;    //ограничение  количества

    public function __construct($class, $where = "", $order = "")
    {
        $this->class = $class;
        $this->where = $where;
        $this->order = $order;
    }

    public function getItemCount()
    {
        $class = $this->class;
        return $class::findCnt($this->where);
    }

    public function getItems($start = -1, $count = -1, $sortfield = null, $desc = null)
    {
        if (strlen($this->order) > 0 && strlen($sortfield) == 0) {
            $sortfield = $this->order;
            $_s = explode(" ",$sortfield) ;
            if(count($_s)==2){
                $sortfield = $_s[0];
                $desc = $_s[1];
            }
            if(count($_s)>2){
                $desc ="";
            }
        }
        $class = $this->class;
        return $class::find($this->where, $sortfield, $desc, $count, $start);
    }

    public function getItem($id)
    {
        $class = $this->class;
        return $class::load($id);
    }

    public function setWhere($where='')
    {
        $this->where = $where;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

}

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
        private $top;    //ограничение  количества

        public function __construct($class, $where = "", $order = "", $top = 0)
        {
                $this->class = $class;
                $this->where = $where;
                $this->order = $order;
                $this->top = $top;
        }

        public function getItemCount()
        {
                $class = $this->class;
                return $class::findCnt();
        }

        public function getItems($start, $count, $sortfield = null, $desc = null)
        {
                $arr = array();
                $arr['limit'] = $start . ',' . $count;
                if (strlen($sortfield) > 0) {
                        $arr['order by'] = $sortfield . ' ' . $desc;
                }
                if (strlen($this->order) > 0) {
                        $arr['order by'] = $this->order;
                }
                if (strlen($this->where) > 0) {
                        $arr['where'] = $this->where;
                }
                if ($this->top > 0) {
                        $arr['limit'] = '0,' . $this->top;
                }
                $class = $this->class;
                return $class::find($arr);
        }

        public function getItem($id)
        {
                $class = $this->class;
                return $class::load($id);
        }

}


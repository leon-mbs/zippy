<?php

namespace Zippy\Html\DataList;

use Zippy\Interfaces\DataSource;
use Zippy\Interfaces\Binding;

/**
 * Класс  провайдера данных из  массива
 */
class ArrayDataSource implements DataSource
{
    private $data;

    /**
     * Консируктор
     * @return array  Массив  данных или  PropertyBinding
     */
    public function getArray() {
        if ($this->data instanceof Binding) {
            return $this->data->getValue();
        } else {
            return $this->data;
        }
    }

    /**
     * Конструктор
     *
     * @param mixed $source массив, биндинг или  ссылка  на   компонент
     * @param mixed $property имя  свойстава   если  первый  парметр - компонент
     * @return ArrayDataSource
     */
    public function __construct($source, $property = null) {
        if ($source instanceof \Zippy\Html\HtmlComponent) {
            $this->data = new \Zippy\Binding\ArrayPropertyBinding($source, $property);
        } else {
            $this->data = $source;
        }
    }

    /**
     * @see DataSource
     */
    public function getItemCount() {
        return count($this->getArray());
    }

    /**
     * @see DataSource
     */
    public function getItems($start, $count, $sortfield = null, $asc = null) {

        $list = $this->getArray();
        if ($sortfield != null) {

            uasort(
                $list,
                function ($a, $b) use ($sortfield, $asc) {
                    if ($asc == 'desc') {
                        if (is_numeric($a->{$sortfield}) && is_numeric($a->{$sortfield})) {
                            return $b->{$sortfield} > $a->{$sortfield};
                        } else {
                            return strcmp($b->{$sortfield}, $a->{$sortfield});
                        }
                    } else {
                        if (is_numeric($a->{$sortfield}) && is_numeric($b->{$sortfield})) {
                            return $a->{$sortfield} > $b->{$sortfield};
                        } else {
                            return strcmp($a->{$sortfield}, $b->{$sortfield});
                        }
                    }
                }
            );
        }
        if ($start >= 0 or $count >= 0) {
            return array_slice($list, $start, $count);
        }
        return $list;
    }

    /**
     * @see DataSource
     */
    public function getItem($id) {
        $list = $this->getArray();
        foreach ($list as $item) {
            if ($item->getID() === $id) {
                return $item;
            }
        }
        return null;
    }

    //устанавливает  массив с  данными
    public function setArray($source) {
        $this->data = $source;
    }

}

<?php

namespace Zippy\Binding;

use Zippy\Interfaces\Binding;

/**
 * Реализует привязку  данных  к  свойству  объекта.
 * При изменении  значеня поля изменяется  соответствующее  поле в  заданном  объекте и наоборот.
 * @see Binding
 */
class PropertyBinding implements Binding
{
    protected $obj = null;
    protected $propertyname = "";

    /**
     * Конструктор
     * @param mixed Объект
     * @param string  Название  свойства  объекта (должно быть public)
     *
     * class Myobj
     * {
     *    public  $myvalue
     * } ;
     *
     * $o = new Myobj() ;
     *
     *  new PropertyBinding($o,'myvalue');
     *
     */
    public function __construct($obj, $propertyname) {
        $this->obj = $obj;
        $this->propertyname = $propertyname;
    }

    public function getValue() {
        if (false) {
            if (!in_array($this->propertyname, array_keys(get_object_vars($this->obj)), false)) {
                throw new \Zippy\Exception(sprintf(ERROR_NOT_FOUND_PROPERTY_BINDING, $this->propertyname, get_class($this->obj)));
            };
        }
        return $this->obj->{$this->propertyname};
    }

    public function getPropertyName() {
        return $this->propertyname;
    }

    public function setValue($data) {
        if (false) {
            if (!in_array($this->propertyname, array_keys(get_object_vars($this->obj)), false)) {
                throw new \Zippy\Exception(sprintf(ERROR_NOT_FOUND_PROPERTY_BINDING, $this->propertyname, get_class($this->obj)));
            };
        }
        $this->obj->{$this->propertyname} = $data;
    }

}

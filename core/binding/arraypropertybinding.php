<?php

namespace Zippy\Binding;

/**
 * Используется  если  привязанное свойство  объекта - массив.
 * Добавляет  к  базовому  классу  метод  очистки  массива
 * @see PropertyBinding
 */
class ArrayPropertyBinding extends PropertyBinding
{
    /**
     *  Очистка  привязанного  массива.
     */
    public function clear() {
        $this->obj->{$this->propertyname} = array();
    }

}

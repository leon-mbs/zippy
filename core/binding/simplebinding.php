<?php

namespace Zippy\Binding;

use Zippy\Interfaces\Binding;

/**
 * Реализует  простое  связывание данных, содержит  непосредственное  значение без  реальной  привязки
 * @see Binding
 */
class SimpleBinding implements Binding
{
    private $value = "";

    /**
     * Конструктор
     * @param mixed  Значение
     */
    public function __construct($value) {
        $this->value = $value;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

}

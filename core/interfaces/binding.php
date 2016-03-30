<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  классами  привязки
 *
 */
interface Binding
{

    /**
     * Получить  данные из  поля  привязанного объекта
     */
    public function getValue();

    /**
     * Присвоить  данные   полю  привязанного  объекта
     */
    public function setValue($value);
}

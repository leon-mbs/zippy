<?php

namespace ZCL\DB;

/**
 * Расширение  \ZDB\Entity  для  работы  с  компонентами фреймворка
 */
abstract class Entity extends \ZDB\Entity implements \Zippy\Interfaces\DataItem
{


    protected $fields = array();  //список  полей

    /**
     * Конструктор
     *
     * @param mixed $row массив инициализирующий некторые
     * или  все  поля объекта
     *
     */

    function __construct($row = null)
    {
        parent::__construct($row);
    }


    /**
     * Возвращает значение  уникального  ключа  сущности
     *
     */
    public final function getID()
    {
        return parent::getKeyValue();
    }


}

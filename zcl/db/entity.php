<?php

namespace ZCL\DB;

/**
 * Базовый  класс  для  бизнес-сущностей
 * Реализует паттерн  Active Directory
 * Предназначен  для   автоматизации стандартных  над  записями  в  БД
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

<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  объектами  содержащими  данные
 * в виде объекта (структуры  с  полями)  с  уникальным  свойством
 * 
 */
interface DataItem
{

    /**
     * Возвращает  уникальный   ключ
     * @return  int  ID
     */
    public function getID();
}

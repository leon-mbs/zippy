<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  объектами  содержащими  данные
 * в виде объекта (структуры  с  полями)  с  уникальным  полем  в   виде  ключа
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

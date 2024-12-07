<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  объектами - провайлерами  данных
 *
 *
 */
interface DataSource
{
    /**
     * Возвращает  количество  елементов   в  наборе
     * @return int         *
     */
    public function getItemCount();

    /**
     * Возвращает  массив  оьъектов DataItem
     * @param mixed $start  Начальный   елемент
     * @param mixed $count Количество елементов
     * @param mixed $sortfield Количество елементов
     * @param mixed $desc Количество елементов
     * @return array         *
     */
    public function getItems($start, $count, $sortfield = null, $desc = true);

    /**
     * Возвращает объект DataItem по  уникальному  ключу
     * @param mixed  Уникальный  ключ
     * @return DataItem
     */
    //public function getItem($id);
}

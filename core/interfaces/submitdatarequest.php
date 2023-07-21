<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами, способными принимать  данные с  формы
 *
 */
interface SubmitDataRequest
{
    /**
     * Получить данные  с  HTTP запроса
     */
    public function getRequestData();

    /**
     * Очистить элемент от  данных
     */
    public function clean();
}

<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами, способными принимать  данные с  формы
 *
 */
interface SubmitDataRequest
{

    /**
     * Получить дынные  с  HTTP запроса
     */
    public function getRequestData();
}

?>

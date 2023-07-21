<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами которые  могут вернуть  через  AJAX код для
 * асинхронного обновления  на  странице.
 *
 */
interface AjaxRender
{
    /**
     * Возвращает JavaScript код  для   рендеринга на клиенте
     * @return string
     */
    public function AjaxAnswer();
}

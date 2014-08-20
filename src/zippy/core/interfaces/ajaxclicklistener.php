<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами  которые  при  клике  мышкой вызывают  серверный  обработчик
 * с  помощью  AJAX
 *
 */
interface AjaxClickListener
{

    /**
     * Устанавливает обработчик  события
     * @param  mixed  Объект
     * @param  string Имя  метода - обработчика
     */
    public function setAjaxClickHandler(EventReceiver $receiver, $handler);
}

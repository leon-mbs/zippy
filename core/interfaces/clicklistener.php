<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами  вызывающими  серверный  обрабочик  события  при  клике  мышкой
 *
 */
interface ClickListener
{
    /**
     * Устанавливает обработчик  события
     * @param EventReceiver  $receiver
     * @param string $handler Имя  метода - обработчика
     * @param   mixed $ajax  обработчик  будет  вызван асинхронно
     */
    public function onClick(EventReceiver $receiver, $handler, $ajax = false);
}

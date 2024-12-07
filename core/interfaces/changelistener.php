<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами  вызывающими  серверный  обрабочик  события  при изменении  их  значения
 *
 */
interface ChangeListener
{
    /**
     * Устанавливает обработчик  события
     * @param EventReceiver $receiver Объект
     * @param string $handler Имя  метода - обработчика
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = true);
}

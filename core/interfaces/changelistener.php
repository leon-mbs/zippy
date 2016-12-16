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
     * @param  mixed  Объект
     * @param  string Имя  метода - обработчика
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = true);
}

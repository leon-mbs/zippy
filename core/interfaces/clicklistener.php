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
     * @param mixed  Объект
     * @param string Имя  метода - обработчика
     * @ajax   bool   обработчик  будет  вызван асинхронно
     */
    public function onClick(EventReceiver $receiver, $handler, $ajax = false);
}

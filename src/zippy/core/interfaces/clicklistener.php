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
     * @param  mixed  Объект
     * @param  string Имя  метода - обработчика
     */
    public function setClickHandler(EventReceiver $receiver, $handler);

    /**
     * Вызывает  срабатывание  обработчика
     */
    // public function OnClick();
}



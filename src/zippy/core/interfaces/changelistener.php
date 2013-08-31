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
        public function setChangeHandler(EventReceiver $receiver, $handler);

        /**
         * Вызывает  срабатывание  обработчика
         */
        public function OnChange();
}


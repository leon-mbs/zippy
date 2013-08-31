<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами  которые  при  изменения  значения   вызывают  серверный  обработчик
 * с  помощью  AJAX
 *
 */
interface AjaxChangeListener
{

        /**
         * Устанавливает обработчик  события
         * @param  mixed  Объект
         * @param  string Имя  метода - обработчика
         */
        public function setAjaxChangeHandler(EventReceiver $receiver, $handler);
}


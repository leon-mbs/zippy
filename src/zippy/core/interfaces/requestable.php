<?php

namespace Zippy\Interfaces;

/**
 * Реализуется  компонентами, способными обрабатывать  HTTP запрос
 *
 */
interface Requestable
{

        /**
         * Вызывется  при  перенаправлении  компоненту  HTTP запроса
         */
        public function RequestHandle();
}


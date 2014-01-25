<?php

namespace Zippy;

use \Zippy\Interfaces\EventReceiver;
use \Zippy\Html\HtmlComponent;

/**
 * Класс инкапсулирующий  обработчик  события
 */
class Event
{

        private $handler, $receiver = null;
        public $isajax = false;  //если  true  рендерим  обработчик  для  ajax

        /**
         * Конструктор
         * @param EventReceiver Объект  метод  которого  является  обработчиком  события
         * @param string Имя  метода - обработчика
         */

        public function __construct(EventReceiver $receiver, $handler)
        {
                $this->receiver = $receiver;
                $this->handler = $handler;
        }

        /**
         * Метод, вызывающий  обработчик  события
         * @param  Объект,  инициирующий  обработку   события
         * @param array список  параметров для обработчика
         */
        public function onEvent($sender, $params = null)
        {
                /*  if (ZIPPY_DEBUG) {
                  if (!in_array($this->handler,get_class_methods($this->receiver),false)) {
                  throw new \Zippy\Exception(sprintf(ERROR_NOT_FOUND_PROPERTY_BINDING,$this->handler,get_class($this->receiver)));
                  };
                  }
                 */
                if ($this->receiver != null)
                        return $this->receiver->{$this->handler}($sender, $params);
                else
                        return $this->handler($sender, $params);
        }

}


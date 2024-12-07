<?php

namespace Zippy;

use Zippy\Interfaces\EventReceiver;
use Zippy\Html\HtmlComponent;

/**
 * Класс инкапсулирующий  обработчик  события
 */
class Event
{
    private $handler;
    private $receiver = null;
    public $isajax = false;  //если  true  рендерим  обработчик  для  ajax

    /**
     * Конструктор
     * @param EventReceiver $receiver Объект  метод  которого  является  обработчиком  события
     * @param string $handler Имя  метода - обработчика
     */

    public function __construct(EventReceiver $receiver, string $handler) {
        $this->receiver = $receiver;


        $this->handler = $handler;

    }

    /**
     * Метод, вызывающий  обработчик  события
     * @param mixed $sender Объект,  инициирующий  обработку   события
     * @param array $params список  параметров для обработчика
     */
    public function onEvent($sender, $params = null) {
        $h = $this->handler;

        if ($h != null && $this->receiver != null) {
            return $this->receiver->{$h}($sender, $params);
        } else {
            throw new  \Zippy\Exception(sprintf(ERROR_HANDLER_NOTFOUND, $sender->id)) ;
        }
    }

}

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
     * @param EventReceiver Объект  метод  которого  является  обработчиком  события
     * @param string Имя  метода - обработчика
     */

    public function __construct(EventReceiver $receiver, $handler) {
        $this->receiver = $receiver;


        $this->handler = $handler;

    }

    /**
     * Метод, вызывающий  обработчик  события
     * @param Объект,  инициирующий  обработку   события
     * @param array список  параметров для обработчика
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

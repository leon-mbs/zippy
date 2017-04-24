<?php

namespace Zippy;

use \Zippy\Interfaces\EventReceiver;
use \Zippy\Html\HtmlComponent;
use \Opis\Closure\SerialiazbleClosure;

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
        
          if($handler instanceof \Closure){
          // $handler->bindTo($receiver,"\\". get_class($receiver));
           $handler = new \Opis\Closure\SerializableClosure($handler) ;
            
        }
        $this->handler = $handler;
        
    }

    /**
     * Метод, вызывающий  обработчик  события
     * @param  Объект,  инициирующий  обработку   события
     * @param array список  параметров для обработчика
     */
    public function onEvent($sender, $params = null)
    {
        $h =$this->handler;
        if($h instanceof \Opis\Closure\SerializableClosure){
            
               return $h($sender);
        }
        if ($h != null) {
            return $this->receiver->{$h}($sender, $params);
        } else {
            return $this->handler($sender, $params);
        }
    }

}

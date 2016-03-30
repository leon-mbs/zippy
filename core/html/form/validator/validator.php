<?php

namespace Zippy\Html\Form\Validator;

use \Zippy\Binding\PropertyBinding;
use \Zippy\Html\Label;
use \Zippy\Event;

/**
 *  Базовый  класс  для   валидаторов
 */
abstract class Validator
{

    private $event = null, $value = null;
    protected $message = "";
    private $result = null;

    /**
     * Конструктор
     * @param  mixed Если  задана  привязка результат  записывается  в   поле  связанного  объекта, если компонет Label присваевается  его  тексту
     * @param mixed  Если  задано  событие - вызывается  при неверной  валидации
     * @param string  Текст  сообщения. Если  задано  выдается  при неправильной   валидации  вместо  дефолтного
     */
    public function __construct($value, Event $event, $message = '')
    {

        if ($value instanceof PropertyBinding || $value instanceof Label) {
            $this->value = $value;
        }


        if ($event instanceof Event) {
            $this->event = $event;
        }


        $this->message = $message;
    }

    /**
     * Выполняет  валидацию
     * @param mixed Валидируемый  объект
     * @param mixed  Валидируемое  значение
     */
    public final function validate($validated, $value)
    {
        $this->result = $this->check($value);
        if ($this->result !== true) {


            if ($this->value instanceof PropertyBinding) {
                $this->value->setValue($this->result);
            }
            if ($this->value instanceof Label) {
                $this->value->setText($this->result);
            }


            if ($this->event != null) {
                $this->event->onEvent($this, array($validated, $this->result));
            }
        }
    }

    /**
     * Имплементация алгоритма валидации в конкретном валидаторе
     * @param  mixed  Валидируемое  значение
     */
    protected abstract function check($value);

    /**
     * Возвращает результат валидации
     */
    public final function getValidateResult()
    {
        return $this->result;
    }

}

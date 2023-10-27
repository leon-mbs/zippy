<?php 

namespace Zippy\Html\Form;

use Zippy\Html\HtmlComponent;
use Zippy\Interfaces\SubmitDataRequest;
use Zippy\Interfaces\Binding;

/**
 * Базовый   класс для компонентов, способных  принимать данные  с  формы
 */
abstract class HtmlFormDataElement extends HtmlComponent implements SubmitDataRequest
{
    protected $value = null;

    /**
     * Устанавливает  значение данных  объекта
     * @param mixed  Объект данных или  PropertyBinding
     */
    public function setValue($value) {
        if ($this->value instanceof Binding && !($value instanceof Binding)) {
            $this->value->setValue($value);
        } else {
            $this->value = $value;
        }
    }

    /**
     * Возвращает  значение данных  объекта
     */
    public function getValue() {
        if ($this->value instanceof Binding) {
            return $this->value->getValue();
        } else {
            return $this->value;
        }
    }

}

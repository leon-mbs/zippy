<?php

namespace Zippy\Html\Form\Validator;

/**
 * Валидатор  числовых  значенй
 */
class NumericValidator extends Validator
{

    protected function check($value)
    {
        if (is_numeric($value)) {
            return true;
        } else {
            return strlen($this->message) > 0 ? $this->message : ERROR_VALIDATE_NUMERIC;
        }
    }

}

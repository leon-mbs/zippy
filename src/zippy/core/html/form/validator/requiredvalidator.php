<?php

namespace Zippy\Html\Form\Validator;

/**
 * Валидатор обязательных  значений
 */
class RequiredValidator extends Validator
{

        protected function check($value)
        {
                if (strlen($value) > 0) {
                        return true;
                } else {
                        return strlen($this->message) > 0 ? $this->message : ERROR_VALIDATE_REQUIRED;
                }
        }

}


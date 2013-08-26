<?php

namespace Zippy\Html\Form\Validator;

/**
 * Email валидатор
 */
class EmailValidator extends Validator
{

        protected function check($value)
        {
                if (preg_match(REG_EMAIL, $value)) {
                        return true;
                } else {
                        return strlen($this->message) > 0 ? $this->message : ERROR_VALIDATE_EMAIL;
                }
        }

}


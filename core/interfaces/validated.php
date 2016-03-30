<?php

namespace Zippy\Interfaces;

use \Zippy\Validator\Validator;

/**
 * Реализуется  компонентами  к  которым  можно применить  автоматическую  валидацию
 *
 * @see  Validator
 */
interface Validated
{

    /**
     * добавляет  валидатор к  объекту
     * Как  правило объект  содержит  массив   валидаторов  вызываемых  по  очереди
     */
    public function addValidator(Validator $validator);
}

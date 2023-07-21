<?php

namespace Zippy\Html;

/**
 *  Базовый компонент для создания реализаций с  пользовательским  рендерингом.
 */
abstract class CustomComponent extends HtmlComponent
{
    /**
     * @see  HtmlComponent
     */
    final public function RenderImpl() {
        $HtmlTag = $this->getTag();
        $attributes = $HtmlTag->attr('*'); //атрибуты с шаблона
        $HtmlTag->replaceWith($this->getContent($attributes));
    }

    /**
     * Метод перегружаемый в пользовательской реализации
     * Возвращает HTML содержание
     * для  работы с HTTP запросами необходимо реализовать соответствующие
     * интерфейсы типа Requestable и т.д.
     * Список   аттрибутов  HTML тэга (как правило,  DIV элемента) доступен
     * через  поле  attributes
     *
     * @return string
     */
    abstract public function getContent($attributes);
}

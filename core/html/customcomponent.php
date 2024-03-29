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
        $attributes = []; //атрибуты с шаблона
        foreach($HtmlTag->attributes as $a){
           $attributes[$a->nodeName] =$a->nodeValue;
        };        
        $html= $this->getContent($attributes);
        $HtmlTag->html($html);
     //   $HtmlTag->follow($html);
     //   $HtmlTag->destroy();
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

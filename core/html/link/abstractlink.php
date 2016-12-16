<?php

namespace Zippy\Html\Link;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Binding;

/**
 * Базовый   класс для  компонентов HTML ссылок
 *
 */
abstract class AbstractLink extends \Zippy\Html\HtmlContainer
{

    protected $value = null;
    protected $disabled = false;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->setAttribute("href", "javascript:void(0);");
    }

    /**
     * Устанавливает  текст  ссылки. В случае  тэга  <IMG> вместо  текста  задает
     * путь к  изображению (атрибут src)
     *
     * @param  string
     */
    public function setValue($text)
    {
        $this->value = $text;
        return $this;
    }

    function getValue()
    {
        if ($this->value instanceof Binding) {
            return $this->value->getValue();
        } else {
            return $this->value;
        }
    }

    /**
     * @see HtmlComponent
     */
    public function beforeRender()
    {
        $HtmlTag = $this->getTag('a');
        $children = $HtmlTag->children('img');

        if ($this->value != null && count($children) == 0) {
            $HtmlTag = $this->getTag();
            $HtmlTag->text($this->getValue());
        }
        if ($children->size() == 1 && $this->value != null) {
            $children[0]->attr('src', $this->getValue());
        }
    }

    /**
     * Устанавливает состояние disabled при котором ссылка отображается как текст
     */
    public function setDisabled($disabled = true)
    {
        $this->disabled = $disabled;
    }

    public function RenderImpl()
    {
        parent::RenderImpl();
    }

}

<?php

namespace Zippy\Html\Form;

/**
 * Выводит список  чекеров
 */
class CheckBoxList extends HtmlFormDataElement
{

    public $delimiter = " ";
    public $list = array();
    protected $event;
    public $selectedvalue;

    /**
     * Конструктор
     * @param  string ID компонента
     * @param  string Разделитель  между ссылками
     */
    public function __construct($id, $delimiter = '')
    {
        parent::__construct($id);
        $this->delimiter = $delimiter;
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        // $url = $this->owner->getURLNode()."::".$this->id ;

        $out = "";
        foreach ($this->list as $id => $item) {

            $attributes = "";
            foreach ($item['attributes'] as $key => $value) {
                $attributes = $attributes . $key . "=\"{$value}\" ";
            }
            $checked = $item['value'] === true ? ' checked="on"' : '';

            $out .= $this->RenderItem($this->id . '::' . $id, $checked, $item['caption'], $attributes, $this->delimiter);
        }
        $out = substr($out, 0, strlen($out) - strlen($this->delimiter));

        $this->getTag()->html($out);
    }

    /**
     * Рендерит чекбокс.  Мжет быть перегружен  для  кастомного рендеринга.
     * 
     * @param mixed $name
     * @param mixed $checked
     * @param mixed $caption
     * @param mixed $attr
     * @param mixed $delimiter
     */
    public function RenderItem($name, $checked, $caption = "", $attr = "", $delimiter = "")
    {
        return "<nobr><input   type=\"checkbox\" name=\"{$name}\" {$attr} {$checked}  /> {$caption}</nobr>{$delimiter}";
    }

    /**
     * Добавить  чекер в  список
     * @param mixed  Номер  чекера
     * @param boolean  Значение
     * @param string Текст возле  чекера
     * @param array Список   аттрибутов
     */
    public function AddCheckBox($itemid, $value, $caption, $attributes = array())
    {
        $this->list[$itemid] = array('value' => $value, 'caption' => $caption, 'attributes' => $attributes);
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData()
    {
        foreach ($this->list as $id => $item) {
            $this->list[$id]['value'] = isset($_REQUEST[$this->id . '::' . $id]);
        }
    }

    /**
     * Устанавливает состояние  чекера
     * @param mixed номер  в  списке
     * @param mixed Состояние
     */
    public function setChecked($id, $state)
    {
        $this->list[$id]['value'] = $state;
    }

    /**
     * Устанавливает состояние всех  чекеров  в  списке
     * @param mixed Состояние
     */
    public function setAllChecked($state)
    {
        foreach ($this->list as $id => $item) {
            $this->list[$id]['value'] = $state;
        }
    }

    /**
     * Проверка  состояния чекера
     * @param mixed  Номер  в  списке
     */
    public function isChecked($id)
    {
        return $this->list[$id]['value'];
    }

    /**
     * Список номеров  отмеченых  чекеров
     * @return  array
     */
    public function getCheckedList()
    {
        $ids = array();
        foreach ($this->list as $id => $item) {
            if ($this->list[$id]['value'] === true) {
                $ids[] = $id;
            }
        }
        return $ids;
    }

    /**
     * Очистка  массива
     */
    public function Clear()
    {
        $this->list = array();
    }

}

<?php

namespace Zippy\Html\Form;

/**
 * Выводит список  радиокнопок
 */
class RadioList extends HtmlFormDataElement
{
    public $delimiter = " ";
    public $list = array();
    protected $event;
    public $selectedvalue = -1;

    /**
     * Конструктор
     * @param string $id ID компонента
     * @param string $delimiter Разделитель  между кнопками
     */
    public function __construct($id, $delimiter = '') {
        parent::__construct($id);
        $this->delimiter = $delimiter;
    }

    /**
     * Добавить  кнопку в  список
     * @param int $value Значение
     * @param string $caption Текст возле  кнопки
     * @param array $attributes Список   аттрибутов
     */
    public function AddRadio($value, $caption, $attributes = array()) {
        $this->list[] = array('value' => $value, 'caption' => $caption, 'attributes' => $attributes);
    }

    /**
     * Устанавливает значение
     * @param mixed $value значение
     */
    public function setChecked($value) {
        $this->selectedvalue = $value;
    }

    /**
     * Возвращает  значение
     */
    public function getChecked() {
        return $this->selectedvalue;
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl() {
        // $url = $this->owner->getURLNode()."::".$this->id ;

        $out = "";
        foreach ($this->list as $item) {

            $attributes = "";
            foreach ($item['attributes'] as $key => $value) {
                $attributes = $attributes . $key . "=\"{$value}\" ";
            }
            $checked = $item['value'] === $this->selectedvalue ? ' checked="on"' : '';
            $out = $out . "<nobr><input style=\"border:none;\" type=\"radio\" name=\"{$this->id}\" {$checked} value=\"{$item['value']}\" />{$item['caption']}</nobr>";
            $out .= $this->delimiter;
        }
        $out = substr($out, 0, strlen($out) - strlen($this->delimiter));
        if(!is_string($out)) {
           $out='';    
        }

        $this->getTag()->html($out);
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData() {

        $this->selectedvalue = isset($_REQUEST[$this->id]) ? $_REQUEST[$this->id] : -1;
    }

    public function clean() {
        $this->setValue(0);
    }

}

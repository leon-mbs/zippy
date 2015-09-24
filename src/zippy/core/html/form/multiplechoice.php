<?php

namespace Zippy\Html\Form;

use \Zippy\Binding\ArrayPropertyBinding;
use \Zippy\Interfaces\Binding;

/**
 * Компонент  тэга  &lt;select  multiple=&quot;on&quot; &gt; для загрузки  файла
 */
class MultipleChoice extends HtmlFormDataElement
{

    private $optionlist;

    /**
     * Конструкт
     * @param  mixed  ID
     * @param  mixed  Модель данных
     */
    public function __construct($id, ArrayPropertyBinding $data, $optionlist)
    {
        parent::__construct($id);
        $this->setValue($data);
        $this->optionlist = $optionlist;
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        //    $this->checkInForm();
        $this->setResponseData();
        $this->setAttribute("name", $this->id . "[]");
        $this->setAttribute("multiple", "On");
    }

    private function setResponseData()
    {

        $list = $this->optionlist instanceOf Binding ? $this->optionlist->getValue() : $this->optionlist;

        foreach ($list as $key => $value) {
            $option = "<option value=\"{$key}\" ";
            $v = $this->getValue();
            $array = array_values($this->getValue());
            if (in_array($key, array_values($this->getValue()))) {
                $option .= " selected ";
            }
            $option .= ">{$value}</option>";
            $this->getTag()->append($option);
        }
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData()
    {
        if (is_array($_REQUEST[$this->id])) {
            $this->setValue($_REQUEST[$this->id]);
        } else {
            $this->setValue(array());
        }
    }

    /**
     * Возвразает  массив  списка  комбобокса
     * 
     */
    public function getOptionList()
    {
        return $list = $this->optionlist instanceOf Binding ? $this->optionlist->getValue() : $this->optionlist;
    } 

}

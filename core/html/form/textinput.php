<?php

namespace Zippy\Html\Form;


use \Zippy\Interfaces\AjaxRender;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt;
 */
class TextInput extends HtmlFormDataElement implements AjaxRender
{

    private $defvalue;
    private $dlist;
    /**
     * Конструктор
     * @param mixed  ID
     * @param Значение элемента  или  поле  привязанного объекта
     */
    public function __construct($id, $value = '') {
        parent::__construct($id);
        $this->setValue($value);
        $this->setAttribute("name", $this->id);
        $this->defvalue = $value;
    }

    /**
     * Возвращает  текстовое  значение
     * @return  string
     */
    public function getText() {
        return $this->getValue();
    }

    /**
     * Устанавливает  текстовое  значение
     * @param string
     */
    public function setText($text='') {
        $this->setValue($text);
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl() {
        // $this->checkInForm();

        $this->setResponseData();
        $this->setAttribute("list", null) ;
        $list="";
        if(is_array(  $this->dlist) && count(  $this->dlist)>0) {
             $this->setAttribute("list", $this->id.'_list') ;
             
             $list =  "  <datalist id=\"".$this->id.'_list'."\">" ;
             foreach($this->dlist as $option) {
                $list .=  " <option label='{$option}' value='{$option}'>  ";
             }
             $list .=  "  </datalist>"  ;
             $HtmlTag = $this->getTag();
             $HtmlTag->after($list);
            
             
        }
        
    }

    protected function setResponseData() {
        $this->setAttribute("value", ($this->getValue()));
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData() {

        $this->setValue($_REQUEST[$this->id]);

    }


    /**
     * @see AjaxRender
     */
    public function AjaxAnswer() {

        $text = $this->getValue();
        return "$('#{$this->id}').val('{$text}')";
    }


    public function clean() {
        $this->setText($this->defvalue);
    }

    /**
    * выпадающий список
    * 
    * @param mixed $list
    */
    public final function setDataList($list) {
        if(!is_array($list)) $list = array();
        $this->dlist  = $list;
        
    }
    
}

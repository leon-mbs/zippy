<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Interfaces\ChangeListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\AjaxRender;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt;
 */
class TextInput extends HtmlFormDataElement implements ChangeListener, Requestable, AjaxRender
{

    private $defvalue;
    private $dlist;
    private $event;
        
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
        
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;

            $url = $this->owner->getURLNode() . '::' . $this->id;
            $url = substr($url, 2 + strpos($url, 'q='));

            if ($this->event->isajax == false) {

                $this->setAttribute("onblur", "javascript:{if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit();}");
            } else {
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $this->setAttribute("onblur", "if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true');");
            }
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
    public function RequestHandle() {
        $this->OnEvent();
    }   
     /**
     * @see  ChangeListener
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = false) {

        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
    }

    /**
     * @see ChangeListener
     */
    public function OnEvent() {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }   
    
}

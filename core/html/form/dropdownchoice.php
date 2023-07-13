<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Interfaces\Binding;
use \Zippy\Interfaces\ChangeListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\AjaxRender;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Компонент  тэга  &lt;select&gt;
 */
class DropDownChoice extends HtmlFormDataElement implements ChangeListener, Requestable, AjaxRender
{

    private $optionlist;
    private $event;
    private $defvalue;

    /**
     * Конструктор
     * @param mixed  ID
     * @param array  Массив  значений
     * @param Текущее значение  елемента
     */
    public function __construct($id, $optionlist = array(), $value = -1, $bgupdate = false) {
        parent::__construct($id);
        $this->setValue($value);

        $this->defvalue = $value;
        $this->optionlist = $optionlist;
        $this->bgupdate = $bgupdate;
    }

    protected function onAdded() {
        if ($this->bgupdate) {
            $page = $this->getPageOwner();
            $this->onChange($page, 'OnBackgroundUpdate', true);
        }
    }

    /**
     * 3
     * @see  HtmlComponent
     */
    public function RenderImpl() {
        //    $this->checkInForm();


        $this->setAttribute("name", $this->id);
        $this->setAttribute("id", $this->id);

        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;

            $url = $this->owner->getURLNode() . '::' . $this->id;
            $url = substr($url, 2 + strpos($url, 'q='));

            if ($this->event->isajax == false) {

                $this->setAttribute("onchange", "javascript:{if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit();}");
            } else {
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $this->setAttribute("onchange", "if(beforeZippy('{$this->id}') ==false) return false; $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true');");
            }
        }


        $this->setResponseData();
    }

    private function setResponseData() {

        $list = $this->optionlist instanceof Binding ? $this->optionlist->getValue() : $this->optionlist;
        if(is_array($list)==false) $list = array();
        $tag = $this->getTag();
        $options = "";
        foreach ($list as $key => $value) {
            // if($item instanceof SelectOption)
            $option = "<option value=\"{$key}\" ";
 
            if ($key == $this->getValue()) {
                $option .= " selected ";
            }

            $option .= ">{$value}</option>";
            $options .= $option;
            //$tag->appendChild($option) ;
        }
        $tag->append($options);
        //$html = $tag->html();

        if (count($list) == 0) {
            //WebApplication::$app->getResponse()->addJavaScript("$('#" . $this->id . " :nth-child(" . $this->getValue() . ")').attr('selected', 'selected') ;", true);
            $js = "$('#" . $this->id . " option').each(function() {    if($(this).val() == '" . $this->getValue() . "') {        $(this).prop(\"selected\", true);   }});";

            WebApplication::$app->getResponse()->addJavaScript($js, true);
            // WebApplication::$app->getResponse()->addJavaScript("$(\"#" . $this->id . "\" ).filter('[value=1]').prop('selected', true)", true);
            //  WebApplication::$app->getResponse()->addJavaScript("$(\"#" . $this->id . ">option[value='" . $this->getValue() . "']\").prop('selected','selected')", true);
            //  WebApplication::$app->getResponse()->addJavaScript("$(\"#" . $this->id . ">option[value='" . $this->getValue() . "']\").attr('selected','selected')", true);
        }
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData() {
        if(!isset($_REQUEST[$this->id])) return;
        $this->setValue($_REQUEST[$this->id] );    
        
    }

    /**
     * @see Requestable
     */
    public function RequestHandle() {
        $this->OnEvent();
    }

    /**
     * @see AjaxRender
     */
    public function AjaxAnswer() {

        $list = $this->optionlist instanceof Binding ? $this->optionlist->getValue() : $this->optionlist;

        $js = "$('#{$this->id}').empty();";
        foreach ($list as $key => $value) {
            $js .= "$('#{$this->id}').append('<option value=\"{$key}\">{$value}</option>');";
        }
        return $js;
    }
   
    public function setValue($value) {
        
       parent::setValue($value) ;
       
       if( \Zippy\WebApplication::$app->getRequest()->isAjaxRequest() ){
          $js= "$('#{$this->id}').val('{$value}')" ;
          \Zippy\WebApplication::$app->getResponse()->addAjaxResponse($js) ;            
       }          
       
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

    protected function getTag($tagname = "") {
        return parent::getTag('select');
    }

    public function setOptionList($optionlist) {
        if (is_array($optionlist)) {
            $this->optionlist = $optionlist;
            $this->setValue(-1);
        }
        
        if( \Zippy\WebApplication::$app->getRequest()->isAjaxRequest() ){
           $list = $this->optionlist instanceof Binding ? $this->optionlist->getValue() : $this->optionlist;

           $js = "$('#{$this->id}').empty();";
           foreach ($list as $key => $value) {
              $js .= "$('#{$this->id}').append('<option value=\"{$key}\">{$value}</option>');";
           }
          
           \Zippy\WebApplication::$app->getResponse()->addAjaxResponse($js) ;            
        }           
        
    }

    /**
     * Возвращает  массив  списка  комбобокса
     *
     */
    public function getOptionList() {
        return $list = $this->optionlist instanceof Binding ? $this->optionlist->getValue() : $this->optionlist;
    }

    /**
     * Добавляет  пункт  в  список
     *
     * @param mixed $value
     * @param mixed $text
     */
    public function addOption($value, $text) {
        if ($this->optionlist instanceof Binding) {
            return;
        }
        $this->optionlist[$value] = $text;
    }

    /**
     * возвращает текст  выбраного  значения
     *
     */
    public function getValueName() {
        $list = $this->optionlist instanceof Binding ? $this->optionlist->getValue() : $this->optionlist;
        return $list[$this->getValue()] ??'';
    }

    public function clean() {
        $this->setValue($this->defvalue);
    }

    /**
     * Иициализирует комбобокс  первым значением из списка
     *
     */
    public function selectFirst() {
        $list = $this->optionlist instanceof Binding ? $this->optionlist->getValue() : $this->optionlist;
        if (count($list) == 0) {
            return;
        }
        $k = array_keys($list);
        $this->setValue($k[0]);
    }
    
    
    public function getIntValue() {
        
        return  intval($this->getValue());
        
    }    
    
}

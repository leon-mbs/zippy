<?php
namespace ZCL\BT;

use \Zippy\Validator\Validator;
use \Zippy\Interfaces\Validated;
use \Zippy\Interfaces\AjaxRender;
use \Zippy\WebApplication;

/**
 * Компонент  списка  тегов основанный  на
 * https://github.com/maxwells/bootstrap-tags
 */
class Tags extends \Zippy\Html\Form\HtmlFormDataElement  
{

   
    public $sug=array();

    /**
     * Конструктор
     * @param mixed  ID
     * @param Значение элемента  или  поле  привязанного объекта
     */
    public function __construct($id )
    {
        parent::__construct($id);
        $this->setValue(array());
    }
   
    public function RenderImpl()
    {
        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
       
        $data = json_encode($this->value);
        $sug = json_encode($this->sug);
        $val = implode(';',$this->value);
        
        $js = " 
              var tags =      $('#{$this->id}').tags({
                        tagData:{$data},
                        suggestions:{$sug} ,
                        afterAddingTag:function(tag){
                          var t = tags.getTags();
                          $('#{$this->id}_tags').val(t.join(\";\"));
                        },
                        afterDeletingTag:function(tag){
                          var t = tags.getTags();
                          $('#{$this->id}_tags').val(t.join(\";\"));
                         
                        }
                        
                         
                   }); 
                    $('#{$this->id}').after('<input type=\"hidden\" id=\"{$this->id}_tags\" name=\"{$this->id}_tags\" value=\"{$val}\"  />');

                  ";       
       
        WebApplication::$app->getResponse()->addJavaScript($js, true);

    }

    /**
    * список подсказок
    * 
    * @param mixed $sug
    */
    public function setSuggestions($sug)
    {
        $this->sug =$sug;
    }
   public function setTags($tags)
    {
        $this->setValue($tags);
    }
    public function getTags()
    {
      return  $this->getValue();
    }
    
    public function getRequestData()
    {

        $tags =$_REQUEST[$this->id.'_tags'];
        $this->setValue(explode(';',$tags));
 
    }    
}
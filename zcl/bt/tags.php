<?php

namespace ZCL\BT;

use \Zippy\Interfaces\AjaxRender;
use \Zippy\WebApplication;

/**
 * Компонент  списка  тегов основанный  на
 * https://github.com/maxwells/bootstrap-tags
 */
class Tags extends \Zippy\Html\Form\HtmlFormDataElement
{

    private $_sug = array();
    private $_options = array();

    /**
     * 
     * 
     * @param mixed $id
     * @param mixed $options  Массив  опций элемента в  виде  ключ-значение
     */
    public function __construct($id, $options = null)
    {
        parent::__construct($id);
        $this->setValue(array());
        if (is_array($options)) {
            $this->_options = $options;
        }
    }

    public function RenderImpl()
    {
        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";

        $data = json_encode($this->value);
        $sug = json_encode($this->_sug);
        $val = implode(';', $this->value);

        $js = " 
              var tags =      $('#{$this->id}').tags({
                        tagData:{$data},
                        suggestions:{$sug} , ";
        foreach ($this->_options as $key => $value) {
            $js .= "{$key}:'{$value}',";
        }
        $js .= "     afterAddingTag:function(tag){
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
        $this->_sug = $sug;
    }

    public function setTags($tags)
    {
        $this->setValue($tags);
    }

    public function getTags()
    {
        return $this->getValue();
    }

    public function getRequestData()
    {

        $tags = $_REQUEST[$this->id . '_tags'];
        $this->setValue(explode(';', $tags));
    }
 
     public function clean(){
        $this->_options = array();
     }
}

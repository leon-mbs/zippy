<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Interfaces\Requestable;
use \Zippy\Event;
use \Zippy\Interfaces\EventReceiver;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt; с  автозавершением
 */
class AutocompleteTextInput extends TextInput implements Requestable
{

    public $minChars = 2;
    public $timeout = 100;
    private $key = 0;
    private $event2 = null;
    private $event = null;

    /**
     * Конструктор
     * @param  Zippy ID
     * @param  Минимальное  количество  символов
     * @param  Таймаут в  мс.
     */
    public function __construct($id, $minChars = 2, $timeout = 100, $bgupdate = false)
    {
        parent::__construct($id);
        $this->minChars = $minChars;
        $this->timeout = $timeout;
        $this->bgupdate = $bgupdate;
    }

    protected function onAdded()
    {
        if ($this->bgupdate) {
            $page = $this->getPageOwner();
            $this->onChange($page, 'OnBackgroundUpdate', true);
        }
    }

    public function RenderImpl()
    {
        TextInput::RenderImpl();

        $onchange = "null";

        if ($this->event2 != null) {
            $formid = $this->getFormOwner()->id;

            if ($this->event2->isajax == false) {

                $url = $this->owner->getURLNode() . '::' . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $onchange = " { $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "_s').trigger('click');}";
            } else {
                $url = $this->owner->getURLNode() . "::" . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                $onchange = "  { $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true'); }";
            }
        }
        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";

        $js = "
                    $('#{$this->id}').typeahead(
                    {   
                    minLength:{$this->minChars},
                    source: function (query, process) {
                         
                        return $.getJSON('{$url}&text=' + query, function (data) {
                            return process(data);
                        });
                    },
                    highlighter: function(item) {
                          var parts = item.split('_');
                          parts.shift();
                          return parts.join('_');
                      }, 
                    updater: function(item) {
                      var parts = item.split('_');   ;
                      var userId = parts.shift();   
                      $('#{$this->id}_id').val( userId);
                   
                      return parts.join('_');
                   } ,
                   afterSelect :function(item) {
                        {$onchange}     
                   }
                   }); 
                   $('#{$this->id}').after('<input type=\"hidden\" id=\"{$this->id}_id\" name=\"{$this->id}_id\"  value=\"{$this->key}\"/>');

                  ";
        //  $this->setAttribute("data-key", $this->key);
        $this->setAttribute("autocomplete", 'off');


        WebApplication::$app->getResponse()->addJavaScript($js, true);
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->OnEvent2();
            return;
        }


        $this->setValue($_REQUEST['text']);
        $arr = $this->OnAutocomplete();
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                //$posts[] = array("id"=>$key, "value"=> $value);
                $posts[] = $key . "_" . $value;
            }
        }


        WebApplication::$app->getResponse()->addAjaxResponse(json_encode($posts));
    }

    /**
     * Событие при автозавершении.
     * Вызывает  обработчик который  должен  вернуть  массив строк для  выпадающего списка.
     */
    public function OnAutocomplete()
    {
        if ($this->event != null) {
            return $this->event->onEvent($this);
        }
        return null;
    }

    /**
     * Устанавливает  событие
     * @param Event
     */
    public function onText(EventReceiver $receiver, $handler)
    {

        $this->event = new Event($receiver, $handler);
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData()
    {
        $this->setValue($_REQUEST[$this->id]);
        $this->key = $_REQUEST[$this->id . "_id"];
 
        if (strlen(trim($this->getValue())) == 0)
            $this->key = 0;
    }

    //возвращает  ключ  для   выбранного значения
    public function getKey()
    {
        return $this->key;
    }

    //
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @see  ChangeListener
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = true)
    {
        $this->event2 = new Event($receiver, $handler);
        $this->event2->isajax = $ajax;
    }

    /**
     * @see ChangeListener
     */
    public function OnEvent2()
    {
        if ($this->event2 != null) {
            $this->event2->onEvent($this);
        }
    }

}

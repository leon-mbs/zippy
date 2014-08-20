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

    /**
     * Конструктор
     * @param  Zippy ID 
     * @param  Минимальное  количество  символов
     * @param  Таймаут в  мс.
     */
    public function __construct($id, $minChars = 2, $timeout = 100)
    {
        parent::__construct($id);
        $this->minChars = $minChars;
        $this->timeout = $timeout;
    }

    public function RenderImpl()
    {
        TextInput::RenderImpl();

        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";

        $js = "
                    $('#{$this->id}').z_autocomplete('{$url}',{minChars:{$this->minChars},timeout:{$this->timeout}});
                      ";
        $this->setAttribute("data-key", $this->key);
        $this->setAttribute("autocomplete", 'off');

        WebApplication::$app->getResponse()->addJavaScript($js, true);
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {

        $this->setValue($_REQUEST['text']);
        $arr = $this->OnAutocomplete();
        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                $posts[] = array($key => $value);
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
    public function setAutocompleteHandler(EventReceiver $receiver, $handler)
    {

        $this->event = new Event($receiver, $handler);
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData()
    {
        $this->setValue($_REQUEST[$this->id . "_text"]);
        $this->key = $_REQUEST[$this->id];
        foreach ($this->validators as $validator) {
            $validator->validate($this, $this->getValue());
        }
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

}

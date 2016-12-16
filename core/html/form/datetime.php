<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Event;
use \Zippy\Interfaces\ChangeListener;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Interfaces\Requestable;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt; для ввода  даты и времени (используется jQuery)
 * https://github.com/mugifly/jquery-simple-datetimepicker
 */
class DateTime extends TextInput implements Requestable, ChangeListener
{

    private $event;

    public function __construct($id, $value = null, $bgupdate = false)
    {
        parent::__construct($id);
        $this->setDate($value);
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

        // $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";

        $js = "$('#{$this->id}').pickatime( {format : 'HH:i',interval:5 });";
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;

            if ($this->event->isajax == false) {
                $url = $this->owner->getURLNode() . '::' . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $this->setAttribute("onchange", "javascript:{ $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit();}");
                $js = "$('#{$this->id}').pickatime( {format : 'HH:i' ,onSet: function() {  
                   $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit()
                } }   );";
            } else {
                $url = $this->owner->getURLNode() . "::" . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();

                $js = "$('#{$this->id}').dtpicker( {dateFormat : 'yy-m-d' ,onSelect: function() {  
                   $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true')
                } }   );";
            }
        }
        WebApplication::$app->getResponse()->addJavaScript($js, true);
    }

    /**
     * Возвращает дату   в виде timestamp
     * @param mixed $endday - установить  конец  дня 
     */
    public function getDate($endday = false)
    {
        $date = strtotime($this->getText());
        if ($endday == true) {
            $d = date('Y-m-d', $date);
            $date = strtotime($d . ' 23:59:59');
        }
        return $date;
    }

    /**
     * Устанавливает дату
     * Если  параметр не  задан  - текущая  дата
     * 
     * @param mixed $t - timestamp 
     */
    public function setDate($t = null)
    {
        if ($t > 0) {
            $this->setText(date('Y-m-d H:i', $t));
        } else {
            $this->setText("");
        }
    }

    /**
     * @see  ChangeListener
     */
    public function onChange(EventReceiver $receiver, $handler,$ajax=false)
    {
        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
    }
    

    /**
     * @see ChangeListener
     */
    public function OnChange()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {
        $this->OnChange();
    }

}

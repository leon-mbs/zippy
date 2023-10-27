<?php 

namespace Zippy\Html\Form;

use Zippy\WebApplication;
use Zippy\Event;
use Zippy\Interfaces\ChangeListener;
use Zippy\Interfaces\EventReceiver;
use Zippy\Interfaces\Requestable;

/**
 * Компонент  тэга  &lt;input type=&quot;date&quot;&gt;
  */
class Date extends TextInput implements Requestable, ChangeListener
{
    private $event;
    private $min=0;
    private $max=0;

    public function __construct($id, $value = null, $bgupdate = false) {
        parent::__construct($id);
        $this->setDate($value);
        $this->bgupdate = $bgupdate;
    }

    protected function onAdded() {
        if ($this->bgupdate) {
            $page = $this->getPageOwner();
            $this->onChange($page, 'OnBackgroundUpdate', true);
        }
    }

    /**
     * Установка  минимальной  или  максимальной  дат.  Если  не  используется  передать 0
     *
     * @param mixed $min
     * @param mixed $max
     */
    public function setMinMax($min, $max) {
        if($min > $max) {
            return;
        }
        $this->min = $min;
        $this->max = $max;
    }

    public function RenderImpl() {
        TextInput::RenderImpl();

        /*
        $min='';
        $max='';
        // $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
        if ($this->min > 0) {
            $min = ", min: new Date(" . date("Y,m-1,d", $this->min) . ")";
        }
        if ($this->max > 0) {
            $max = ", max: new Date(" . date("Y,m-1,d", $this->max) . ")";
        }

        $js = "$('#{$this->id}').pickadate(  {    format: 'yyyy-mm-dd' {$min} {$max} });";
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;

            if ($this->event->isajax == false) {
                $url = $this->owner->getURLNode() . '::' . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $this->setAttribute("onchange", "javascript:{ $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit(); }");
                $js = "$('#{$this->id}').pickadate( {format: 'yyyy-mm-dd',onSet: function() {   ;
                   $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit()
                } }   );";
            } else {
                $url = $this->owner->getURLNode() . "::" . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();

                $js = "$('#{$this->id}').pickadate( {format: 'yyyy-mm-dd' ,onSet: function() {
                   $('#" . $formid . "_q').attr('value','" . $url . "'); submitForm('{$formid}','{$_BASEURL}/?ajax=true')
                } }   );";
            }
        }
        WebApplication::$app->getResponse()->addJavaScript($js, true);
        */
        $this->setAttribute('type', 'date') ;
        if($this->min >0) {
            $this->setAttribute('min', date('Y-m-d', $this->min)) ;
        }
        if($this->max >0) {
            $this->setAttribute('max', date('Y-m-d', $this->max)) ;
        }

    }

    /**
     * Возвращает дату   в виде timestamp
     * @param mixed $endday - установить  конец  дня
     */
    public function getDate($endday = false) {
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
    public function setDate($t = 0) {
        if ($t > 0) {
            $this->setText(date('Y-m-d', $t));
        } else {
            $this->setText("");
        }
    }

    /**
     * @see  ChangeListener
     */
    public function onChange(EventReceiver $receiver, $handler, $ajax = true) {

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

    /**
     * @see Requestable
     */
    public function RequestHandle() {
        $this->OnEvent();
    }

}

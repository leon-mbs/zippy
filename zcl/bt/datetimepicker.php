<?php

namespace ZCL\BT;

use \Zippy\WebApplication;
use \Zippy\Event;
use \Zippy\Interfaces\ChangeListener;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Interfaces\Requestable;

/**
 * Twitter Botstrap компонент  тэга  &lt;input type=&quot;text&quot;&gt; с  календарем и выбором  времени
 */
class DateTimePicker extends \Zippy\Html\Form\TextInput implements Requestable, ChangeListener
{

    private $event;
    private $min, $max;

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
     * set range
     *
     * @param mixed $min
     * @param mixed $max
     */
    public function setMinMax($min, $max = null) {
        $this->min = $min;
        $this->max = $max;
    }

    public function RenderImpl() {
        \Zippy\Html\Form\TextInput::RenderImpl();

        // $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";


        if ($this->min > 0) {
            $min = ", startDate:  '" . date("Y-m-d h:i", $this->min) . "' ";
        }
        if ($this->max > 0) {
            $max = ", endDate:  '" . date("Y-m-d h:i", $this->max) . "' ";
        }

        $js = "$('#{$this->id}').datetimepicker( {  format : 'yyyy-mm-dd hh:ii',fontAwesome:true ,minuteStep: 30,autoclose:true {$min} {$max}  });";
        if ($this->event != null) {
            $formid = $this->getFormOwner()->id;

            if ($this->event->isajax == false) {
                $url = $this->owner->getURLNode() . '::' . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $this->setAttribute("onchange", "javascript:{ $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit();}");
                $js = "$('#{$this->id}').datetimepicker( {format : 'yyyy-mm-dd hh:ii',fontAwesome:true}).on('changeDate', function() {  
                   $('#" . $formid . "_q').attr('value','" . $url . "');$('#" . $formid . "').submit()
                }     );";
            } else {
                $url = $this->owner->getURLNode() . "::" . $this->id;
                $url = substr($url, 2 + strpos($url, 'q='));
                $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();

                $js = "$('#{$this->id}').datetimepicker( {format : 'yyyy-mm-dd hh:ii',fontAwesome:true}).on('changeDate', function() {  
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
    public function setDate($t = null) {
        if ($t > 0) {
            $this->setText(date('Y-m-d H:i', $t));
        } else {
            $this->setText("");
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

    /**
     * @see Requestable
     */
    public function RequestHandle() {
        $this->OnEvent();
    }

}

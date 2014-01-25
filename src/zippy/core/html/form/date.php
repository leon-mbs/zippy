<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;

/**
 * Компонент  тэга  &lt;input type=&quot;text&quot;&gt; с  календарем (используется jQuery)
 */
class Date extends TextInput
{

        public function __construct($id, $value = null)
        {
                parent::__construct($id);
                $this->setDate($value);
        }

        public function RenderImpl()
        {
                TextInput::RenderImpl();

                $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";

                $js = "$('#{$this->id}').datepicker( {dateFormat : 'yy-m-d' });";


                WebApplication::$app->getResponse()->addJavaScript($js, true);
        }

        /**
         * Возвращает дату   в виде timestamp
         * 
         */
        public function getDate()
        {
                return strtotime($this->getText());
        }

        /**
         * Устанавливает дату
         * Если  параметр не  задан  - текущая  дата
         * 
         * @param mixed $t - timestamp 
         */
        public function setDate($t = null)
        {
                if ($t == null && $t == 0) {
                        $this->setText("");
                } else {
                        $this->setText(date('Y-m-d', $t));
                }
        }

}
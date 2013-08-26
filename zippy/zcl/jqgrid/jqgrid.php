<?php

namespace ZCL\JqGrid;

use \Zippy\WebApplication ;

use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Класс-backend для jQuery  плагина  jqGrid  
 * 
 */
class JQGrid extends HtmlComponent implements Requestable
{

        private $loadevent = null;
        private $editevent = null;

        /**
         * @see HtmlComponent
         */
        public final function RenderImpl()
        {
                $id = $this->getAttribute('id');
                $url = $this->owner->getURLNode() . "::" . $this->id . ":loaddata&ajax=true";
                WebApplication::$app->getResponse()->addJavaScript("$('#{$id}').jqGrid('setGridParam',{url:'{$url}'});", true);
                $url = $this->owner->getURLNode() . "::" . $this->id . ":editdata&ajax=true";
                WebApplication::$app->getResponse()->addJavaScript("$('#{$id}').jqGrid('setGridParam',{editurl:'{$url}'});", true);
                WebApplication::$app->getResponse()->addJavaScript("window.setTimeout(function(){ $('#{$id}').trigger('reloadGrid')},400)", true);
        }

        /**
         * @see Requestable
         */
        public final function RequestHandle()
        {

                $params = WebApplication::$app->getRequest()->request_params[$this->id];  
                $gridp = array('page'=>$_REQUEST['page'],'rows'=>$_REQUEST['rows'],'sidx'=>$_REQUEST['sidx'],'sord'=>$_REQUEST['sord']) ;
                if ($params[0] == 'loaddata' && $this->loadevent != null) {
                        $data = $this->loadevent->onEvent($this, $gridp);
                        WebApplication::$app->getResponse()->addAjaxResponse(json_encode($data));
                }
                if ($params[0] == 'editdata' && $this->editevent != null) {
                        $data = $this->editevent->onEvent($this, $gridp);
                        WebApplication::$app->getResponse()->addAjaxResponse($data);
                }
        }

        /**
         * Установка  обработчика на  загрузку  данных  в  таблицу
         * @param  EventReceiver Объект с методом  обработки  события
         * @param  string Имя  метода-обработчика
         */
        public function setOnLoadDataHandler(EventReceiver $receiver, $handler)
        {

                $this->loadevent = new Event($receiver, $handler);
        }

        /**
         * Установка  обработчика на  редактирование записи  в  таблице
         * @param  EventReceiver Объект с методом  обработки  события
         * @param  string Имя  метода-обработчика
         */
        public function setOnEditDataHandler(EventReceiver $receiver, $handler)
        {

                $this->editevent = new Event($receiver, $handler);
        }

}

?>

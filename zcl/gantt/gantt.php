<?php

namespace ZCL\Gantt;

use \Zippy\WebApplication;
use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Класс-backend для jQuery  плагина  jqGanttView
 * 
 */
class Gantt extends HtmlComponent implements \Zippy\Interfaces\Requestable
{

    private $event = null;
    private $data = array();

    /**
     * @see HtmlComponent
     */
    public final function RenderImpl()
    {
        $id = $this->getAttribute('id');
        $url = $this->owner->getURLNode() . "::" . $this->id;
        $json = $this->getJson();
        $js = <<<EOT
            
         var  ganttData   = {$json}     
         
         var sw=  $("#{$id}").attr('data-slidew') 

            $("#{$id}").ganttView({ 
                data: ganttData,
                slideWidth:  sw,
                behavior: {
                    onClick: function (data) { 
                        
                        var url ='{$url}:' + data.id  + ':click:' + data.start.getTime()/1000 + ':'+ data.end.getTime()/1000 +'&ajax=true'
                        $.ajax({
                                url: url,
                                success: function (data, textStatus) {
                                      // eval(data);
                                }
                        }); 

                    },
                    onResize: function (data) { 
                         var url ='{$url}:' + data.id  + ':resize:' + data.start.getTime()/1000 + ':'+ data.end.getTime()/1000 +'&ajax=true'
                        $.ajax({
                                url: url,
                                success: function (data, textStatus) {
                                      // eval(data);
                                }
                        });   
                    },
                    onDrag: function (data) { 
                         var url ='{$url}:' + data.id  + ':drag:' + data.start.getTime()/1000 + ':'+ data.end.getTime()/1000 +'&ajax=true'
                        $.ajax({
                                url: url,
                                success: function (data, textStatus) {
                                      // eval(data);
                                }
                        });     
                       
                    }
                }
            });   
EOT;

        WebApplication::$app->getResponse()->addJavaScript($js, true);
    }

    /**
     * @see Requestable
     */
    public final function RequestHandle()
    {
        $params = WebApplication::$app->getRequest()->request_params[$this->id];
        $action = array();
        $action['id'] = $params[0];
        $action['action'] = $params[1];
        $action['start'] = $params[2];
        $action['end'] = $params[3];

        if ($this->event != null) {
            $this->event->onEvent($this, $action);
        }
    }

    /**
     * Устанавливает  обработчик события.  
     * Обработчик  должен  иметь  праметрыЖ
     * $sender - источник
     * $event - данные  события 
     * $event['action'] (может  быть 'click','drag','resize')
     * $event['id']   идентификатор  задачи
     * $event['start']   дата  начала
     * $event['end']   дата конца
     * @param EventReceiver $receiver
     * @param mixed $handler
     */
    public function setAjaxEvent(EventReceiver $receiver, $handler)
    {

        $this->event = new Event($receiver, $handler);
    }

    private function getJson()
    {
        $json = "";


        foreach ($this->data as $item) {
            $json .= ", { id: {$item->id}, name: \"\", series: [{ name: \"{$item->title}\", start: {$item->start}, end: {$item->end}, color: \"{$item->color}\" }     ]    } ";
        }

        return "[" . trim($json, ",") . " ]";
    }

    /**
     * Записывает массив  задач ( массивы GanttItem )
     * 
     * 
     * @param mixed $data
     */
    public function setData($data = array())
    {
        $this->data = $data;
    }

}

class GanttItem
{

    public $id;
    public $title;
    public $start;   //timestamp
    public $end;   //timestamp
    public $color;  //css color

    public function __construct($id, $title, $start, $end, $color = "")
    {

        $this->id = $id;
        $this->title = $title;
        $this->start = "new Date(" . date("Y", $start) . "," . (date("m", $start) - 1 ) . "," . date("d", $start) . ")";

        $this->end = "new Date(" . date("Y", $end) . "," . (date("m", $end) - 1 ) . "," . date("d", $end) . ")";
        // $this->end =  "new Date(2010,00,05)" ;        
        $this->color = $color;
    }

}

?>

<?php

namespace Zippy\Html\Form;

use \Zippy\WebApplication;
use \Zippy\Html\HtmlContainer;
use \Zippy\Interfaces\SubmitDataRequest;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Класс  компонента  HTML формы
 */
class Form extends HtmlContainer
{

    private $event;

    /**
     * Конструктор
     * @param string ШВ
     * @param  string  Тип запроса get  или  post
     */
    public function __construct($id, $method = "post")
    {
        parent::__construct($id);
        $this->setAttribute("method", $method);
    }

    /**
     * @see HtmlComponent
     */
    protected function beforeRender()
    {


        $url = $this->owner->getURLNode();


        $this->setAttribute("id", $this->id);
        $this->setAttribute("name", $this->id);

        $url = substr($url, strpos($url, 'index.php?q=') + 3);

        $HtmlTag = $this->getTag('form');
        $HtmlTag->append("<input type=\"hidden\" name=\"q\" id=\"{$this->id}_q\" value=\"" . $url . "::" . $this->id . "\" >");
        $HtmlTag->append("<input type=\"hidden\" name=\"{$this->id}_q\" value=\"\" >");
        $HtmlTag->append("<input type=\"submit\" name=\"{$this->id}_s\" id=\"{$this->id}_s\" style=\"display:none\" >");
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {

        if (isset($_REQUEST[$this->id . '_q'])) {
            $allchild = $this->getChildComponents(true);
            foreach ($allchild as $component) {
                if ($component instanceof SubmitDataRequest) {
                    //проверяем что элемент не находится в невидимой панели
                    if ($this->checkInvisibleOwner($component) == false) {
                        if ($component->isVisible()) {
                            $component->getRequestData();
                        }
                    }
                }
            }
            $clist =  explode("::",$_REQUEST["q"]);
            if($clist[count($clist)-1]== $this->id)   {
                $this->onEvent();    
            }
            
        }
        parent::RequestHandle();
    }

    /**
     * Устанавливает  обработчик  события  при  отправке  формы
     * @param EventReceiver Объект  метод  которого  является  обработчиком  события
     * @param string Имя  метода - обработчика
     */
    public function onSubmit(EventReceiver $receiver, $handler)
    {
        $this->event = new Event($receiver, $handler);
    }

    /**
     * Вызывает  обработчик   события  отправки  формы
     */
    public function onEvent()
    {
        if ($this->event instanceof Event) {
            $this->event->onEvent($this);
        }
    }

    private function checkInvisibleOwner($component)
    {
        $owner = $component->getOwner();
        do {
            if ($owner->isVisible() == false) {
                return true;
            }
            $owner = $owner->getOwner();
        } while ($owner != null);
        return false;
    }

    /**
     * Очистка  элементов  формы
     *
     */
    public function clean()
    {
        $allchild = $this->getChildComponents(true);
        foreach ($allchild as $component) {
            
            if($component instanceof SubmitDataRequest) {
               $component->clean();
            }
         
 
        }
    }

}

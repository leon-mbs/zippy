<?php

namespace Zippy\Html;

use Zippy\WebApplication;
use Zippy\Exception as ZE;
use Zippy\Interfaces\Requestable;

/**
 *  Базовый компонент контейнера  для  других  компонентов
 *
 */
abstract class HtmlContainer extends HtmlComponent implements Requestable
{
    protected $components = array();

    /**
     * @see  HtmlComponent
     */
    public function __construct($id) {
        parent::__construct($id);
    }

    /**
     * Добавляет  компонент  к  списку  дочерних
     * Иерархия  компонентов  должна   строго соответствовать  иерархии
     * вложеных  тэгов (с аттрибутами  zippy) в HTML шаблоне
     */
    public function add(HtmlComponent $component) {
        if (isset($this->components[$component->id])) {
            throw new ZE(sprintf(ERROR_COMPONENT_ALREADY_EXISTS, $component->id, $this->id));
        }
        if (property_exists($this, $component->id)) {
            $id = strlen($this->id) > 0 ? $this->id : get_class($this);
            throw new ZE(sprintf(ERROR_COMPONENT_AS_PROPERTY, $component->id, $id));
        }

        $this->components[$component->id] = $component;
        $component->setOwner($this);
        $component->onAdded();
        return $component;
    }

    /**
     * Получить  дочерний   компонент
     *
     * @param string $id ID компонента
     * @param mixed $desc Если  false  - искать  только непосредственнно  вложенных
     * @return HtmlComponent
     */
    public function getComponent($id, $desc = true) :null|HtmlComponent {
        if ($desc == false) {
            return $this->components[$id];
        } else {
            if (isset($this->components[$id])) {
                return $this->components[$id];
            }

            foreach ($this->components as $component) {
                if ($component instanceof HtmlContainer === false) {
                    continue;
                }

                $tmp = $component->getComponent($id);

                if ($tmp != null) {
                    return $tmp;
                }
            }
            return null;
        }
    }

    /**
     * Перегрузка  данного  метода  позволяет
     * получить  доступ  к  дочернему   компоненту как к полю  объекта
     *
     * $this->add(new Label('msg'));
     * ...
     * $this->msg->setValue();
     *
     *
     * @param string $id ID компонента
        
     */
    final public function __get($id)   {

        if (!isset($this->components[$id])) {
            $m = sprintf(ERROR_NOT_FOUND_CHILD, get_class($this), $this->id, $id);
            throw new ZE($m);
        };

        return $this->components[$id];
    }

    /**
     * Возвращает (для  дочерних) формируемый  иерархией компонентов
     *  HTTP адрес, добавляя  собственный  ID
     */
    public function getURLNode() {
        return $this->owner->getURLNode() . "::" . $this->id;
    }

    /**
     * Имплементация  рендеринга
     * Вызывает  методы  для  рендеринга   вложеных  компонентов
     */
    protected function RenderImpl() {

        //   $this->beforeRender();
        //$keys =   array_keys($this->components);
        foreach ($this->components as $component) {


            if ($component->isVisible()) {
                $component->Render();
            } else {
                $component->getTag()->destroy();
                $label = $component->getLabelTag();
            
                foreach ($label as $l) {
                    $l->destroy();
                }
      
                $label = $component->getLabelTagFor();

                foreach ($label as $l) {
                    $l->destroy();
                }

            }
        }
        //   $this->afterRender();
    }

    /**
     * Обработка  HTTP запроса
     * отделяет  собственный  ID  с  запроса  и  передает дочернему  элементу
     * которому  предназначен  запрос
     */
    public function RequestHandle() {
        if ($this->visible == false) {
            return;
        }

        $child = next(WebApplication::getApplication()->getRequest()->request_c);

        if ($child != false && ($this->components[$child] ??null) instanceof Requestable) {
            $this->components[$child]->RequestHandle();
        } else {
            if($this instanceof \Zippy\Html\WebPage || $this instanceof \Zippy\Html\PageFragment) { //ищем метод
                if(method_exists($this, $child)) {
                    $this->RequestMethod($child); //webpage methos

                }
            }
        }



    }

    /**
     * Возвращает  массив   вложенных  компонентов
     * @param mixed  $all Если  true, возвращает  со всех  уровней   вложения
     */
    public function getChildComponents($all = false) {
        if ($all == false) {
            return $this->components;
        }

        $list = array();
        foreach ($this->components as $component) {
            $list[] = $component;

            if ($component instanceof HtmlContainer) {
                $childs = $component->getChildComponents(true);
                foreach ($childs as $child) {
                    $list[] = $child;
                }
                //array_push($list,$component->getChildComponents(true));
            }
        }
        return $list;
    }

     /**
     * алиас на getComponent
     * 
     * @param mixed $id
     * @param mixed $desc
 
     */
    public function _c($id, $desc = true)   {
         return $this->getComponent($id,$desc) ;
    }
       
}

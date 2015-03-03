<?php

namespace Zippy\Html;

use \Zippy\WebApplication;
use \Zippy\Interfaces\AjaxRender;
use \Zippy\Interfaces\EventReceiver;

/**
 *  Базовый  класс  для  компонентов-страниц
 *  Является  контейнером  для  всех компонентов  страницы
 *
 */
abstract class WebPage extends HtmlContainer implements EventReceiver
{

    public $layout = '';
    public $args = '';
    public $_title = '';
    public $_description = '';
    public $_keywords = '';
    private $beforeRequestEvents = array();  //array  of callbacks
    private $afterRequestEvents = array();  //array  of callbacks
    private $_ajax;
    private $_ankor ='';

    /**
     * Конструктор
     *
     */
    public function __construct()
    {
        
    }

    /**
     * Вызывается   из  WebApplication при обработке  HTTP запроса
     *
     * @param array  Запрос  ввиде массива  элементов
     * @see WebApplication
     */
    public function RequestHandle()
    {

        $this->beforeRequest();

        parent::RequestHandle();

        $this->afterRequest();
    }

    /**
     * @see HtmlContainer
     */
    public final function getURLNode()
    {
        return WebApplication::$app->getResponse()->getBaseUrl();
    }

    /**
     * Вызывается  перед  сохранением  страницы  в   персистентной  сессии
     */
    public function beforeSaveToSession()
    {
        
    }

    /**
     * Вызывается  после  восстановлении страницы  из  персистентной  сессии
     */
    public function afterRestoreFromSession()
    {
        
    }

    /**
     *   Вызывается в  реализации  страницы  после  AJAX запроса
     * для   елементов которые  должны  перерендерится на   клиенте
     *
     * @param  mixed  id  или массив id (значений   атттрибута  zippy) компонентов 
     * @param  string Произвольный JavaScript код для  выполнения  на  клиенте после Ajax вызова
     *
     * @see AjaxRender
     */
    protected function updateAjax($components, $js = null)
    {


        if (!is_array($components)) {
            $components = array($components);
        }
        if (is_array($components)) {
            foreach ($components as $item) {
                $this->_ajax[] = $item;
            }
        }
        if (strlen($js) > 0) {
            WebApplication::$app->getResponse()->addAjaxResponse($js);
        }
    }

    public function renderAjax()
    {
        if (is_array($this->_ajax)) {
            foreach ($this->_ajax as $item) {
                $component = $this->getComponent($item);

                if ($component instanceof AjaxRender) {
                    $responseJS = $component->AjaxAnswer();
                    WebApplication::$app->getResponse()->addAjaxResponse($responseJS);
                }
            }
        }
    }

    /**
     * @see HttpComponent
     * 
     */
    public function Render()
    {
        if(strlen($this->_ankor) >0 ){
             WebApplication::$app->getResponse()->addJavaScript("window.location='#".$this->_ankor."'", true);
             $this->_ankor = '';
        }
        $this->beforeRender();
        $this->RenderImpl();
        $this->afterRender();
    }

    /**
     * Добавляет  обработчик  на  событие  перед  обработкой  страницей  HTTP запроса
     * @param  $obj  Объект  регистрирующий  свою  функцию как   callback
     * @param  $func Имя функции - обработчика
     */
    public function addBeforeRequestEvent($obj, $func)
    {
        $this->beforeRequestEvents[] = new \Zippy\Event($obj, $func);
    }

    /**
     * Добавляет  обработчик  на  событие  после обработки страницей  HTTP запроса
     * @param  $obj  Объект  регистрирующий  свою  функцию как   callback
     * @param  $func Имя функции - обработчика
     */
    public function addAfterRequestEvent($obj, $func)
    {
        $this->afterRequestEvents[] = new \Zippy\Event($obj, $func);
    }

    /**
     * Вызывается  перед requestHandler
     * 
     */
    public function beforeRequest()
    {
        $this->_ajax = array();

        if (count($this->beforeRequestEvents) > 0) {
            foreach ($this->beforeRequestEvents as $event) {
                $event->onEvent($this);
            }
        }
    }

    /**
     * Вызывается  после requestHandler
     * 
     */
    public function afterRequest()
    {

        if (count($this->afterRequestEvents) > 0) {
            foreach ($this->afterRequestEvents as $event) {
                $event->OnEvent($this);
            }
        }
    }

    /**
    * Переход  по  имени  якоря (после  загрузки страницы)
    * 
    * @param mixed $name
    */
    
    protected function goAnkor($name){
        $this->_ankor  = $name;
    }
}

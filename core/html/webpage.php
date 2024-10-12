<?php

namespace Zippy\Html;

use Zippy\WebApplication;
use Zippy\Interfaces\EventReceiver;
use Zippy\Interfaces\AjaxRender;

/**
 *  Базовый  класс  для  компонентов-страниц
 *  Является  контейнером  для  всех компонентов  страницы
 *
 */
abstract class WebPage extends HtmlContainer implements EventReceiver
{
    public $args = '';
    public $_title = '';
    public $_description = '';
    public $_keywords = '';
    private $beforeRequestEvents = array();  //array  of callbacks
    private $afterRequestEvents = array();  //array  of callbacks
    private $_ajax;
    private $_ankor = '';
    public $_tvars = array();  //переменные  для  шаблонизатора Mustache
    protected $_ajaxblocks = [];  //список для  серверного ренеринга  при  AJAX
    //  public $zarr  = array();

    /**
     * Конструктор
     *
     */

    public function __construct() {


    }

    /**
     * Вызывается   из  WebApplication при обработке  HTTP запроса
     *
     * @param array  Запрос  ввиде массива  элементов
     * @see WebApplication
     */
    public function RequestHandle() {
        $this->_ajaxblock = [];
        $this->beforeRequest();

        parent::RequestHandle();

        $this->afterRequest();
    }

    /**
     * @see HtmlContainer
     */
    final public function getURLNode() {
        return WebApplication::$app->getResponse()->getBaseUrl();
    }

    /**
     * Вызывается  перед  сохранением  страницы  в   персистентной  сессии
     */
    public function beforeSaveToSession() {

    }

    /**
     * Вызывается  после  восстановлении страницы  из  персистентной  сессии
     */
    public function afterRestoreFromSession() {

    }

    /**
     *   Вызывается в  реализации  страницы  после  AJAX запроса
     * для   елементов которые  должны  перерендерится на   клиенте
     *
     * @param mixed  id  или массив id (значений   атттрибута  zippy) компонентов
     * @param string Произвольный JavaScript код для  выполнения  на  клиенте после Ajax вызова
     *
     * @see AjaxRender
     */
    protected function updateAjax($components, $js = null) {
        $this->addAjaxResponse($js) ;
        return;
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

    /**
     * Рендерит  компоненты для  ajax ответа
     * @panels   рендеринг  панелей
     */
/*     
    public function renderAjax($panels = false) {
        $haspanels = false;
        if (is_array($this->_ajax)) {
            foreach ($this->_ajax as $item) {
                $component = $this->getComponent($item);

                if ($component instanceof Panel) {
                    $haspanels = true;
                    if ($panels == true) {
                        $responseJS = $component->AjaxAnswer();
                        WebApplication::$app->getResponse()->addAjaxResponse($responseJS);
                        continue;
                    }
                }

                if ($component instanceof AjaxRender) {
                    if ($component instanceof Panel) {
                        continue;
                    }
                    $responseJS = $component->AjaxAnswer();
                    WebApplication::$app->getResponse()->addAjaxResponse($responseJS);
                }
            }

            return $haspanels;
        }
    }
*/
    /**
     * @see HttpComponent
     *
     */
    public function Render() {
        if (strlen($this->_ankor) > 0) {
            WebApplication::$app->getResponse()->addJavaScript("window.location='#" . $this->_ankor . "'", true);
            $this->_ankor = '';
        }
        $this->beforeRender();


        //$this->updateTag();


        $this->RenderImpl();
        $this->afterRender();
        

        
        //адрес страницы без параметров
        $_baseurl =  $this->getURLNode()  ;
        $this->_tvars['_baseurl'] =     $_baseurl ;
        WebApplication::$app->addJavaScript(" window._baseurl= '{$_baseurl}'")  ;

    }
    /*
    public function getLoadedTag($id){
        if(isset($this->zarr[$id]))  return  pq($this->zarr[$id] );
        else return null;
    }

    public function updateTag(){
        $this->zarr = array();

        $z = pq('[zippy]') ;

        foreach($z->elements as $o){
            $a = "".$o->getAttribute('zippy');
            $this->zarr[$a] =  $o ;
        }
    }
    */
    /**
     * Добавляет  обработчик  на  событие  перед  обработкой  страницей  HTTP запроса
     * @param  $obj  Объект  регистрирующий  свою  функцию как   callback
     * @param  $func Имя функции - обработчика
     */
    public function addBeforeRequestEvent($obj, $func) {
        $this->beforeRequestEvents[] = new \Zippy\Event($obj, $func);
    }

    /**
     * Добавляет  обработчик  на  событие  после обработки страницей  HTTP запроса
     * @param  $obj  Объект  регистрирующий  свою  функцию как   callback
     * @param  $func Имя функции - обработчика
     */
    public function addAfterRequestEvent($obj, $func) {
        $this->afterRequestEvents[] = new \Zippy\Event($obj, $func);
    }

    /**
     * Вызывается  перед requestHandler
     *
     */
    public function beforeRequest() {
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
    public function afterRequest() {

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
    protected function goAnkor($name) {
        $this->_ankor = $name;
    }

    protected function setTitle($title) {
        $this->_title = $title;
    }

    protected function setDescription($description) {
        $this->_description = $description;
    }

    protected function setKeywords($keywords) {
        $this->_keywords = $keywords;
    }

    /**
     * функция  фонового  обновления  значения элемента  формы
     *
     * @param mixed $sender
     */
    public function OnBackgroundUpdate($sender) {

    }


    final public function RequestMethod($method) {
        $p =  WebApplication::$app->getRequest()->request_params[$method];
        $post=null;
        if($_SERVER["REQUEST_METHOD"]=='POST') {

            if(count($_POST)>0) {
                $post = $_POST;
            } else {
                $post =  file_get_contents('php://input');
            }
        }
        $answer = $this->{$method}($p, $post);
        if(strlen($answer)) {
            WebApplication::$app->getResponse()->addAjaxResponse($answer) ;
        }

    }

    /**
    * добавляет  яваскрипт  в  конец  ответа на ajax  запрос
    *
    * @param mixed $js
    */
    protected function addAjaxResponse($js) {
        if (strlen($js) > 0) {
            WebApplication::$app->getResponse()->addAjaxResponse($js);
        }
    }
   
 
    final public function updateAjaXBlocks($a) {
        $this->_ajaxblock = $a;
    
    }
   
    final public function updateAjaxHTML() {
         if($this->hasAB()) {
            
            foreach($this->_ajaxblock as $id) {
                $c = $this->getComponent($id,true) ;
                if($c ==null){
                    continue;
                }
                $html = $c->getHTML()  ;
                if($html == null){
                    continue;
                }
                $html = str_replace("'","`",$html)  ;
             //   $b = base64_encode($html) ;
                $b = json_encode($html, JSON_UNESCAPED_UNICODE);
                
           //     $js  = "var hh = JSON.parse('{$b}')  ";
                $js .= "$('#{$id}').replaceWith({$b})";
            //    $js .= "$('#{$id}').replaceWith('{$html}')";

             //  $js= "console.log('{$b}' )";
                
                $this->addAjaxResponse($js)   ;
                
            }
            
        }       
    }
   
    final public function hasAB() {
       return count($this->_ajaxblock);
    }
     
}

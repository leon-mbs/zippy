<?php

namespace Zippy;

use \Zippy\Html\HtmlContainer;

/**
 * Класс  приложения. Обеспечивает жизненный  цикл  страниц, управление  сессией
 * и обработку  HTTP запросов.
 * Для  использования необходимо отнаследоватся  и  переопределить,  как  минимум,
 * getTemplate().  
 * 
 */
abstract class WebApplication
{

        public static $context = array("currentform" => null, "formcount" => 0);
        private $currentpage = null;
        public static $app = null;
        private $macros = array();
        private $reloadPage = false;
        private $request;
        private $response;

        // public  $doc;
        /**
         * Конструктор
         * @param string Имя  класса  начальной  страницы
         */
        function __construct($homepage = null)
        {
                $this->homepage = $homepage;

                self::$app = $this;



                $this->request = new HttpRequest();
                $this->response = new HttpResponse();
        }

        public function __sleep()
        {
                //avoid serialization
        }

        /**
         * Возвращает экземпляр приложения
         * @return WebApplication
         */
        public static final function getApplication()
        {
                return self::$app;
        }

        /**
         * Возвращает HTML шаблон по  имени класса  страницы
         * перегружается   в  классе  пользовательского  приложения
         * @param  string Имя  класса  страницы
         * @param string  имя  вида страницы если  несколько
         */
        public abstract function getTemplate($name);

        /**
         * Возвращает  объект  текущей  страницы
         * @return WebPage
         */
        public final function getCurrentPage()
        {
                return $this->currentpage;
        }

        /**
         * Создает  новый  экземпляр  страницы  по  имени  класса  страницы
         * и делает  ее  текущей.  По  сути  серверный  редирект без  изменения  адресной   строки  браузера.
         * @param string Имя класса  страницы
         */
        public final function LoadPage($name, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
        {

                if (is_array($arg1) == false) {
                        //$this->currentpage = new $name($arg1, $arg2, $arg3, $arg4, $arg5);    
                        $arg1 = func_get_args();
                        $arg1 = array_slice($arg1, 1);
                }

                $classpage = new \ReflectionClass($name);
                $this->currentpage = $classpage->newInstanceArgs($arg1);


                /*   $cnt = count($arg1);
                  switch ($cnt) {
                  case 1: $this->currentpage = new $name($arg1[0]);
                  break;
                  case 2: $this->currentpage = new $name($arg1[0], $arg1[1]);
                  break;
                  case 3: $this->currentpage = new $name($arg1[0], $arg1[1], $arg1[2]);
                  break;
                  case 4: $this->currentpage = new $name($arg1[0], $arg1[1], $arg1[2], $arg1[3]);
                  break;
                  case 5: $this->currentpage = new $name($arg1[0], $arg1[1], $arg1[2], $arg1[3], $arg1[4]);
                  break;
                  default:
                  $this->currentpage = new $name();
                  }
                 */
                $this->currentpage->args = $arg1; //запоминаем аргументы страницы

                $this->response->setPageIndex($this->getPageManager()->putPage($this->currentpage));
        }

        /**
         * Основной   цикл  приложения
         */
        public final function Run()
        {
                self::$app = $this;

                if ($this->homepage == null) {
                        throw new \Zippy\Exception(ERROR_NOT_FOUND_HOMEPAGE);
                }

                if ($this->request->querytype == HttpRequest::QUERY_HOME) {
                        $this->LoadPage($this->homepage);
                }


                if ($this->request->querytype == HttpRequest::QUERY_PAGE) {
                        $this->LoadPage($this->request->request_page, $this->request->request_page_arg);
                }

                if ($this->request->querytype == HttpRequest::QUERY_SEF) {
                        $this->Route($this->request->uri);
                }

                if ($this->request->querytype == HttpRequest::QUERY_EVENT) {
                        //получаем  адресуемую  страницу
                        if (!is_numeric($this->request->getRequestIndex())) {
                                $this->response->to404Page();
                        }

                        $this->currentpage = $this->getPageManager()->getPage($this->request->getRequestIndex());
                        if ($this->currentpage == null) {
                                //$this->response->to404Page();
                                $this->LoadPage($this->request->request_page);
                        }

                        $this->response->setPageIndex($this->getRequest()->getRequestIndex());

                        if ($this->currentpage instanceof \Zippy\Html\WebPage == false) {
                                $this->response->toIndexPage();
                                $this->response->output();
                                return;
                        }

                        $this->currentpage->RequestHandle();

                        if ($this->request->isAjaxRequest() || $this->request->isBinaryRequest()) {
                                // если  Ajax запрос, отдаем  в  выходной  поток  только  ответ
                                // адресуемого  елемента
                                // $this->output = $this->currentpage->getAjaxAnswer();

                                $this->getPageManager()->updatePage($this->currentpage, $this->getRequest()->getRequestIndex());
                        } else {

                                $this->response->setPageIndex($this->getPageManager()->putPage($this->currentpage));

                                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                                        //получаем  новую  версию  страницы

                                        if ($this->reloadPage == true) { //если  надо  сбросить адресную строку
                                                $this->response->toBaseUrl();
                                        }

                                        //
                                }
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        // обновляем  страницу  в  сессии  не   меняю  версию
                                        //  WebSession::getSession()->getPageManager()->updatePage($this->currentpage, $request[0]);
                                        //$this->Render();   
                                        //  $this->_saveSession();
                                        $this->response->toBaseUrl();
                                }
                        }
                }



                if ($this->currentpage == null) {
                        $this->response->to404Page();
                }

                if ($this->response->isRedirect() != true) {
                        $this->Render();
                }




                $this->response->output();
        }

        /**
         * Метод,  выполняющий   формирование  выходного  HTML потока
         * на  основе  текущено  шаблона  и  данных  елементов  страницы
         */
        private final function Render()
        {
                if ($this->request->isBinaryRequest())
                        return;

                $renderpage = $this->currentpage;

                if ($this->request->isAjaxRequest()) {
                        $renderpage->renderAjax();
                        return;
                }



                //загружаем  соответсвующий  шаблон
                $template = $this->getTemplate(get_class($renderpage), $renderpage->layout);
                foreach ($this->macros as $name => $value) {
                        $template = str_ireplace("{" . $name . "}", $value, $template);
                }


                $doc = \phpQuery::newDocumentHTML($template);

                $basepage = get_parent_class($renderpage);
                // если  страница  не  базовая   а  дочерняя     
                if ($basepage !== "Zippy\\Html\\WebPage") {

                        $basetemplate = WebApplication::getApplication()->getTemplate($basepage, $renderpage->layout);
                        foreach ($this->macros as $name => $value) {
                                $basetemplate = str_ireplace("{" . $name . "}", $value, $basetemplate);
                        }

                        $bdoc = \phpQuery::newDocumentHTML($basetemplate);
                        //восстанавливаем  по умолчанию


                        $bdoc["childpage"]->replaceWith($doc['body']->html());

                        $links = $doc['head  > link'];
                        foreach ($links as $l) {
                                pq('head')->append($l);
                        }

                        $script = $doc['head  > script'];
                        foreach ($script as $sc) {
                                pq('head')->append($sc);
                        }
                        $script = $doc['head  > title'];
                        foreach ($script as $sc) {
                                $bdoc['title']->remove();
                                pq('head')->append($sc);
                        }
                        $script = $doc['head  > meta'];
                        foreach ($script as $sc) {
                                pq('head')->append($sc);
                        }


                        /*
                          if (strlen($title) > 0) {
                          $bdoc['description']->remove();
                          pq('head')->append("<description>{$description}</title>");
                          }
                          if (strlen($title) > 0) {
                          $bdoc['title']->remove();
                          pq('head')->append("<title>{$title}</title>");
                          } */
                }

                $renderpage->Render();


                if (strlen($renderpage->_title) > 0) {
                        pq('title')->text($renderpage->_title);
                }
                if (strlen($renderpage->_keywords) > 0) {
                        pq('keywords')->text($renderpage->_keywords);
                }
                if (strlen($renderpage->_description) > 0) {
                        pq('description')->text($renderpage->_description);
                }



                $response = '<!DOCTYPE HTML>' . pq('html')->htmlOuter(); //HTML  в  выходной  поток

                $this->response->setContent($response);
        }

        /**
         * Добавляет  макрос  в  список на   уровне  прилождения
         * Макросы - переменные   в  фигурных  скобках, используются  для  вставки  изменяемых  частей  шаблона
         * не   связанных  с  HTML  компонентами
         * @param  string  Имя макроса
         * @param string Значение
         */
        public final function setMacros($name, $value)
        {
                $this->macros[$name] = $value;
        }

        /**
         *  Метод  выполняющий   роутинг URL запросов.  Например  для ЧПУ ссылок
         *  Переопределяется   в  пользовательском  приложении. Как  правило,  выполняет  редирект на   соответствующую  страницу
         *  @param string  Входящий  запрос
         */
        protected function Route($uri)
        {
                
        }

        /**
         *  Указывает  что  текущая  страница  должна  перегрузиться   с  помощью
         * клиентского редиректа для сброса  адресной  строки
         * @param boolean
         */
        public final function setReloadPage($value = true)
        {
                $this->reloadPage = $value;
        }

        /**
         * Возвращает  оьхект  HttpRequest
         * @return HttpRequest
         */
        public final function getRequest()
        {
                return $this->request;
        }

        /**
         * возвращает  объект Httpresponse
         * @return HttpResponse
         */
        public final function getResponse()
        {
                return $this->response;
        }

        /**
         * Возвращает  менеджер  страниц
         * @return PageManager
         */
        protected function getPageManager()
        {
                if (!isset($_SESSION['zippy_pagemanager'])) {
                        $_SESSION['zippy_pagemanager'] = new PageManager();
                }
                return $_SESSION['zippy_pagemanager'];
        }

        /**
         * Устанавливает  адрес  страницы 404
         *        
         * @param mixed $url
         */
        public function set404($url)
        {
                $this->getResponse()->page404 = $url;
        }

}


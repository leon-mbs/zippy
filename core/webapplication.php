<?php

namespace Zippy;

use Zippy\Html\HtmlContainer;

/**
 * Класс  приложения. Обеспечивает жизненный  цикл  страниц, управление  сессией
 * и обработку  HTTP запросов.
 * Для  использования необходимо отнаследоватся  и  переопределить,  как  минимум,
 * getTemplate().
 *
 */
abstract class WebApplication
{
    private $currentpage = null;
    public static $app = null;
    public static $dom = null;

    private $reloadPage = false;
    private $request;
    private $response;


    // public  $doc;

    /**
     * Конструктор
     */
    public function __construct() {


        self::$app = $this;


        $this->request = new HttpRequest();
        $this->response = new HttpResponse();
    }

    public function __sleep()  {
        //avoid serialization
         
    }

    /**
     * Возвращает экземпляр приложения
     * @return WebApplication
     */
    final public static function getApplication() :WebApplication {
        return self::$app;
    }

    /**
     * Возвращает HTML шаблон по  имени класса  страницы
     * перегружается   в  классе  пользовательского  приложения
     * @param string $name Имя  класса  страницы
     */
    abstract public function getTemplate($name);


    /**
     * Возвращает  объект  текущей  страницы
     * @return \Zippy\Html\WebPage
     */
    final public function getCurrentPage() :\Zippy\Html\WebPage {
        return $this->currentpage;
    }

    /**
     * Создает  новый  экземпляр  страницы  по  имени  класса  страницы
     * и делает  ее  текущей.  По  сути  серверный  редирект без  изменения  адресной   строки  браузера.
     * @param string $name Имя класса  страницы
     */
    final public function LoadPage($name, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null) {

        if (is_array($arg1) == false) {
            //$this->currentpage = new $name($arg1, $arg2, $arg3, $arg4, $arg5);
            $arg1 = func_get_args();
            $arg1 = array_slice($arg1, 1);
        }
        $name=  $res = str_ireplace( array( '\'', '"',    '<', '>' ), '', $name);


        $classpage = new \ReflectionClass($name);
        $this->currentpage = $classpage->newInstanceArgs($arg1);


        $this->currentpage->args = $arg1; //запоминаем аргументы страницы

        $this->response->setPageIndex($this->getPageManager()->updatePage($this->currentpage));
    }

    /**
     * Основной   цикл  приложения
     */
    final public function Run($homepage) {
        self::$app = $this;

        if ($homepage == null) {
            throw new \Zippy\Exception(ERROR_NOT_FOUND_HOMEPAGE);
        }

        if ($this->request->querytype == HttpRequest::QUERY_INVALID) {
            //self::Redirect404();
            self::$app->getResponse()->toIndexPage();
            self::$app->getResponse()->output();
            die;
        }

        if ($this->request->querytype == HttpRequest::QUERY_HOME) {
            $this->LoadPage($homepage);
        }

        if ($this->request->querytype == HttpRequest::QUERY_PAGE) {
            $this->LoadPage($this->request->request_page, $this->request->request_page_arg);
        }

        if ($this->request->querytype == HttpRequest::QUERY_SEF) {
            $this->currentpage = null;

            $this->Route($this->request->uri);


        }

        if ($this->request->querytype == HttpRequest::QUERY_EVENT) {
            //получаем  адресуемую  страницу
            if (strlen($this->request->getRequestIndex())==0) {
                $this->response->to404Page();
            }

            $this->currentpage = $this->getPageManager()->getPage($this->request->getRequestIndex());
            if ($this->currentpage == null) {
                if (strlen($this->request->request_page) > 2) {
                    $this->LoadPage($this->request->request_page);
                } else {
                    $this->currentpage = $this->getPageManager()->getLastPage();
                }
            }

            $this->response->setPageIndex($this->getRequest()->getRequestIndex());

            if ($this->currentpage instanceof \Zippy\Html\WebPage == false) {
                $this->response->toIndexPage();
                $this->response->output();
                return;
            }
            if ($this->request->querytype == HttpRequest::QUERY_EVENT) {
                $this->currentpage->RequestHandle();
            }



            if ($this->request->isAjaxRequest() || $this->request->isBinaryRequest()) {
                // если  Ajax запрос, отдаем  в  выходной  поток  только  ответ
                // адресуемого  елемента
                // $this->output = $this->currentpage->getAjaxAnswer();

                $this->getPageManager()->updatePage($this->currentpage);
            } else {

                $this->response->setPageIndex($this->getPageManager()->updatePage($this->currentpage));
                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    if ($this->reloadPage == true) { //если  надо  сбросить адресную строку
                        $this->response->toBaseUrl();
                    }
                }
                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    //  $this->response->toBaseUrl();
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
    private function Render() {
        if ($this->request->isBinaryRequest()) {
            return;
        }

         $renderpage = $this->currentpage;

        if ($this->request->isAjaxRequest()  && !$this->currentpage->hasAB()) {
           // если нет  ajax blocks
            return;
        }
 
        //загружаем  соответсвующий  шаблон
        $template = $this->getTemplate(get_class($renderpage));
 
  
        self::$dom =  (new \DOMWrap\Document())->html($template);

        
        $basepage = get_parent_class($renderpage);
        // если  страница  не  базовая   а  дочерняя
        if ($basepage !== "Zippy\\Html\\WebPage") {

            $basetemplate = WebApplication::getApplication()->getTemplate($basepage);
            

            $body = self::$dom->find('body')->html();   

           
            $basetemplate= str_replace('<childpage/>', $body, $basetemplate) ;

 
            $bdom = (new \DOMWrap\Document())->html($basetemplate);
    
            $bhead=$bdom->find('head');

            $links = self::$dom->find('head  > link') ;
            foreach ($links as $l) {
                $bhead->appendWith($l);
            }           
            $scripts = self::$dom->find('head  > script') ;
            foreach ($scripts as $s) {
                $bhead->appendWith($s);
            }           
            $metas = self::$dom->find('head  > meta') ;
            foreach ($metas as $m) {
                $bhead->appendWith($m);
            }           
            $title = self::$dom->find('head  > title') ;
            foreach ($title as $t) {
                $bhead->find('title')->destroy();
                $bhead->prependWith($t);
            }           
            self::$dom =  $bdom;
        }

        $renderpage->Render();
    
        $head=self::$dom->find('head');
        //тэги должны  уже  быть
        if (strlen($renderpage->_title) > 0) {
            $head->find('title')->text($renderpage->_title);
        }
        if (strlen($renderpage->_keywords) > 0) {
            $head->find('meta[name="keywords"]')->attr('content', $renderpage->_keywords);;
        }   
        if (strlen($renderpage->_description) > 0) {
            $head->find('meta[name="description"]')->attr('content', $renderpage->_description);;
        }

        $response= self::$dom->html();
    //    file_put_contents('z:/r2.html',$response) ;

        if (count($renderpage->_tvars) > 0) {

            //восстанавливаем  скобки Mustache в тегах
            $response = str_replace("%7B%7B", "{{", $response);
            $response = str_replace("%7D%7D", "}}", $response);

            $m = new \Mustache_Engine();
            $response = $m->render($response, $renderpage->_tvars);
        }
    
        if ($this->request->isAjaxRequest()) {
            // если  ajax blocks
              
           //  \phpQuery::newDocumentHTML($response);
             $renderpage->updateAjaxHTML()  ;
              
             return;
           
        }
     
        $this->response->setContent($response);
    }


    /**
     *  Метод  выполняющий   роутинг URL запросов.  Например  для ЧПУ ссылок
     *  Переопределяется   в  пользовательском  приложении.
     *  Загружает страницу (с  параметрами) методом $this->LoadPage
     * @param string $uri Входящий  запрос
     */
    protected function Route($uri) {

    }

    /**
     *  Указывает  что  текущая  страница  должна  перегрузиться   с  помощью
     * клиентского редиректа для сброса  адресной  строки
     * @param mixed  $value
     */
    final public function setReloadPage($value = true) {
        $this->reloadPage = $value;
    }

    /**
     * Возвращает  объект  HttpRequest
     * @return HttpRequest
     */
    final public function getRequest() :HttpRequest{
        return $this->request;
    }

    /**
     * возвращает  объект Httpresponse
     * @return HttpResponse
     */
    final public function getResponse() :HttpResponse{
        return $this->response;
    }

    /**
     * Возвращает  менеджер  страниц
     * @return PageManager
     */
    protected function getPageManager() :PageManager{
        if (!isset($_SESSION['zippy_pagemanager'])) {
            $_SESSION['zippy_pagemanager'] = new PageManager();
        }
        return $_SESSION['zippy_pagemanager'];
    }


    /**
     * возвращает имя класса предыдущей страницы
     *
     */
    public function getPrevPage() {
        return $this->getPageManager()->getPrevPage();
    }

    /**
     * Редирект на  страницу 404
     *
     */
    public static function Redirect404() {
        self::$app->getResponse()->to404Page();
    }

    /**
     * Редирект на  предыдущую страницу
     *
     */
    public static function RedirectBack() {
        $pagename = self::$app->getPageManager()->getPrevPage();
        $pagename = '\\' . rtrim($pagename, '\\');

        if ($pagename == "\\") {
            self::$app->response->toIndexPage();
            return;
        }
        self::$app->response->Redirect($pagename, array());
    }

    /**
     * Вызывается  перед обработкой  запроса
     * Перегружается  в  приложении
     * если  есть префикс (например указан  язык)
     * возвращает массив  с  элементами:
     * префикс
     * uri без префикса
     * @param mixed $uri
     */
    public function beforeRequest($uri) {
        return null;
    }

    /**
     * Выполняет клиентский редирект  на  страницу
     *
     * @param mixed $page Имя класса  страницы
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     * @param mixed $arg4
     * @param mixed $arg5
     */
    public static function Redirect($page, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null) {
        if (self::$app instanceof WebApplication) {
            self::$app->getResponse()->Redirect($page, $arg1, $arg2, $arg3, $arg4, $arg5);
        }
    }

    /**
     * Редирект на  домашнюю  страницу
     *
     */
    public static function RedirectHome() {
        self::$app->getResponse()->toIndexPage();
    }


    /**
     * Вставляет  JavaScript  в  конец   выходного  потока
     * @param string $js Код  скрипта
     * @param mixed $docready Если  true  - вставка  после  загрузки  документа в  браузер
     */
    public static function addJavaScript($js, $docready = false) {
        return self::$app->getResponse()->addJavaScript($js, $docready);
    }

    /**
     * редирект по URL
     *
     * @param mixed $uri
     */
    public static function RedirectURI($uri) {
        self::$app->getResponse()->toPage($uri);
    }

}

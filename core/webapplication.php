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

    private $currentpage = null;
    public static $app = null;
    private $macros = array();
    private $reloadPage = false;
    private $request;
    private $response;
    private $routes = array(); 

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

        if ($this->request->querytype == HttpRequest::QUERY_REST) {
                
               $uarr = explode("/",$this->request->uri) ;
                   foreach($this->routes as $k=>$v){
                    
                    
                    if($uarr[0] ==ltrim($k,'/')){
                        $pr = "\\". ltrim($v,"\\");
                       
                        $classapi = new $v();
                         $uarr = array_slice($uarr, 1);
                        if($classapi instanceof \Zippy\RestFull){
                            $classapi->Execute(count($uarr)>0 ? $uarr[0] : 0);
                            
                            die;
                        }
                        break;
                    }
                }            
                //$classapi = new \ReflectionClass($uarr[0]);
                
                 
     
                        
        }
        if ($this->request->querytype == HttpRequest::QUERY_SEF) {
            $this->currentpage = null;
            
            $uri = ltrim($this->request->uri,'/');
            $uarr = explode("/",$uri) ;
            
             
                foreach($this->routes as $k=>$v){
                    
                    
                    if($uarr[0] ==ltrim($k,'/')){
                        $pr = "\\". ltrim($v,"\\");
                        $uarr = array_slice($uarr, 1);
                        $this->LoadPage($pr, $uarr);
       
                        break;
                    }
                }
           
           
            
            if($this->currentpage == null){
                $this->Route($this->request->uri);    
            }
             
            
        }

        if ($this->request->querytype == HttpRequest::QUERY_EVENT) {
            //получаем  адресуемую  страницу
            if (!is_numeric($this->request->getRequestIndex())) {
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

        

           if(false==$renderpage->renderAjax())  return;

            
        }



        //загружаем  соответсвующий  шаблон
        $template = $this->getTemplate(get_class($renderpage), $renderpage->layout);





        $doc = \phpQuery::newDocumentHTML($template);

        $basepage = get_parent_class($renderpage);
        // если  страница  не  базовая   а  дочерняя
        if ($basepage !== "Zippy\\Html\\WebPage") {

            $basetemplate = WebApplication::getApplication()->getTemplate($basepage, $renderpage->layout);
            // if(count($renderpage->_tvars) >0){
            //    $m = new \Mustache_Engine();
            //   $basetemplate= $m->render($basetemplate, $renderpage->_tvars);
            //  }


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
            pq('meta[name="keywords"]')->attr('content', $renderpage->_keywords);
        }
        if (strlen($renderpage->_description) > 0) {
            pq('meta[name="description"]')->attr('content', $renderpage->_description);
        }



        $response = '<!DOCTYPE HTML>' . pq('html')->htmlOuter(); //HTML  в  выходной  поток

        if (count($renderpage->_tvars) > 0) {

            //востанавливаем  скобки  в тегах
            $response = str_replace("\"%7B%7B", "\"{{", $response);
            $response = str_replace("%7D%7D\"", "}}\"", $response);

            $m = new \Mustache_Engine();
            $response = $m->render($response, $renderpage->_tvars);
        }

        if ($this->request->isAjaxRequest()) {
            \phpQuery::newDocumentHTML($response);

            $renderpage->renderAjax(true);
            return;
        }
        foreach ($this->macros as $name => $value) {
            $response = str_ireplace("{" . $name . "}", $value, $response);
        }
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
     * Возвращает  объект  HttpRequest
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

    /**
     * возвращает имя класса предыдущей страницы
     *
     */
    public function getPrevPage()
    {
        return $this->getPageManager()->getPrevPage();
    }

    /**
     * Редирект на  страницу 404
     *
     */
    public static function Redirect404()
    {
        self::$app->getResponse()->to404Page();
    }

    /**
     * Редирект на  предыдущую страницу
     *
     */
    public static function RedirectBack()
    {
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
    public function beforeRequest($uri)
    {
        return null;
    }

    /**
     * Выполняет клиентский редирект  на  страницу
     *
     * @param mixed $page     Имя класса  страницы
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     * @param mixed $arg4
     * @param mixed $arg5
     */
    public static function Redirect($page, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        if (self::$app instanceof WebApplication) {
            self::$app->getResponse()->Redirect($page, $arg1, $arg2, $arg3, $arg4, $arg5);
        }
    }

    /**
     * Редирект на  домашнюю  страницу
     *
     */
    public static function RedirectHome()
    {
        self::$app->getResponse()->toIndexPage();
    }

    /**
    * Добавляет записи для роутера в  виде массива
    * ключ - URI, значение  текстовое представлеие  класса  страницы
    * значения следующие через прямой слэш - параметры котрые пережаются в конструктор страницы
    * И соответственно  должны  ьыть там  обьявлены
    * Например
    * add Route(array('user'=>"\\App\\User")):
    * 
    * при запросе /user/2 будет созднала страница new \App\User(2);
    * 
    * @param mixed $r   массив пар  для роутинга
    */
    public function addRoute(  $r)  {
          if(is_array($r)){
             $this->routes = array_merge_recursive($this->routes,$r);         
          }
     
    }
    
}

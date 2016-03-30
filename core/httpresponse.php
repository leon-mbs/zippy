<?php

namespace Zippy;

/**
 * Класс формирующий  ответ  клиенту (браузеру). Создается   приложеем.
 */
class HttpResponse
{

    private $content;
    //  private $ajaxanswer = "";
    public $JSrender = "";
    public $JSrenderDocReady = "";
    private $pageindex;
    public $binaryanswer;
    private $gzip = false;
    private $redirect = "";
    public $page404 = null;

    public function __construct()
    {
        //$this->setRequestIndex(WebApplication::$app->getRequest()->getRequestIndex());
    }

    /**
     * Формирует  выходной  поток  данных
     * @return string
     */
    public function output()
    {
        if (strlen($this->redirect) > 0) {
            header("Location: " . $this->redirect);
           // echo "<script>window.location='{$this->redirect}'</script>";
            die;
        }
        if (WebApplication::$app->getRequest()->isBinaryRequest() == true) {
            // вывод  осуществляет  адресуемый   компонент
            return;
        }

        Header("Content-Type: text/html;charset=UTF-8");
        Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        Header("Cache-Control: no-store,no-cache, must-revalidate");
        Header("Pragma: no-cache");
        Header("Last-Modified: Mon, 26 Jul 2100 05:00:00 GMT");

        if (WebApplication::$app->getRequest()->isAjaxRequest() == true) {
            echo $this->content;
            return;
        }

        if (strpos($this->content, "</head>") > 0) {
            $this->content = str_replace("</head>", $this->getJS() . "</head>", $this->content);
        } else {
            $this->content = $this->content . $this->getJS();
        }

        if (WebApplication::$app->getRequest()->isAjaxRequest() == false && $this->gzip === true && strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") !== false) {
            Header("Content-Encoding: gzip");
            echo gzencode($this->content . $this->getJS());
            return;
        }
        echo $this->content;
    }

    /**
     * редирект на  главную  страницу
     *
     */
    public function toIndexPage()
    {
        $this->redirect = "/";
        // header("Location: /index.php");
        //  die;
    }

    /**
     * Редирект непосредственно  по  адресу
     *
     */
    public function toPage($url)
    {
        $this->redirect = $url;
    }

    /**
     * редирект на текущую  страницу без  параметров
     * (применяется  для "сброса" адресной  строки
     *
     */
    public function toBaseUrl()
    {
        if (strlen($this->redirect) == 0) { //если  не  было редиректа
            $this->redirect = $this->getBaseUrl();
        }
    }

    /**
     * Добавляет  JavaScript код для  AJAX ответа
     *
     * @param mixed $js
     */
    public function addAjaxResponse($js)
    {

        $this->content .= ( $js . "\n");
    }

    /**
     * Вставляет  JavaScript  в  конец   выходного  потока
     * @param string  Код  скрипта
     * @param  boolean Если  true  - вставка  после  загрузки  документа в  браузер
     */
    public function addJavaScript($js, $docready = false)
    {

        if ($docready === true) {
            $this->JSrenderDocReady .= ( $js . "\n");
        } else {
            $this->JSrender .= ( $js . "\n");
        }
    }

    /**
     * Возвращает  результирующий  JavaScript  код  при  формировании  выходного  HTML  потока
     * @return  string JavaScript  код
     */
    private function getJS()
    {

        if (strlen($this->JSrenderDocReady . $this->JSrender) == 0) {
            return "";
        }

        $js = "
                    <script type=\"text/javascript\">

                    ";
        if (strlen($this->JSrenderDocReady) > 0) {
            $js .= "   $(document).ready(function()
                        {
                        {$this->JSrenderDocReady}
                        } )
                      ";
        }
        $js .= $this->JSrender . "


                    </script>"
        ;

        return $js;
    }

    /**
     *  Возвращает для  дочерних елементов базовую  часть  URL с  номером и  версией страницы
     * @return string
     */
    public final function getBaseUrl()
    {
        //$pagename = get_class(WebApplication::$app->getCurrentPage());
        //$pagename = str_replace("\\", "/", $pagename);
        //return $this->getHostUrl() . "/?q=" . $pagename . ":" . $this->pageindex;
        return $this->getHostUrl() . "/?q=p:" . $this->pageindex;
    }

    /**
     * Создает  новый  экземпляр  страницы  по  имени  класса  страницы
     * делает  ее  текущей и  перенаправляет  на  нее  HTTP запрос с  клиента
     * (клиентский   редирект, сбрасывающий   адресную  строку  браузера).
     * @param string Имя класса  страницы
     * @param array массив параметров страницы
     *
     */
    public final function Redirect($name, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        if ($name instanceof \Zippy\Html\WebPage) {
            $name = get_class($name);
        }

        WebApplication::$app->LoadPage($name, $arg1, $arg2, $arg3, $arg4, $arg5);
        //   $url = "/?q=" . $this->getPageManager()->session->putPage($this->currentpage) . "::1";
        //   $this->saveSession(serialize($this->session));
        $this->toBaseUrl($name);
        $this->output();
    }

    public final function setContent($content)
    {
        $this->content = $content;
    }

    /**
     *
     * @return string
     */
    public final function getHostUrl()
    {
        $http = empty($_SERVER["HTTPS"]) ? 'http' : 'https';

        $url =  $http . "://" . $_SERVER["HTTP_HOST"];
        $prefix = WebApplication::$app->getRequest()->getPrefix();
        $url = strlen($prefix) > 0 ? $url . '/' . $prefix  : $url;
        return $url;
    }

    public final function setPageIndex($index)
    {
        $this->pageindex = $index;
    }

    public final function setGzip($gzip)
    {
        $this->gzip = $gzip;
    }

    public final function isRedirect()
    {
        return strlen($this->redirect) > 0;
    }

    /**
     * Перенаправляет на  страницу 404
     *
     */
    public function to404Page()
    {
        if ($this->page404 == null) {
            header("HTTP/1.0 404 Not Found");
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
        } else {
            header("Location: /" . ltrim($this->page404, '/'));
        };
        die;
    }

    /**
     *  редирект на  предыдущую  страницу
     */
     /*
    public final function toBack()
    {
        $pagename = WebApplication::$app->getPrevPage();

        // $pagename = str_replace("\\", "/", $pagename);
        $pagename = '\\' . rtrim($pagename, '\\');
        //$this->redirect = $this->getHostUrl() . "/?q=" . $pagename . ":" . $this->pageindex--;
        if($pagename== "\\") {
            $this->toIndexPage();
            return;
        }
        $this->Redirect($pagename, array());
    }
*/



}

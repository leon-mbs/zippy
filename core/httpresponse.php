<?php

namespace Zippy;

/**
 * Класс формирующий  ответ  клиенту (браузеру). Создается   приложеем.
 */
class HttpResponse
{
    private $content;
    //  private $ajaxanswer = "";
    public $SSRrender = "";
    public $JSrender = "";
    public $JSrenderDocReady = "";
    private $pageindex;
    public $binaryanswer;
    private $gzip = false;
    private $redirect = "";
    public $page404 = null;

    public function __construct() {
        //$this->setRequestIndex(WebApplication::$app->getRequest()->getRequestIndex())

    }

    /**
     * Формирует  выходной  поток  данных
     * @return string
     */
    public function output() {
        if (strlen($this->redirect) > 0) {
            header("Location: " . $this->redirect);
            // echo "<script>window.location='{$this->redirect}'</script>";
            die;
        }
        if (WebApplication::$app->getRequest()->isBinaryRequest() == true) {
            // вывод  осуществляет  адресуемый   компонент
            return;
        }

        Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        Header("Cache-Control: no-store,no-cache, must-revalidate");
        Header("Pragma: no-cache");
        Header("Last-Modified: Mon, 26 Jul 2100 05:00:00 GMT");

        if (WebApplication::$app->getRequest()->isAjaxRequest() == true) {
            $this->content = trim($this->content) ;
            if(strpos($this->content,"{")===0)  {
               Header("Content-Type: application/json;charset=UTF-8");
            }   else {
               Header("Content-Type: text/javascript;charset=UTF-8");
            }
            echo $this->content;
            return;
        }
        Header("Content-Type: text/html;charset=UTF-8");

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
    public function toIndexPage() {
        $this->redirect = "/";
        // header("Location: /index.php");
        //  die;
    }

    /**
     * Редирект непосредственно  по  адресу
     *
     */
    public function toPage($url) {
        $this->redirect = $url;
    }

    /**
     * редирект на текущую  страницу без  параметров
     * (применяется  для "сброса" адресной  строки
     *
     */
    public function toBaseUrl() {
        if (strlen($this->redirect) == 0) { //если  не  было редиректа
            $this->redirect = $this->getBaseUrl();
        }
    }

    /**
     * Добавляет  JavaScript код для  AJAX ответа
     *
     * @param mixed $js
     */
    public function addAjaxResponse($js) {

        $this->content .= ($js . " \n");
    }

    /**
     * Вставляет  JavaScript  в  конец   выходного  потока страницы
     * @param string  Код  скрипта
     * @param boolean Если  true  - вставка  после  загрузки (onload) документа в  браузер
     */
    public function addJavaScript($js, $docready = false) {

        if ($docready === true) {
            $this->JSrenderDocReady .= ($js . " \n");
        } else {
            $this->JSrender .= ($js . " \n");
        }
    }

    /**
     * Возвращает  результирующий  JavaScript  код  при  формировании  выходного  HTML  потока
     * @return  string JavaScript  код
     */
    private function getJS() {

        if (strlen($this->JSrenderDocReady . $this->JSrender) == 0) {
            return "";
        }

        $js = "
                    <script type=\"text/javascript\">
                    //сгенерировано  фреймворком
                    ";
        if (strlen($this->JSrenderDocReady) > 0) {
            $js .= "   $(document).ready(function()
                        {
                        {$this->JSrenderDocReady}
                        } )
                      ";
        }
        $js .= $this->JSrender . "

                     //сгенерировано  фреймворком
                    </script>";

        return $js;
    }

    /**
     *  Возвращает для  дочерних елементов базовую  часть  URL с  номером и  версией страницы
     * @return string
     */
    final public function getBaseUrl() {
        $pagename = get_class(WebApplication::$app->getCurrentPage());
        $pagename = str_replace("\\", "/", $pagename);
        //  return $this->getHostUrl() . "/index.php?q=p:" . $pagename  ;
        return  "/index.php?q=p:" . $pagename  ;

    }

    /**
     * Создает  новый  экземпляр  страницы  по  имени  класса  страницы
     * делает  ее  текущей и  перенаправляет  на  нее  HTTP запрос с  клиента
     * (клиентский   редирект, сбрасывающий   адресную  строку  браузера).
     * @param string Имя класса  страницы
     * @param array массив параметров страницы
     *
     */
    final public function Redirect($name, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null) {
        if ($name instanceof \Zippy\Html\WebPage) {
            $name = get_class($name);
        }

        WebApplication::$app->LoadPage($name, $arg1, $arg2, $arg3, $arg4, $arg5);
        //   $url = "/index.php?q=" . $this->getPageManager()->session->putPage($this->currentpage) . "::1";
        //   $this->saveSession(serialize($this->session));
        $this->toBaseUrl();
        $this->output();
    }

    final public function setContent($content) {
        $this->content = $content;
    }

    /**
     *
     * @return string
     */
    final public function getHostUrl() {
        $http = 'http';
        if (isset($_SERVER['HTTPS']) &&  strtolower($_SERVER['HTTPS']) !== 'off') {
            $http = 'https';
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $http = 'https';
        } elseif(443 == intval($_SERVER['SERVER_PORT'])) {
            $http = 'https';
        }

        $url = $http . "://" . $_SERVER["HTTP_HOST"];
        return $url;
    }

    final public function setPageIndex($index) {
        $this->pageindex = $index;
    }

    /**
    * сжимать данные  для  браузера
    * 
    * @param mixed $gzip true false
    */
    final public function setGzip($gzip) {
        $this->gzip = $gzip;
    }

    final public function isRedirect() {
        return strlen($this->redirect) > 0;
    }

    /**
     * Перенаправляет на  страницу 404
     *
     */
    public function to404Page() {

        header("HTTP/1.0 404 Not Found");

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
      //$this->redirect = $this->getHostUrl() . "/index.php?q=" . $pagename . ":" . $this->pageindex--;
      if($pagename== "\\") {
      $this->toIndexPage();
      return;
      }
      $this->Redirect($pagename, array());
      }
     */
}

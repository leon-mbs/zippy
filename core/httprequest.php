<?php

namespace Zippy;

/**
 * Класс выполняющий обработку  HTTP запроса. Создается приложением
 */
class HttpRequest
{
    public const QUERY_HOME    = 0;
    public const QUERY_EVENT   = 1;
    public const QUERY_PAGE    = 2;
    public const QUERY_SEF     = 3;
    public const QUERY_INVALID = 4;



    private $request;
    public $request_c;
    public $request_params;
    public $request_page;
    public $request_page_arg = array();
    public $uri;

    public $querytype = self::QUERY_INVALID;
    private $pageindex = 0;

    /**
     * Конструктор.  Выполняет  парсинг  HTTP запроса
     *
     */
    public function __construct() {
        $uri = $_SERVER["REQUEST_URI"];


        // основной  тип URI генерируемый  компонентами  фреймворка
        if (isset($_REQUEST["q"])) {

            $this->querytype = self::QUERY_EVENT;
            $this->request = explode("::", $_REQUEST["q"]);
            if (count($this->request) < 1) {
                throw new \Zippy\Exception(ERROR_INVALID_VERSION);
            }

            $arr = explode(':', $this->request[0]);

            $this->pageindex = $arr[1];
            $this->request_page = "\\" . ltrim(str_replace("/", "\\", $arr[0]), "\\");

            $this->request_params = array();
            $this->request_c = array("page");
            foreach (array_slice($this->request, 1) as $cid) {
                $arr = explode(':', $cid); //id  компонента
                $this->request_params[$arr[0]] = array_slice($arr, 1);
                $this->request_c[] = $arr[0];
            }


            return;
        }

        // URI формируемый  RedirectLink с  параметром  bookmarkable и кодированием
        if (isset($_REQUEST["r"])) {
            $this->querytype = self::QUERY_PAGE;
            $p = unserialize(trim(base64_decode($_REQUEST["r"])));

            $this->request_page = $p[0];
            $this->request_page_arg = $p[1];
            return;
        }
        // URI с  именем  класса  страницы и параметрами
        //пример  - переход на  страницу с  классом  news : /index.php?p=mycms/news&arg=2/4

        if (isset($_REQUEST["p"])) {
            $this->querytype = self::QUERY_PAGE;
            $this->request_page = "\\" . ltrim(str_replace("/", "\\", $_REQUEST["p"]), "\\");
            $this->request_page_arg = isset($_REQUEST["arg"]) ? explode('/', trim($_REQUEST["arg"], '/')) : array();
            return;
        }
        // URI формируемый  BookmarkableLink (в частности, ЧПУ)

        if (strlen($uri) > 1 && strpos($uri, '/?') === false && strpos($uri, '/index.php') === false) {
            $p = strpos($uri, '?');
            if ($p > 0) {  //отсекаем приклееное  фейсбуком и прочими
                $uri = substr($uri, 0, $p);
            }
            if (preg_match('/^[-#a-zA-Z0-9\/_]+$/', $uri)) {
                $this->querytype = self::QUERY_SEF;
                $this->uri = ltrim($uri, '/');
            }
        }
        if (strpos($uri, 'index.php') > 0) {
            $this->querytype = self::QUERY_HOME;
        }
        $uri = ltrim($uri, '/');
        if ($uri == "") {
            $this->querytype = self::QUERY_HOME;
        }

    }

    /**
     * Возвращает  индекс  страницы
     * @return int
     */
    public function getRequestIndex() {
        return  str_replace("/", "\\", $this->pageindex);
    }

    /**
     * Проверка  был  ли запрос AJAX запросом
     *
     */
    public function isAjaxRequest() {
        return isset($_REQUEST["ajax"]);
    }

    /**
     * Проверка  был  ли запрос запросом бинарного контента
     *
     */
    public function isBinaryRequest() {
        return isset($_REQUEST["binary"]);
    }



}

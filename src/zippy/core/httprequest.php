<?php

namespace Zippy;

/**
 * Класс выполняющий обработку  HTTP запроса. Создается приложением
 */
class HttpRequest
{

    const QUERY_HOME = 0;
    const QUERY_EVENT = 1;
    const QUERY_PAGE = 2;
    const QUERY_SEF = 3;

    private $request;
    public $request_c;
    public $request_params;
    public $request_page;
    public $request_page_arg = array();
    public $uri;
    public $querytype = self::QUERY_HOME;
    private $pageindex = 0;

    /**
     * Конструктор.  Выполняет  парсинг  HTTP запроса
     * 
     */
    public function __construct()
    {

        // основной  тип URI генерируемый  компонентами  фреймворка
        if (isset($_REQUEST["q"])) {

            $this->querytype = self::QUERY_EVENT;
            $this->request = explode("::", $_REQUEST["q"]);
            if (count($this->request) < 1) {
                throw new Exception(ERROR_INVALID_VERSION);
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
        //пример  - переход на  страницу с  классом  news : /?p=mycms/news&arg=2/4

        if (isset($_REQUEST["p"])) {
            $this->querytype = self::QUERY_PAGE;
            $this->request_page = "\\" . ltrim(str_replace("/", "\\", $_REQUEST["p"]), "\\");
            $this->request_page_arg = isset($_REQUEST["arg"]) ? explode('/', trim($_REQUEST["arg"],'/')) : array();
            return;
        }
        // URI формируемый  BookmarkableLink (в частности, ЧПУ) 

        if (strlen($_SERVER["REQUEST_URI"]) > 1 && strpos($_SERVER["REQUEST_URI"], '/?') === false && strpos($_SERVER["REQUEST_URI"], '/index.php') === false) {
            $this->querytype = self::QUERY_SEF;
            $this->uri = ltrim($_SERVER["REQUEST_URI"], '/');
        }
    }

    /**
     * Возвращает  индекс  страницы
     * @return int
     */
    public function getRequestIndex()
    {
        return $this->pageindex;
    }

    /**
     * Проверка  был  ли запрос AJAX запросом
     * 
     */
    public function isAjaxRequest()
    {
        return isset($_REQUEST["ajax"]);
    }

    /**
     * Проверка  был  ли запрос запросом бинарного контента
     * 
     */
    public function isBinaryRequest()
    {
        return isset($_REQUEST["binary"]);
    }

}

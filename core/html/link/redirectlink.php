<?php

namespace Zippy\Html\Link;

use Zippy\WebApplication;
use Zippy\Interfaces\Requestable;

/**
 * Елемент-ссылка  выполняющая  редирект
 *
 */
class RedirectLink extends AbstractLink implements Requestable
{
    public $bookmark;
    public $pagename;
    public $params;
    public $bookmarkable;
    public $encode;

    /**
     * Конструктор
     * @param string $id ID компонента
     * @param string $pagename Имя страницы для   редиректа
     * @param array $params Список  параметров  для  страницы
     * @param mixed $bookmarkable Если  true  - рендерит  bookmarkable  ссылку
     * @param mixed $encode Если  true  - кодирует ссылку
     */
    public function __construct($id, $pagename = "", $params = array(), $bookmarkable = true, $encode = false) {
        AbstractLink::__construct($id);
        $this->setLink($pagename, $params, $bookmarkable, $encode);
    }

    /**
     * @see  Requestable
     */
    public function RequestHandle() {
        $cnt = count($this->params);
        switch ($cnt) {
            case 0:
                WebApplication::$app->getResponse()->Redirect($this->pagename);
                break;
            case 1:
                WebApplication::$app->getResponse()->Redirect($this->pagename, $this->params[0]);
                break;
            case 2:
                WebApplication::$app->getResponse()->Redirect($this->pagename, $this->params[0], $this->params[1]);
                break;
            case 3:
                WebApplication::$app->getResponse()->Redirect($this->pagename, $this->params[0], $this->params[1], $this->params[2]);
                break;
            case 4:
                WebApplication::$app->getResponse()->Redirect($this->pagename, $this->params[0], $this->params[1], $this->params[2], $this->params[3]);
                break;
            case 5:
                WebApplication::$app->getResponse()->Redirect($this->pagename, $this->params[0], $this->params[1], $this->params[2], $this->params[3], $this->params[4]);
                break;
        }
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl() {
        parent::RenderImpl();
        if ($this->bookmarkable === true) {


            $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
            if ($this->encode == true) {
                $url = serialize(array($this->pagename, $this->params));
                if (strlen($url) % 3 == 1) {
                    $url .= '  ';
                }
                if (strlen($url) % 3 == 2) {
                    $url .= ' ';
                }

                $url = $_BASEURL . "/index.php?r=" . base64_encode(serialize(array($this->pagename, $this->params)));
            } else {
                $this->pagename = str_replace("\\", "/", ltrim($this->pagename, "\\"));

                $url = $_BASEURL . "/index.php?p=" . $this->pagename;
                if (count($this->params) > 0) {
                    $_param = implode("/", $this->params);
                    $url .= "&arg=" . $_param;
                }
            }
        } else {
            $url = $this->owner->getURLNode() . "::" . $this->id;  //для   вызова  обработчика
        }
        $this->setAttribute("href", "{$url}");
    }

    public function setLink($pagename, $params = array(), $bookmarkable = true, $encode = false) {
        $this->pagename = $pagename;
        $this->params = $params;
        if (!is_array($params)) {
            $this->params = array($params);
        }
        $this->bookmarkable = $bookmarkable;
        $this->encode = $encode;
    }

}

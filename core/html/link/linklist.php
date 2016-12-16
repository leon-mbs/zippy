<?php

namespace Zippy\Html\Link;

use \Zippy\WebApplication;
use \Zippy\HtpRequest;
use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Компонент отображающий  список  ссылок
 * 
 */
class LinkList extends HtmlComponent implements ClickListener, Requestable
{

    public $delimiter = " ";
    public $list = array();
    protected $event;
    public $selectedvalue;

    /**
     * Конструктор
     * @param  string ID компонента
     * @param  string Разделитель  между ссылками
     */
    public function __construct($id, $delimiter = ' ')
    {
        parent::__construct($id);
        $this->delimiter = $delimiter;
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        $url = $this->owner->getURLNode() . "::" . $this->id;

        $out = "";
        for ($i = 1; $i <= count($this->list); $i++) {
            $item = $this->list[$i];
            $attributes = "";
            if ($item['selected'] == true) {
                $item['attributes']['data-selected'] = 'selected';
            } else {
                $item['attributes']['data-selected'] = '';
            }
            if (count($item['attributes']) > 0) {
                foreach ($item['attributes'] as $key => $value) {
                    if (strlen($value) > 0) {
                        $attributes = $attributes . ' ' . $key . "=\"{$value}\" ";
                    }
                }
            }
            if ($item['disabled'] == true) {
                $out = $out . "<span>" . $item['caption'] . "</span>";
            } else {


                if ($item['type'] == 'click') {
                    $out = $out . "<a href = \"javascript:void(0);\" onclick=\"window.location='{$url}:{$i}';event.returnValue=false; return false;\"  {$attributes} >{$item['caption']}</a>";
                }
                if ($item['type'] == 'redirect') {
                    if ($item['encode'] == true) {
                        $_BASEURL = WebApplication::$app->getResponse()->getHostUrl();
                        $url = $_BASEURL . "/?r=" . base64_encode(serialize(array($item['page'], $item['params'])));
                    } else {
                        $pagename = str_replace("\\", "/", ltrim($item['page'], "\\"));

                        $url = $_BASEURL . "/?p=" . $pagename;
                        if (count($item['params']) > 0) {
                            $_param = implode("/", $item['params']);
                            $url .= "&arg=" . $_param;
                        }
                    }


                    $out = $out . "<a href = \"{$url}\"   {$attributes} >{$item['caption']}</a>";
                }
                if ($item['type'] == 'bookmarkable') {
                    $out = $out . "<a href = \"{$item['value']}\"   {$attributes} >{$item['caption']}</a>";
                }
            }
            $out .= $this->delimiter;
        }
        $out = substr($out, 0, strlen($out) - strlen($this->delimiter));

        $this->getTag()->html($out);
    }

    /**
     * Добавляет к  списку аналог ClickLink
     * @param mixed Значение  привязанное  к  ссылке
     * @param string Текст  ссылки
     * @param  array список   аттрибутов
     */
    public function AddClickLink($value, $caption, $attributes = array())
    {
        $this->list[count($this->list) + 1] = array('type' => 'click', 'value' => $value, 'caption' => $caption, 'attributes' => $attributes, 'selected' => false, 'disabled' => false);
    }

    /**
     * Добавляет к  списку аналог BookmarkableLink
     * @param mixed Адрес  ссылки
     * @param string Текст  ссылки
     * @param  array список   аттрибутов
     */
    public function AddBookmarkableLink($href, $caption, $attributes = array())
    {
        $this->list[count($this->list) + 1] = array('type' => 'bookmarkable', 'href' => $href, 'caption' => $caption, 'attributes' => $attributes, 'selected' => false, 'disabled' => false);
    }

    /**
     * Добавляет к  списку аналог RedirectLink
     * @param mixed Страница
     * @param  array список  параметров  страницы
     * @param string Текст ссылки
     * @param  array список аттрибутов
     */
    public function AddRedirectLink($page, $params, $caption, $attributes = array(), $bookmarkable = true, $encode = false)
    {
        $this->list[count($this->list) + 1] = array('type' => 'redirect', 'page' => $page, 'params' => $params, 'caption' => $caption, 'attributes' => $attributes, 'bookmarkable' => $bookmarkable, 'encode' => $encode, 'selected' => false, 'disabled' => false);
    }

    /**
     * @see Requestable
     */
    public function RequestHandle()
    {

        $p = WebApplication::$app->getRequest()->request_params[$this->id];
        if (!is_numeric($p[0]))
            return;
        $item = $this->list[$p[0]];
        if (!is_array($item))
            return;
        if ($item['type'] == 'click') {
            $this->selectedvalue = $item['value'];
            $this->setSelected($p[0]);
            $this->OnEvent();
        }
        if ($item['type'] == 'redirect') {
            WebApplication::getApplication()->getResponse()->Redirect($item['page'], $item['params']);
        }
    }

    /**
     * @see ClickListener
     */
    public function onClick(\Zippy\Interfaces\EventReceiver $receiver, $handler, $ajax = false)
    {
        $this->setClickHandler($receiver, $handler);
        $this->event->isajax = $ajax;
    }

    /**
     * Вызывает  событие
     */
    public function OnEvent()
    {
        if ($this->event != null) {
            $this->event->onEvent($this);
        }
    }

    /**
     * Очистка  массива
     */
    public function Clear()
    {
        $this->list = array();
    }

    /**
     * Устанавливает  пункт  выделенным.
     * @param mixed Номер пункта
     */
    public function setSelected($number)
    {

        for ($i = 1; $i <= count($this->list); $i++) {
            $this->list[$i]['selected'] = false;
        }
        if (isset($this->list[$number])) {
            $this->list[$number]['selected'] = true;
        }
    }

    /**
     * Устанавливает  пункт  как  текст
     * @param mixed Номер пункта   
     */
    public function setDisabled($number)
    {
        for ($i = 1; $i <= count($this->list); $i++) {
            $this->list[$i]['disabled'] == false;
        }

        if (isset($this->list[$number])) {
            $this->list[$number]['disabled'] = true;
        }
    }

}

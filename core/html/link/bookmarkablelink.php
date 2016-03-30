<?php

namespace Zippy\Html\Link;

use \Zippy\Interfaces\Binding;
use \Zippy\Binding\SimpleBinding;

/**
 * Ссылка  с  возможностью  делать  закладки, копировать  и  т.д.
 *
 */
class BookmarkableLink extends AbstractLink
{

    private $link;

    /**
     * Конструктор
     * @param  string ID компонента
     * @param  string Адрес  ссылки
     */
    public function __construct($id, $link = "")
    {
        AbstractLink::__construct($id);
        $this->link = $link;
    }

    /**
     * @see  HtmlComponent
     */
    public function RenderImpl()
    {
        parent::RenderImpl();
        $link = $this->getLink();
        if (strlen($link) > 0) {
            if (strpos($link, '://') === false) {
                // $link = "http://".$_SERVER["HTTP_HOST"]."/". $link;
                if ($link[0] != '/')
                    $link = '/' . $link;
            }
            $this->setAttribute("href", $link);
        }
    }

    /**
     * возвращает ссыоку
     *
     */
    public function getLink()
    {
        if ($this->link instanceof Binding) {
            return $this->link->getValue();
        } else {
            return $this->link;
        }
    }

    /**
     * Устнанавливает  ссылку
     *
     * @param mixed $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

}

<?php

namespace Zippy\Html\Link;

/**
 * Класс  для  якоря
 *
 */
class Anchor extends AbstractLink
{
    private $name;

    public function __construct($id, $name = '') {
        parent::__construct($id);
        $this->name = $name;
    }

    public function RenderImpl() {

        $this->setAttribute("name", "#" . $this->name);
    }

    /**
     * Установить  имя   якоря
     */
    public function setAnchor($name) {
        $this->name = $name;
    }

}

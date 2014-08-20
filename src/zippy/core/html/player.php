<?php

namespace Zippy\Html;

/**
 * Компонент  медиаплеера  HTML5 
 */
class Player extends HtmlComponent
{

    public $sources;
    public $src;

    public function __construct($id)
    {
        parent::__construct($id);
        $this->sources = array();
    }

    /**
     * рендеринг
     * @see Zippy\Html\HtmlComponent 
     */
    protected function RenderImpl()
    {
        $i = 0;
        $children = pq('[zippy=' . $this->id . ']  source');
        foreach ($children as $child) {
            if (strlen($this->sources[$i]) > 0) {
                pq($child)->attr("src", $this->sources[$i]);
                $i++;
            }
        }

        if (strlen($this->src) > 0)
            $this->setAttribute("src", $this->src);
    }

}

?>

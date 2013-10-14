<?php

namespace ZCL\Tree;

/**
 * Класс вывода узла дерева
 * 
 */
class TreeNode
{

        protected $text;
        public $children = array();
        private $dataitem;
        //  public $isroot = false;
        public $islast = false;
        public $expand = false;
        public $number, $owner, $parent = null;
        public $isactive = false;
        public $isselected = false;
        public $isloading = false;
        public $icon = ""; //иконки  для  узла
        public $checked = null;

        /**
         * Конструктор
         * @param string Текст  возле  узла
         * @param mixed Елемент данных ассоциированный  с  узлом
         * @param boolean Усли true  - рендерит  узел,  подтягивающий  дочерние  через AJAX
         *
         */
        public function __construct($text, $dataitem = null, $loading = false)
        {
                $this->text = $text;
                $this->dataitem = $dataitem;
                $this->isloading = $loading;
        }

        /**
         * Возвращает елемент данных ассоциированный  с  узлом
         */
        public function getDataItem()
        {
                return $this->dataitem;
        }

        /**
         *  Устанавливает  елемент данных ассоциированный  с  узлом
         * 
         * @param mixed $dataitem
         */
        public function setDataItem($dataitem)
        {
                $this->dataitem = $dataitem;
        }

        /**
         * @see Container
         */
        public function Render()
        {
                // $isroot_ = $this->isroot ? "isRoot" : "";
                //  $islast_ = $this->islast ? "IsLast" : "";
                $content = "";

                $isroot = $this->parent == null ? "IsRoot" : '';
                $expand = $this->isactive ? "ExpandOpen" : "ExpandClosed";


                if (count($this->children) > 0) {

                        $content = "  <li nodeid=\"{$this->number}\" class=\" Node   {$expand} {$isroot} \">";
                        $content .= "<div class=\"Expand\"></div>";
                        $content .= $this->getCaption();
                        if (!$this->isloading) {
                                $content .= "<ul class=\"Container\">";
                                foreach ($this->children as $child) {

                                        $content .= $child->Render();
                                }
                                $content .= "</ul>";
                        }

                        $content .= "</li>";
                } else {

                        $content = "  <li class=\" Node   ExpandLeaf  {$isroot} \"><div class=\"Expand\"></div> ";

                        $content .= $this->getCaption();
                }

                return $content;
        }

        /**
         * @see AjaxRender
         */
        public function AjaxRender()
        {
                usleep(300000); //для  демо
                $caption = $this->getCaption();

                $content = "<ul class=\"Container\">";
                foreach ($this->children as $child) {
                        $content .= $child->Render();
                }
                $content .= "</ul>";
                return $content;
        }

        /**
         * Возвращает HTML код  ссылки.
         * @return  string
         */
        protected function getRenderedLink()
        {
                $url = $this->owner->getUrl() . ":" . $this->number;
                $class = "";
                if ($this->isselected) {
                        $class = "class=\"IsSelected\"";
                }
                $link = "<a href=\"{$url}\"  {$class} >{$this->text}</a>";
                return $link;
        }

        /**
         * Устанавливает узел  акттивным  - выделенным
         * или  находящимся  в  ветке  с  выделенным  узлом
         * @param boolean
         */
        public function setActive($active)
        {
                $this->isactive = $active;
                $this->isloading = false;
                if ($this->parent != null && $active == true) {
                        $this->parent->setActive($active);
                }
        }

        /**
         * Устанавливает узел  выделенным (текущим)
         * @param boolean
         */
        public function setSelected($value)
        {
                $this->isselected = $value;
        }

        /**
         * Возвращает  текст узла
         * @return  string
         */
        public function getText()
        {
                return $this->text;
        }

        /**
         * Устанавливает  текст узла
         * @param  string Текст
         */
        public function setText($text)
        {
                $this->text = $text;
        }

        /**
         *   Возвращает для рендеринга текст  узла с  иконками  и  чекбоксом (если они установлены)
         */
        private function getCaption()
        {
                $caption = "";
                $link = $this->getRenderedLink();

                if ($this->checked !== null) {
                        $checked = $this->checked ? "checked" : "";
                        $caption .= "<input type=\"checkbox\" {$checked} name=\"{$this->owner->id}_{$this->number}\" onclick=\"treeCheck(this)\" /> ";
                } else
                if (strlen($this->icon) > 0) {
                        $caption .= "<img src=\"{$this->icon}\" />";
                }
                $caption .= "<div class=\"Content\"><span>{$link}</span></div>";
                return $caption;
        }

}


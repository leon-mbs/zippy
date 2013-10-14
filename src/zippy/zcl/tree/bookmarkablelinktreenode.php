<?php

namespace ZCL\Tree;

/**
 * Узел  с  bookmarkable ссылкой
 */
class BookmarkableLinkTreeNode extends TreeNode
{

        private $link;

        /**
         * Конструктор
         * @param string ID
         * @param string Адрес ссылки
         * @param boolean Усли true  - рендерит  узел,  подтягивающий  дочерние  через AJAX
         */
        public function __construct($caption, $link, $loading = false)
        {
                $this->text = $caption;
                $this->link = $link;
                $this->isloading = $loading;
        }

        /**
         * Возвращает  HTML код  ссылки
         * @return  string
         */
        protected function getRenderedLink()
        {
                if ($this->isselected) {
                        $class = "class=\"IsSelected\"";
                }

                return "<a href=\"{$this->link}\" {$class} >{$this->text}</a>";
        }

}


<?php

namespace ZCL\Tree;

/**
 * Класс вывода узла дерева простым  текстом  вместо  ссылки.
 * 
 */
class SimpleTreeNode extends TreeNode
{

        public function __construct($caption, $loading = false)
        {
                parent::__construct($caption, null, $loading);
        }

        protected function getRenderedLink()
        {
                return $this->text;
        }

}


<?php

namespace ZCL\Tree;

/**
 * Узел  вызывающий  серверный  обработчик  через  AJAX
 */
class AjaxTreeNode extends TreeNode
{

        /**
         * Возвращает  HTML код  ссылки
         * @return  string
         */
        protected function getRenderedLink()
        {
                $url = $this->owner->getUrl() . ":" . $this->number . "&ajax=true";
                $link = "<a href=\"#\" onclick=\"getUpdate('{$url}');\" >{$this->caption}</a>";
                return $link;
        }

}


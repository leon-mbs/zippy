<?php

namespace ZCL\Tree;

use \Zippy\WebApplication;
use \Zippy\Html\HtmlComponent;
use \Zippy\Interfaces\ClickListener;
use \Zippy\Interfaces\Requestable;
use \Zippy\Interfaces\SubmitDataRequest;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Event;

/**
 * Класс вывода  дерева
 * 
 */
class Tree extends HtmlComponent implements Requestable, ClickListener, SubmitDataRequest
{

        private $number = 1;
        public $children = array();  // массив  дочерних
        private $selected = null; // выбраный   узел
        public $nodes = array();  //массив  всех  nodes
        //   public  $root = null; //корневой  узел всегда  раскрыт  и  без  иконки
        protected $event;

        /**
         * Конструктор
         *
         * @param  string ID номер  компонента
         * @param  TreeNode Корневой узел
         */
        public function __construct($id)
        {
                HtmlComponent::__construct($id);
                //$this->addRoot($root);
                //$root->setActive(true);
                //$root->setSelected(true);
        }

        /**
         * @see HtmlComponent
         */
        public function RenderImpl()
        {
                $url = $this->getUrl();

                $tree = "<ul class=\"Container \" >";
                foreach ($this->children as $child) {
                        $tree .= $child->Render();
                }

                $tree .= "</ul><br/>";

                $this->getTag()->html($tree);
                WebApplication::$app->getResponse()->addJavaScript("tree('{$this->id}', '{$url}')", true);
        }

        /**
         * Добавляет  узел
         * @param  TreeNode Добавляемый   узел
         * @param  TreeNode Родительский  узел
         */
        public function addNode(TreeNode $node, TreeNode $parentnode = null)
        {

                $node->islast = true;
                $node->owner = $this;
                $node->parent = $parentnode;
                $node->number = $this->number++;
                $this->nodes[$node->number] = $node;

                if ($parentnode == null) {
                        // $node->isroot = true;
                        /* $count = count($this->children);
                          if ($count > 0) {
                          $this->children[$count - 1]->islast = false;
                          }
                         */
                        $this->children[] = $node;
                } else {
                        // $parentnode->setSelected(false);
                        if (in_array($parentnode, $this->nodes)) {
                                //$parentnode->addNode($node);
                                $parentnode->children[] = $node;
                        } else {
                                throw new \Zippy\Exception(ERROR_INVALID_PARENT_NODE);
                        }
                }
                return $node;
        }

        /**
         * @see Requestable
         */
        public function RequestHandle()
        {
                $p = WebApplication::$app->getRequest()->request_params[$this->id];
                $number = $p[0];
                $node = $this->nodes[$number];
                if ($node == null) {
                        throw new \Zippy\Exception(ERROR_INVALID_TREELINK);
                }

                $this->setSelectedNode($node);


                if (isset($p[1]) && $p[1] == "load") {
                        $js = $node->AjaxRender();
                        WebApplication::$app->getResponse()->addAjaxResponse($js);
                        return;
                } else {
                        $this->clickednode = $node;
                        $this->OnClick();
                }
        }

        /**
         * Возвращает URL используемый дочерними  компонентами
         * @return  string
         */
        public function getUrl()
        {
                return $this->owner->getURLNode() . "::" . $this->id;
        }

        /**
         * @see ClickListener
         */
        public function setClickHandler(EventReceiver $receiver, $handler)
        {
                $this->event = new Event($receiver, $handler);
        }

        /**
         * @see ClickListener
         */
        public function OnClick()
        {
                if ($this->event != null) {
                        $this->event->onEvent($this->clickednode);
                }
        }

        /**
         * Удаляет  узел  с  дерева
         * @param  TreeNode Удаляемый  узел
         */
        public function removeNode(TreeNode $node)
        {
                $key = array_search($node, $this->nodes);
                if ($key === false)
                        return;
                unset($this->nodes[$key]);
                foreach ($node->children as $child) {
                        $this->removeNode($child);
                }
                if ($node->parent != null) {
                        $key = array_search($node, $node->parent->children);
                        if ($key !== false) {
                                unset($node->parent->children[$key]);
                        }
                }
        }

        /**
         * #see  SubmitDataRequest
         */
        public function getRequestData()
        {
                foreach ($this->nodes as $node) {
                        if ($node->checked === null)
                                continue;

                        $node->checked = isset($_REQUEST[$this->id . '_' . $node->number]);
                }
        }

        /**
         * Устанавливает  текущий  узел.
         */
        public function setSelectedNode(TreeNode $node)
        {
                foreach ($this->nodes as $_node) {
                        $_node->setActive(false);
                        $_node->setSelected(false);
                }
                //  $node->setActive(true);
                $node->setSelected(true);
                $node->setActive(true);
                if ($node->parent != null) {
                        $node->parent->setActive(true);
                }
                $this->selected = $node;
        }

        /**
         * усьанавливает активным корень  дерева  
         * 
         */
        public function setSelectedRoot()
        {
                $this->setSelectedNode($this->children[0]);
        }

        /**
         * Возвращает  текущий  узел
         * 
         */
        public function getSelectedNode()
        {
                return $this->selected;
        }

        /**
         * Удаляет  все узлы с  дерева
         */
        public function removeNodes()
        {
                $this->nodes = array();
                $this->children = array();
                $this->selected = null;
        }

        /**
         * устанавливает  иконку для  узлов
         *  
         * @param mixed $icon
         */
        public function setIcons($icon)
        {
                foreach ($this->nodes as $node) {
                        $node->icon = $icon;
                }
        }

        /**
         * Возвращает  узел  по  данным  иссоциированым   с  ним
         * 
         * @param mixed $item
         */
        public function getNodeByDataitem($item)
        {
                foreach ($this->nodes as $node) {
                        if ($node->getDataItem() == $item) {
                                return $node;
                        }
                }
                return null;
        }

}


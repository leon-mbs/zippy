<?php

namespace ZCL\BT;

use \Zippy\Interfaces\AjaxRender;
use \Zippy\WebApplication;
use \Zippy\Interfaces\SubmitDataRequest;
use \Zippy\Html\HtmlComponent;

class Tree extends HtmlComponent
{

    private $number = 1;
    public $children = array();  // массив  дочерних
    public $nodes = array();  //массив  всех  nodes

    public function __construct($id)
    {
        HtmlComponent::__construct($id);
    }

    public function RenderImpl()
    {
        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";



        $js = " 
               $('#{$this->id}').treeview({
                  data: getTree(),
                  expandIcon:'fa fa-plus',
                  collapseIcon:'fa fa-minus',
                  checkedIcon:'fa fa-check-square-o',
                  uncheckedIcon:'fa fa-square-o',
                  enableLinks:true, 
                 
                  emptyIcon:'fa ' 
                  
                  
                  });
                ";

        WebApplication::$app->getResponse()->addJavaScript($js, true);

        $js = " 
       function getTree(){
       var tree =   
        [";

        $c = array();
        foreach ($this->children as $child) {
            $c[] = $child->Render();
        }


        $js .= implode(',', $c);

        $js .= " 
        ] ;
        return tree;
           }
        ";


        WebApplication::$app->getResponse()->addJavaScript($js);
    }

    public function addNode(TreeNode $node, TreeNode $parentnode = null)
    {


        $node->owner = $this;
        $node->parent = $parentnode;
        $node->number = $this->number++;
        $this->nodes[$node->number] = $node;

        if ($parentnode == null) {

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

}

class TreeNode
{

    protected $text;
    public $children = array();
    public $tags;
    public $expand = false;
    public $number, $owner, $parent = null;
    public $isselected = false;
    public $icon = ""; //иконки  для  узла
    public $checked = false;
    public $link = '';

    public function __construct($text, $dataitem = null, $loading = false)
    {
        $this->text = $text;
    }

    public function Render()
    {
        $js = "{  text: \"{$this->text}\"  ";
        if (strlen($this->link) > 0) {
            $js .="
            ,href: \"{$this->link}\"";
        }
        if ($this->isselected != null) {
            $js .="
            ,selectable: true";
        }
        if ($this->isselected === true) {
            $js .=" 
            ,state:{
               selected: true
            }
            ";
        }

        $c = array();
        foreach ($this->children as $child) {
            $c[] = $child->Render();
        }
        if (count($c) > 0) {
            $js .= '
            ,nodes: [
            ' . implode(',', $c) . '
            ]
            ';
        }




        return $js . "
        }";
    }

}

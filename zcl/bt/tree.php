<?php

namespace ZCL\BT;

use \Zippy\Interfaces\AjaxRender;
use \Zippy\WebApplication;
use \Zippy\Interfaces\SubmitDataRequest;
use \Zippy\Html\HtmlComponent;
use \Zippy\Event;
use \Zippy\Interfaces\EventReceiver;

/**
* Компонент  древовидного меню
* https://github.com/maxwells/bootstrap-treeview
*/
class Tree extends HtmlComponent  implements \Zippy\Interfaces\Requestable
{

    private $number = 1;
    public $children = array();  // массив  дочерних
    public $nodes = array();  //массив  всех  nodes
    private $options = array();
    private $event = null;
    private $selectedid = -1;   //id узла  на клиенте
    private $selectednodeid = -1;// кастомное  id (например treeentry id)
    private $expanded = array();
     
    public function __construct($id,$options=array())
    {
        HtmlComponent::__construct($id);
        if (is_array($options)) {
            $this->options = $options;
        }    
    }
    public function onSelectNode(EventReceiver $receiver, $handler,$ajax=false)
    {

        $this->event = new Event($receiver, $handler);
        $this->event->isajax = $ajax;
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
                  showBorder: false,
                  showTags: true,
                  "; 
                 
                  if(@$this->options['showTags'] === true){
                     $js .= "  showTags:true,  ";  
                  }
                  if(@$this->options['enableLinks'] === true){
                     $js .= "  enableLinks:true,  ";  
                  }
                 
              $js .= "     emptyIcon:'fa ' 
                  
                  
                  });
                ";
            WebApplication::$app->getResponse()->addJavaScript($js, true);
            
            $url = $this->owner->getURLNode() . "::" . $this->id;
            
            if($this->event instanceof Event){        
                 
                
                
                
                $js  = "   $('#{$this->id}').on('nodeSelected', function(event, node) {  
                   
                   
                   
                   
                   var url ='{$url}:sel:' + node.nodeId+':'+ node.zippyid +':'+getExpanded() ";
                   if($this->event->isajax){
                      $js .= "
 
             
                        url= url+'&ajax=true' ;
                         \$.ajax({
                                url: url,
                                success: function (data, textStatus) {
                                      // eval(data);
                                }
                        }); " ;
                   }else{
                      $js .= "  
                         window.location=url 
                      "  ;
                   
                   }
                $js .="  });   ";
                
                $js .= "   $('#{$this->id}').on('nodeUnselected ', function(event, node) {  
                   
                   
                   
                   
                     url ='{$url}:sel:-1:-1:'+getExpanded() ";
                   if($this->event->isajax){
                      $js .= "
 
             
                        url= url+'&ajax=true' ;
                         \$.ajax({
                                url: url,
                                success: function (data, textStatus) {
                                      // eval(data);
                                }
                        }); " ;
                   }else{
                      $js .= "  
                         window.location=url 
                      "  ;
                   
                   }
                $js .="  });   ";
                
                
            
            WebApplication::$app->getResponse()->addJavaScript($js, true);
        }
        
        
        
        $js = " 
        
        
        function getExpanded(){
    var explist ='';              
  var arr=  $('#{$this->id}').treeview('getExpanded');  
              arr.forEach(function(entry) {
              explist = explist +','+ entry.nodeId;
}); 
return explist;       
        }
        
        
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
       
       
       if($this->selectedid  >=0){
          $js  = "var allNodes=  $('#{$this->id}').treeview('getEnabled'); 
           ; ";  
          $js .= "$(allNodes).each(function(index, element) { 
           ";
          $js .= " if(element.zippyid=={$this->selectedid}) {
                  $('#{$this->id}').treeview('selectNode', [ element, { silent: true } ]);
                  $('#{$this->id}').treeview('revealNode', [ element, { silent: true } ]);
                       }; 
                 });";  
          WebApplication::$app->getResponse()->addJavaScript($js,true);    
        } else 
           if($this->selectednodeid  >0){
                  $js="  $('#{$this->id}').treeview('selectNode', [ {$this->selectednodeid}, { silent: true } ]); ";  
                  WebApplication::$app->getResponse()->addJavaScript($js,true);    
                }      
        
        if(count($this->expanded  >0)){
          $js ="";
          foreach($this->expanded as $id)  {
            $js .= "  $('#{$this->id}').treeview('expandNode', [ {$id}, { silent: true } ]); 
            ";      
          }
          
          WebApplication::$app->getResponse()->addJavaScript($js,true);    
        }
    }
 
    public final function RequestHandle()
    {
        $params = WebApplication::$app->getRequest()->request_params[$this->id];
         
        $op = $params[0];
        $this->selectedid = $params[2];
        $this->selectednodeid = $params[1];
        
        if(strlen($params[3])>0){
            $this->expanded = explode(',',trim($params[3],','));
        }
        
        if ($this->event != null && $op=='sel') {
            $this->event->onEvent($this, $this->selectedid);
        }
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
    
    /**
    * возвращает или  устанавливает кастомный id активного  узла
    * 
    */
    public function selectedNodeId($id=null){
        
        if($id>0){
          $this->selectedid =$id;    
        }
        return $this->selectedid;
        
    }
    
      public function removeNodes() {
          $this->nodes = array();
          $this->children = array();
          $this->number =1;
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
    public $zippyid =0;
  

    public function __construct($text, $id)
    {
        $this->text = $text;
        $this->zippyid = $id;
         
    }

    public function Render()
    {
        $js = "{  text: \"{$this->text}\",zippyid:{$this->zippyid}  ";
      
        if (strlen($this->link) > 0) {
            $js .="
            ,href: \"{$this->link}\"";
        }
      if (strlen($this->tag) > 0) {
            $js .=",tags: ['{$this->tag}'] ";
        }
        if ($this->isselected != null) {
            $js .="
            ,selectable: true";
        } 
        $js .=" 
            ,state:{  ";
        if ($this->isselected === true) {
            
          $js .="      selected: true ";
         
        }
     $js .="   } ";
       
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

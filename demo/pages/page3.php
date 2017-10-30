<?php

 namespace Pages;

use \Zippy\Html\Panel;
use \Zippy\Html\DataList\DataView;
use \Zippy\Html\DataList\ArrayDataSource;
use \Zippy\Html\Label;
use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;  
 
class Page3 extends Base{
    
    public $items=array();
    
    public function __construct()
    {
          parent::__construct();
        
          //тестовые  данные
          $this->items[1]= new User(1,"Иванов И.И.",30);
          $this->items[2]= new User(2,"Петров П.П.",32);
          $this->items[3]= new User(3,"Сидоров С.С.",40);        
          
          $this->add(new Panel('listpanel'));
          $this->listpanel->add(new DataView('list',new ArrayDataSource(new \Zippy\Binding\PropertyBinding($this,"items")),$this,'listOnRow'))->Reload();
          $this->add(new Panel('editpanel'))->setVisible(false);
          $this->editpanel->add(new Form('editform'))->onSubmit($this,'editformOnSubmit');
          $this->editpanel->editform->add(new TextInput('efio'));
          $this->editpanel->editform->add(new TextInput('eage'));
          $this->editpanel->editform->add(new TextInput('itemid'));
          $this->editpanel->editform->add(new ClickLink('cancel'))->onClick($this,'cancelOnClick');              
          

          
          
    }             

  public function listOnRow($row){
  $item = $row->getDataItem();

  $row->add(new Label('fio',$item->fio));
  $row->add(new Label('age',$item->age));
  $row->add(new ClickLink('edit'))->onClick($this,'editOnClick');



}
public function editOnClick($sender){
   //получаем  объект данных связанный с  строкой 
   $item = $sender->getOwner()->getDataItem();
   
   
   $this->editpanel->editform->itemid->setText($item->id); 
   $this->editpanel->editform->efio->setText($item->fio); 
   $this->editpanel->editform->eage->setText($item->age); 
    
   //переключаем видимолсть панелей 
   $this->editpanel->setVisible(true);
   $this->listpanel->setVisible(false);
   

}

public function editformOnSubmit($sender){

    
   $item=  $this->items[$sender->itemid->getText()];
   $item->fio = $sender->efio->getText();
   $item->age = $sender->eage->getText();
   
   //обновляем  грид
   $this->listpanel->list->Reload();   
 
   $this->editpanel->setVisible(false);
   $this->listpanel->setVisible(true);   
}
public function cancelOnClick($sender){
   $this->editpanel->setVisible(false);
   $this->listpanel->setVisible(true);
}    
}     
    

class  User implements \Zippy\Interfaces\DataItem
{
    public $id;
    public $fio;
    public $age;
    
    public function __construct($id,$fio,$age)
    {
        $this->id=$id;
        $this->fio=$fio;
        $this->age=$age;
    } 
    //требование  интерфейса
    public function getID() { return $this->id;}
}

<?php

 namespace Pages;

use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Label;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;  
use \Zippy\Html\Form\DropDownChoice;  
use \Zippy\Html\Form\SubmitButton;  
 
class Page2 extends Base{
    public function __construct()
    {
          parent::__construct();
          
          $this->add(new ClickLink('onmsg'))->onClick($this,'onmsgOnClick',true);
          $this->add(new Label('msg'));
          
          $this->add(new Form('form1'));
          $this->form1->add(new TextInput('entermsg'));
          $this->add(new Label('showmsg'));             
          $this->form1->add(new SubmitButton('s2'))->onClick($this,'form1OnSubmit',true);             

          $this->add(new Form('form2')) ;
          $this->form2->add(new DropDownChoice('type',array(1=>'Бытовая техника',2=>'Компы',3=>'Мабилы')))->onChange($this,"ontype",true);
          $this->form2->add(new DropDownChoice('subtype')) ;
          
 
    } 
    
    public function onmsgOnClick($sender){
       $this->msg->setText("Время: " . date("H:i:s"));
       $this->updateAjax(array('msg'))  ;
    }
    
    public function form1OnSubmit($sender){
       $this->showmsg->setText($this->form1->entermsg->getText());
       $this->updateAjax(array('showmsg'))  ;
    }
    
    
    public function ontype($sender){
       $value= $this->form2->type->getValue();
       
       //дополнительныйй скрипт
       $js=$value > 0 ? "\$('#subtype').show()" : "\$('#subtype').hide()";
       
       
       if($value =="1")
       {
          $this->form2->subtype->setOptionList(array(1=>'Холодильник',2=>'Утюг',3=>'Чайник'));    
       }
       if($value =="2")
       {
          $this->form2->subtype->setOptionList(array(1=>'Комп',2=>'Планшет',3=>'Ноут'));    
       }
       if($value =="3")
       {
          $this->form2->subtype->setOptionList(array(1=>'Samsung',2=>'Apple',3=>'Lenovo'));    
       }
       $this->form2->subtype->setValue(0);
       $this->updateAjax(array('subtype'),$js )  ;
    }
}     
    



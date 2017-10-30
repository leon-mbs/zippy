<?php

 namespace Pages;

use \Zippy\Html\Link\ClickLink;
use \Zippy\Html\Label;
use \Zippy\Html\Form\Form;
use \Zippy\Html\Form\TextInput;  
use \Zippy\Html\Form\DropDownChoice;  
 
class Page1 extends Base{
    public function __construct()
    {
          parent::__construct();
          
          $this->add(new ClickLink('onmsg'))->onClick($this,'onmsgOnClick');
          $this->add(new Label('msg'));
          
          $this->add(new Form('form1'))->onSubmit($this,'form1OnSubmit');
          $this->form1->add(new TextInput('entermsg'));
          $this->add(new Label('showmsg'));             

          $this->add(new Form('form2')) ;
          $this->form2->add(new DropDownChoice('type',array(1=>'Бытовая техника',2=>'Компы',3=>'Мабилы')))->onChange($this,"ontype");
          $this->form2->add(new DropDownChoice('subtype'))->setVisible(false);

                    
    } 
    
    public function onmsgOnClick($sender){
       $this->msg->setText("Время: " . date("H:i:s"));
    }
    public function form1OnSubmit($sender){
       $this->showmsg->setText($this->form1->entermsg->getText());
    }
    public function ontype($sender){
       $value= $this->form2->type->getValue();
       $this->form2->subtype->setVisible($value>0);
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
       
    }
}     
    



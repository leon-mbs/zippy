<?php

namespace Zippy\Html;

use Zippy\Interfaces\Binding;
use Zippy\Interfaces\EventReceiver;
use Zippy\Exception as ZE;
use Zippy\WebApplication;

/**
 * Базовый   класс  для   всех HTML  компонентов
 *
 * Каждый компонент  имеет уникальный в пределах страницы номер элемента
 * соответствующий аттрибуту 'zippy' из  соответствующего тэга  HTML шаблона
 * Иерархия  компонентов  должна  строго  соответстввовать  иерархии  вложенных HTML тэгов
 */
abstract class HtmlComponent
{
    public $id;
    protected $disabled = false;
    protected $visible = true;
    private $attributes = array();
    private $delattributes = array(); //те что надо  удалить
    //  private $cssstyle = "";
    //   private $cssclass = "";
    protected $owner = null;

    //   public  $uid;
    //  private   static  $uidcounter = 0;
    // protected $HtmlTag=null;

    public $tag = null; //Позволяет связать  с компонентом  произвольные двнные

    /**
     * Конструктор
     *
     * @param string
     */

    public function __construct($id) {
        if (!is_string($id) || strlen($id) == 0) {
            throw new ZE(sprintf(ERROR_INVALID_CREATE_ID, get_class($this)));
        }
        if (in_array($id, array('add'))) {
            throw new ZE(sprintf(ERROR_INVALID_ID, $id));
        }
        $this->id = $id;
        $this->attributes["id"] = $id;


        //   $this->uid = ++self::$uidcounter; // номер  екземпляра
    }

    public function __toString() {
        $str = get_class($this) . ' ' . $this->id;
        if ($this->owner != null) {
            $str = ' ' . $this->owner . '->' . get_class($this) . ' ' . $this->id;
        }
        return $str;
    }

    /**
     * Установить   аттрибут для  отобюражения   в  HTML тэге
     * @param string  Имя  атрибута
     * @param string Значение аттрибута
     */
    public function setAttribute($name, $value=null) {

        if($value===null) {
           $this->delattributes[$name] = $name; 
        } else {
            $this->attributes[$name] = $value;
            unset( $this->delattributes[$name] );
        }
        
 
    }

    /**
     * Получить  атрибут
     * @param string Имя  атрибута
     * @return  string
     */
    public function getAttribute($name) {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name] instanceof Binding ? $this->attributes[$name]->getValue() : $this->attributes[$name];
        } else {
            return null;
        }
    }

    /**
     * Возвращает список наименований аттрибутов
     * @return array
     */
    public function getAttributeNames() {
        return array_keys($this->attributes);
    }

    /**
     * Установка  коонтейнера -владельца
     * @param HtmlContainer Владелец
     */
    public function setOwner(HtmlContainer $owner) {
        $this->owner = $owner;
    }

    /**
     * Вызывается  после  добавления   к  владельцу
     */
    protected function onAdded() {

    }

    /**
     * Получить  ссылку  на   владедца
     *
     * @return  HtmlContainer
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     *  Возвращает ссылку на объект страницы в которую добавлен  компонент
     * @return  WebPage
     */
    public function getPageOwner() {
        if ($this->owner == null) {
            return null;
        }
        if ($this->owner instanceof \Zippy\Html\WebPage) {
            return $this->owner;
        } else {
            return $this->owner->getPageOwner();
        }
    }

    /**
     *  Возвращает ссылку на объект формы в которую добавлен  компонент
     * @return  Form
     */
    public function getFormOwner() {
        if ($this->owner == null) {
            return null;
        }
        if ($this->owner instanceof \Zippy\Html\Form\Form) {
            return $this->owner;
        } else {
            return $this->owner->getFormOwner();
        }
    }

    /**
     * Управляет  видимостью  компонента
     * Невидимый  компонент  не  рендерится
     * @param boolean
     */
    public function setVisible($visible) {
        $this->visible = $visible;

        
    }

    /**
     * Прверяет  видимость  компонента
     * @return  boolean
     */
    public function isVisible() {
        return $this->visible;
    }

    /**
     *  Метод  перегружаемый  компонентами  для  имплементации  своего  рендеринга
     */
    protected function RenderImpl() {

    }

    /**
     * Метод  отвечающий  за  рендеринг  компонента
     * @param MarkupXmlNode
     */
    public function Render() {

        $HtmlTag = $this->getTag();

        
        if (isset($this->attributes["class"])) {
            if (strlen($this->attributes["class"]) > 0) {
                $HtmlTag->addClass($this->attributes["class"]) ;
            } else {
                $HtmlTag->removeClass($this->attributes["class"]) ;
            }
        }
      
        
        foreach($HtmlTag->attributes as $a){
           $attributes[$a->nodeName] =$a->nodeValue;
        };
        
        
        if (isset($this->attributes["style"])) {
            if (strlen($this->attributes["style"]) > 0) {
                $attributes['style'] = ($attributes['style'] ??'') . ';  ' . ($this->attributes["style"] ??'');
            } else {
                $attributes['style'] = str_replace(($this->attributes["style"] ??''), "", ($attributes['style'] ??''));
            }
        }          
        
        unset($attributes["zippy"]);

        $attributes['id'] = $this->id;


        foreach ($attributes as $key => $value) {

            if (!array_key_exists($key, $this->attributes)) {
                $this->attributes[$key] = $value;
            }
        }


        $this->beforeRender();
        //вызываем имплементацию  рендеринга наследуемым  классом
        $this->RenderImpl();
        //рендерим   аттрибуты
        foreach ($this->attributes as $name => $value) {
            $attr = $this->getAttribute($name);
            
            if (in_array($name,$this->delattributes)) {
                $HtmlTag->removeAttr($name);
            }  else{
                $HtmlTag->attr($name, $attr);    
            }
        }
        $this->afterRender();
    }

    /**
     * Возвращает  ссылку  на  HTML таг. Используется  библиотека  PHPQuery
     */
    protected function getTag()  : \DOMWrap\Element {
        $p = $this->getPageOwner() ;

        $HtmlTag = WebApplication::$dom->find('[zippy="' . $this->id . '"]');

        if ($HtmlTag->count() > 1) {
            throw new ZE(sprintf(ERROR_MARKUP_NOTUNIQUEID, $this->id));
        }
        if ($HtmlTag->count() == 0) {
            $tag = 'zippy="' . $this->id . '"';

            throw new ZE(sprintf(ERROR_MARKUP_NOTFOUND, $tag));
        }
        return $HtmlTag->first();
    }

    /**
     * Метод, вызываемый  перед  рендерингом
     * в качестве  параметра передаются   атрибуты с  html тега
     */
    protected function beforeRender() {

    }

    /**
     * Метод, вызываемый после рендерингв
     */
    protected function afterRender() {

    }


    /**
     * возвращает  связаный  тег
     *
     */
    protected function getLabelTag() {
        return WebApplication::$dom->find('[data-label="' . $this->id . '"]');
    }
    protected function getLabelTagFor() {
        return WebApplication::$dom->find('[for="' . $this->id . '"]');
    }

    
 
    public function getHTML(){
        try{
          $HtmlTag = $this->getTag();
         $tn= $HtmlTag->tagName;
        $attr="";
        foreach($HtmlTag->attributes as $a){
          $attr = $attr. " {$a->nodeName}=\"{$a->nodeValue}\" ";
        };        
        
        $html = "<{$tn} {$attr}  >".    $HtmlTag->html() . "</{$tn}>";
               
        return $html;  
          
          
        } catch(\Exception $e) {
            return  null;
        }
    }
 
    
       
}

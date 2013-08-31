<?php

namespace Zippy\Html;

use \Zippy\Interfaces\Binding;
use \Zippy\Interfaces\EventReceiver;
use \Zippy\Exception as ZE;
use \Zippy\WebApplication;

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
        //  private $cssstyle = "";
        //   private $cssclass = "";
        protected $owner = null;
        protected $options = array();
        //   public  $uid;
        //  private   static  $uidcounter = 0;
        // protected $HtmlTag=null;

        public $tag = null; //Позволяет связать  с компонентом  произвольные двнные

        /**
         * Конструктор
         *
         * @param string
         */

        public function __construct($id)
        {
                if (!is_string($id) || strlen($id) == 0) {
                        throw new ZE(sprintf(ERROR_INVALID_ID, $id));
                }
                $this->id = $id;
                $this->attributes["id"] = $id;


                //   $this->uid = ++self::$uidcounter; // номер  екземпляра
        }

        public function __toString()
        {
                $str = get_class($this) . ' ' . $this->id;
                if ($this->owner != null)
                        $str = ' ' . $this->owner . '->' . get_class($this) . ' ' . $this->id;
                return $str;
        }

        /**
         * Установить   аттрибут для  отобюражения   в  HTML тэге
         * @param  string  Имя  атрибута
         * @param string Значение аттрибута
         */
        public function setAttribute($name, $value)
        {
                if (strlen($value) > 0) {
                        $this->attributes[$name] = $value;
                } else {
                        unset($this->attributes[$name]);
                }
        }

        /**
         * Получить  атрибут
         * @param  string Имя  атрибута
         * @return  string
         */
        public function getAttribute($name)
        {
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
        public function getAttributeNames()
        {
                return array_keys($this->attributes);
        }

        /**
         * Установка  коонтейнера -владельца
         * @param  HtmlContainer Владелец
         */
        public function setOwner(HtmlContainer $owner)
        {
                $this->owner = $owner;
        }

        /**
         * Вызывается  после  добавления   к  владельцу
         */
        protected function onAdded()
        {
                
        }

        /**
         * Получить  ссылку  на   владедца
         *
         * @return  HtmlContainer
         */
        public function getOwner()
        {
                return $this->owner;
        }

        /**
         *  Возвращает ссылку на объект страницы в которую добавлен  компонент
         *  @return  WebPage
         */
        public function getPageOwner()
        {
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
         * Управляет  видимостью  компонента
         * Невидимый  компонент  не  рендерится
         * @param boolean
         */
        public function setVisible($visible)
        {
                $this->visible = $visible;
        }
        
        /**
         * Прверяет  видимость  компонента
         * @return  boolean
         */
        public function isVisible()
        {
                return $this->visible;
        }

        /**
         *  Метод  перегружаемый  компонентами  для  имплементации  своего  рендеринга
         */
        protected function RenderImpl()
        {
                
        }

        /**
         * Метод  отвечающий  за  рендеринг  компонента
         * @param  MarkupXmlNode
         */
        public function Render()
        {

                $HtmlTag = $this->getTag();

                $attributes = $HtmlTag->attr('*'); //атрибуты с шаблона
                unset($attributes["zippy"]);

                $tagid = $HtmlTag->attr('id');
                if (isset($attributes['id']) && !isset($this->attributes['id'])) {
                        $this->attributes['id'] = $attributes['id'];
                }

                if (!isset($attributes['id']) && !isset($this->attributes['id'])) {
                        $this->attributes['id'] = $this->id;
                }

                if (isset($this->attributes["class"])) {
                        $attributes['class'] = $attributes['class'] . ' ' . $this->attributes["class"];
                }
                if (isset($this->attributes["style"])) {
                        $attributes['style'] = $attributes['style'] . $this->attributes["class"];
                }

                foreach ($attributes as $key => $value) {

                        if (!isset($this->attributes[$key])) {
                                $this->attributes[$key] = $value;
                        }
                }


                $this->beforeRender();
                //вызываем имплементацию  рендеринга наследуемым  классом
                $this->RenderImpl();
                //рендерим   аттрибуты
                foreach ($this->attributes as $name => $value) {
                        $attr = $this->getAttribute($name);
                        $HtmlTag->attr($name, $attr);
                }
                $this->afterRender();
        }

        /**
         * Возвращает  ссылку  на  HTML таг. Используется  библиотека  PHPQuery  
         */
        protected function getTag($tagname = "")
        {
                 $HtmlTag = pq(strtolower($tagname) . '[zippy="' . $this->id . '"]');
                if (strlen($tagname) > 0 && $HtmlTag->size() == 0) {
                        $HtmlTag = pq(strtoupper($tagname) . '[zippy="' . $this->id . '"]');
                }
                if ($HtmlTag->size() > 1 && strlen($this->owner->id) > 0) {
                        $HtmlTag = pq('[zippy="' . $this->owner->id . '"] [zippy="' . $this->id . '"]');
                }
                if ($HtmlTag->size() > 1 && strlen($this->owner->id) == 0) {
                        $HtmlTag = pq('[zippy="' . $this->id . '"]:first');
                }
                if ($HtmlTag->size() > 1) {
                        throw new ZE(sprintf(ERROR_MARKUP_NOTUNIQUEID, $this->id));
                }

                if ($HtmlTag->size() == 0) {
                        if (strlen($tagname) > 0) {
                                $tag = '&lt;' . $tagname . ' zippy="' . $this->id . '" &gt;';
                        } else {
                                $tag = 'zippy="' . $this->id . '"';
                        }
                        throw new ZE(sprintf(ERROR_MARKUP_NOTFOUND, $tag));
                }
                return $HtmlTag;
        }

        /**
         * Метод, вызываемый  перед  рендерингом
         * в качестве  параметра передаются   атрибуты с  html тега
         */
        protected function beforeRender()
        {
               
        }

        /**
         * Метод, вызываемый после рендерингв
         */
        protected function afterRender()
        {
                
        }

        /**
         * Устанавливает  массив   опций  коспонента
         */
        final public function setOptions(array $options = array())
        {
                $this->options = array_merge($this->options, $options);
        }

        /**
        * возвращает  связаный  тег  
        * 
        */
        protected function getLabelTag()
        {
                 return pq('[data-label="' . $this->id . '"]');

        }
}


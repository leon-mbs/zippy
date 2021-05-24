<?php

namespace ZCL\Captcha;

/**
 * Класс,  реализующий  простейшую  капчу.
 * Для  использования   собственной  реализации  класс нужно отнаследовать
 * и переопределить методы OnCode  и OnImage
 * Пример  использования http://examples.zippy.com.ua/Example4
 */
class Captcha extends \Zippy\Html\HtmlComponent implements \Zippy\Interfaces\AjaxRender, \Zippy\Interfaces\Requestable
{

    private static $timeout = 120;
    private $refresh;
    protected $code;
    protected $created;
    public $x = 60;
    public $y = 20;

    /**
     * Конструктор
     *
     * @param mixed $id
     * @param mixed $refresh если  true генерится код для обновления  через  AJAX
     * @return Captcha
     */
    public function __construct($id, $refresh = true) {
        parent::__construct($id);
        $this->refresh = $refresh;
        $this->Refresh();
    }

    /**
     * Реализует  алгоритм  прорисовки изображения.
     * Может  быть  перегружен для  пользовательской  реализации.
     * @return  ссылка  на  ресурс изображения
     */
    protected function OnImage() {
        $im = imagecreate($this->x, $this->y);
        $bg = imagecolorallocate($im, 200, 255, 200);
        $textcolor = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));

        imagestring($im, 5, 2, 2, $this->code, $textcolor);

        for ($i = 0; $i < 5; $i++) {
            $color = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));
            imageline($im, rand(0, 10), rand(1, 20), rand(50, 80), rand(1, 50), $color);
        }
ob_start(); 
    imagepng($im); 
    $contents = ob_get_contents(); 
ob_end_clean();         
        
        return $contents;
    }

    /**
     * Реализует  алгоритм  вычисление  кода. Может  быть  перегружен для  пользовательской  реализации
     * @return   строка  с кодом
     */
    protected function OnCode() {
        $chars = 'abdefhknrstyz23456789';
        $length = rand(4, 6);
        $numChars = strlen($chars);
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $str;
    }

    /**
     * Проверка  кода
     *
     */
    public function checkCode($code) {
        if ($this->created + self::$timeout < time()) {
            return false;
        }
        return $this->code == $code;
    }

    /**
     * Обновление кода капчи
     *
     */
    public function Refresh() {
        $this->code = $this->OnCode();
        $this->created = time();
    }

 

    /**
     * Обработчик HTTP запроса.  Возвращает  изображение  в  бинарный
     *  поток  или  ссылку для атрибута  src при  обновлении  изображения
     *
     */
    public function RequestHandle() {
       
        if (\Zippy\WebApplication::$app->getRequest()->isAjaxRequest()) {
            $this->Refresh();
            \Zippy\WebApplication::$app->getResponse()->addAjaxResponse($this->AjaxAnswer());
        }
    }

    /**
     * @see HttpComponent
     *
     */
    protected function RenderImpl() {

        $url = $this->owner->getURLNode() . "::" . $this->id . "&ajax=true";
        if ($this->refresh) {
            $this->setAttribute("onclick", "getUpdate('{$url}');event.returnValue=false; return false;");
        }
        $im = $this->OnImage();
           
        $src = "data:image/png;base64," . base64_encode($im);
         
        $this->setAttribute('src', $src);
    }

    /**
     * возвращает  код  для  обновления   атрибута  src
     *
     */
    public function AjaxAnswer() {
        $im = $this->OnImage();
           
        $src = "data:image/png;base64," . base64_encode($im);
        return "$('#{$this->id}').attr('src','{$src}')";
    }

    /**
     * Возвращает  код  капчи
     * @return  string
     */
    public function getCode() {
        return $this->code;
    }

}



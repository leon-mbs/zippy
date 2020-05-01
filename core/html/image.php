<?php

namespace Zippy\Html;

/**
 *  Компонент  для  тэга  &lt;IMG&gt;
 *
 */
class Image extends HtmlComponent implements \Zippy\Interfaces\Requestable, \Zippy\Interfaces\AjaxRender
{

    public static $DEFAULT_TYPE = 0;
    public static $URLDATA_TYPE = 1;
    public static $DYNAMIC_TYPE = 2;
    public $src;
    public $title;
    private $type = 0;
    private $event = null;

    /**
     * Конструктор
     * @param string ID  компонента
     * @param string  адрес  изображения
     */
    public function __construct($id, $src = "") {
        parent::__construct($id);
        $this->src = $src;
    }

    /**
     * Тип  подгрузки изображения
     *
     * @param mixed $type
     * Возможные   вариантыЖ
     * Image::$DEFAULT_TYPE  - обычный  путь  к  файлу   в  атрибуте  src - по  умоляанию
     * Image::$URLDATA_TYPE  - URL Data
     * Image::$DYNAMIC_TYPE  - динамическое   формирование  изображение  в  методе binaryOutput
     *
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * адрес  изображения
     *
     * @param mixed $src
     */
    public function setUrl($src) {
        $this->src = $src;
    }

    /**
     * Записывает  значение  src  в зависимости  от  типа
     * *
     */
    private function setSource() {
        if ($this->type == self::$DEFAULT_TYPE) {
            if (strlen($this->src) > 0) {
                $this->setAttribute("src", $this->src);
            }
        }

        if ($this->type == self::$URLDATA_TYPE) {
            $this->setAttribute("src", $this->getURLData());
        }

        if ($this->type == self::$DYNAMIC_TYPE) {
            $this->setAttribute("src", $this->owner->getURLNode() . "::" . $this->id . ':' . time() . '&binary=true');
        }
    }

    /**
     * Кодирование  URL Data
     */
    protected function getURLData() {
        $data = base64_encode(file_get_contents($this->src));
        $im = getimagesize($this->src);
        $data = "data:" . $im['mime'] . ";base64," . $data;

        return $data;
    }

    /**
     * рендеринг
     * @see Zippy\Html\HtmlComponent
     */
    protected function RenderImpl() {

        if (strlen($this->title) > 0) {
            $this->setAttribute("title", $this->title);
        }

        $this->setSource();
    }

    /**
     * Асинхронное  обновление  изображения
     * @see  \Zippy\Interfaces\AjaxRender
     */
    public function AjaxAnswer() {
        $this->setSource();
        $_src = $this->getAttribute('src');
        return "$('#{$this->id}').attr('src','{$_src}')";
    }

    /**
     * Формирует  бинарный   выходной   поток
     * по умолчанию  считывает имя файла с src
     * переопределяется   в  классе наследннике для реализации собственного вывода
     * Должна  заканчиваться die
     */
    protected function binaryOutput() {
        $data = file_get_contents($this->src);
        $im = getimagesize($this->src);
        header('Content-Length: ' . strlen($data));
        header("Content-type: " . $im['mime']);
        echo $data;
        flush();
        die;
    }

    /**
     * Обработчик  запроса для   бинарного  вывода
     * @see  \Zippy\Interfaces\Requestable
     */
    public function RequestHandle() {
        $this->binaryOutput();
    }

}

<?php

namespace Zippy\Html\Form;

/**
 * Компонент  тэга  &lt;input type=&quot;file&quot;&gt; для загрузки  файла
 */
class File extends HtmlFormDataElement
{

    /**
     * Конструктор
     * @param mixed  ID
     */
    public function __construct($id, $multy = false) {
        parent::__construct($id);
        $this->setAttribute("name", $this->id);
        if ($multy) {
            $this->setAttribute("name", $this->id . '[]');
            $this->setAttribute("multiple", 'multiple');
        }
    }

    /**
     * Возвращает массив   с  описанием загруженного  файла - элемент массива  $_FILES
     */
    public function getFile() {
        return $this->getValue();
    }

    /**
     * @see SubmitDataRequest
     */
    public function getRequestData() {
        if(!isset($_REQUEST[$this->id])) return;
        
        $this->setValue($_FILES[$this->id]);
    }

    /*
      public function setFile($path) {

      $files = array();
      if(!file_exists($path)) return;
      $p = pathinfo($path);
      $files['name']  = $p['basename'];
      $files['size']   = filesize($path);
      $tmp = tempnam(sys_get_temp_dir(),'tst');
      $files['tmp_name']  = $tmp;
      copy($path,$tmp);
      $this->setValue($files);
      } */


    public function clean() {

    }
}

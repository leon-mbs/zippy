<?php 

namespace Zippy\Html\DataList;

use Zippy\Html\HtmlComponent;
use Zippy\Html\HtmlContainer;
use Zippy\Interfaces\DataItem;
use Zippy\Html\Form\RadioButton;
use Zippy\Exception as ZE;

/**
 * Класс  строки табличных  данных
 */
class DataRow extends HtmlContainer
{
    private $number;
    private $dataitem = null;

    /**
     *  Конструктор
     * @param DataItem Елемент данных  отображаемый  строкой  таблицы
     * @param mixed Номер строки
     */
    public function __construct($id, DataItem $dataitem, $number) {

        $this->number = $number;
        HtmlComponent::__construct($id . "_" . $this->number);
        $this->dataitem = $dataitem;



    }

    /**
     * Установить данные  в  строку
     * @param DataItem Елемент данных  отображаемый  строкой  таблицы
     */
    public function setDataItem($dataitem) {
        $this->dataitem = $dataitem;
    }

    /**
     * Возвращает уникальный  ключ  елемента  данных
     * @return  string
     */
    public function getItemId() {
        if ($this->dataitem instanceof DataItem) {
            return $this->dataitem->getID();
        } else {
            return null;
        }
    }

    /**
     * Возвращает  елемент данных
     * @return DataItem
     */
    public function getDataItem() {
        return $this->dataitem;
    }

    /**
     * Добавляет  компонент  в строку-контейнер
     * переопределяет  родительский  метод
     * @param HtmlComponent добавляемый  компонент
     */
    public function add(HtmlComponent $component) {

        if (isset($this->components[$component->id]) || isset($this->components[$component->id . ":" . $this->number])) {
            //   $id = substr($component->id,0,strpos($component->id,':'));
            //   $pid = substr($this->id,0,strpos($this->id,':'));
            throw new ZE(sprintf(ERROR_DATAROW_COMPONENT_EXISTS, $component->id));
        }
        $this->components[$component->id] = $component;
        $component->setOwner($this);
        return $component;
    }

    /**
     *  Обновляет  ID компонентов  строки  с  воответствии с  номером  строки
     */
    public function updateChildId() {
        $allchild = $this->getChildComponents(true);
        foreach ($allchild as $component) {
            $_id = $component->id;
            $id = "_" . $this->number;
            $component->id .= $id;
            $attrid = $component->getAttribute("id");

            if ($attrid != null) {
                $component->setAttribute("id", $attrid . $id);
            }
            $attrid = $component->getAttribute("name");
            if ($attrid != null && $component instanceof RadioButton == false) {
                $component->setAttribute("name", $attrid . $id);
            }
            unset($component->owner->components[$_id]);
            $component->owner->components[$component->id] = $component;

            if ($component instanceof AbstractList) {
                $component->Refresh();
            }
        }
    }


    /**
      * уникальный  идентификатор  строки
      * @return  int
      */
    public function getNumber() {
        return $this->number;
    }



    /**
     * Возвращает дочерний элемент  по  ID
     * @return
     */
    public function getChildElement($id) {
        return $this->{$id . '_' . $this->getNumber()};
    }

    /**
     * Получить  дочерний   компонент
     *
     * @param string  ID компонента
     * @param boolean Если  false  - искать  только непосредственнно  вложенных
     */
    public function getComponent($id, $desc = true) {
        $c = parent::getComponent($id, $desc);
        if ($c instanceof HtmlComponent) {
            return $c;
        }
        $id = $id . '_' . $this->number;
        return parent::getComponent($id, $desc);
    }

}

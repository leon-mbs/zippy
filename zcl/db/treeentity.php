<?php

namespace ZCL\DB;


abstract class TreeEntity extends Entity
{

    /**
     * Возврашает  метаданные для   выборки  из  БД
     * Реализуется  конкретными  сущностями имплементирующими  класс  Entity
     * Метаданные  содержат  имя  таблицы, имя  ключевого  поля
     * а  также  имя  представления  если  такое  существует  в  БД
     * Например  array('table' => 'system_users','view' => 'system_users_view', 'keyfield' => 'user_id')
     * Для  работы   с  данными  иерархическогго типа вводятся  дополнительные   параметрыЖ
     * 'parentfield' - поле с родительскич id и 'pathfield' - строчное  поле хранения материализованного
     *  пути
     *
     * Вместо  испоользования  метода   можно  импользоввать  аннтации  возде  определения  класса
     * анноации  именуются   аналогично  ключам  массива метаданных.
     */
    protected static function getMetadata()
    {

        $class = new \ReflectionClass(get_called_class());
        $doc = $class->getDocComment();
        preg_match_all('/@([a-z0-9_-]+)=([^\n]+)/is', $doc, $arr);
        if (is_array($arr)) {
            $reg_arr = array_combine($arr[1], $arr[2]);

            $table = trim($reg_arr['table']);
            $view = trim($reg_arr['view']);
            $keyfield = trim($reg_arr['keyfield']);
            $parentfield = trim($reg_arr['parentfield']);
            $pathfield = trim($reg_arr['pathfield']);


            if (strlen($table) > 0 && strlen($keyfield) > 0 && strlen($parentfield) > 0 && strlen($pathfield) > 0) {
                $retarr = array();
                $retarr['table'] = $table;
                $retarr['keyfield'] = $keyfield;
                $retarr['parentfield'] = $parentfield;
                $retarr['pathfield'] = $pathfield;
                if (strlen($view) > 0)
                    $retarr['view'] = $view;

                return $retarr;
            }
        }
        throw new \Zippy\Exception('getMetadata должен  быть  перегружен');
    }

    /**
     * Создает  дерево на  основе  иерархии  сущностей
     *
     * @param mixed $tree Ссылка  на  компонент  \ZCL\Tree\Tree
     * @param mixed $fname Наименование  поля  сущности,  значение которого  будет  использовано
     * именования узла  дерева
     * @param mixed $rootname Имя  виртуального  корневого  узла, если  есть
     * необходимость  чтобы  дерево  имело  один  корневой  узед
     */
    public static function generateTree(\ZCL\Tree\Tree $tree, $fname, $rootname = "Root")
    {
        $tree->removeNodes();

        $class = get_called_class();
        $meta = $class::getMetadata();

        $itemlist = $class::find('', $meta['pathfield'] . "," . $fname);
        if (count($itemlist) == 0) { //добавляем  корень
            $root = new $class();
            $root->{$fname} = $rootname;
            $root->save();
            $itemlist[0] = $root;
        }
        $first = null;
        $nodelist = array();
        foreach ($itemlist as $item) {
            $node = new \ZCL\Tree\TreeNode($item->{$fname}, $item);
            $parentnode = @$nodelist[$item->{$meta['parentfield']}];

            $tree->addNode($node, $parentnode);

            $nodelist[$item->{$meta['keyfield']}] = $node;
            if ($first == null)
                $first = $node;
        }

        $tree->setSelectedNode($first);
        $first->setSelected(true);
        $first->setActive(true);
    }

    /**
     * @see Entity
     */
    public static function delete($id)
    {
        $class = get_called_class();
        $meta = $class::getMetadata();

        if (is_numeric($id)) {
            

            $obj = $class::load($id);
        } else {
            $obj = $id;
        }
        $alowdelete = true;
        if ($obj instanceof Entity) {
            $alowdelete = $obj->beforeDelete();
        } else {
            return false;
        }
        if ($alowdelete === false) {
            return false;
        }
        $alowdelete = $obj->deleteChildren();
        if ($alowdelete === false) {
            return false;
        }
        $conn = DB::getConnect();
        $conn->Execute("delete from {$meta['table']}  where {$meta['keyfield']} = " . $id);

        return true;
    }

    /**
     * Перемешение  узла  дерева  в  другой   ужел
     *
     * @param mixed $pid Id узла перемешения
     */
    public function moveTo($pid)
    {
        $class = get_called_class();
        $meta = $class::getMetadata();
        $old = sprintf('%08s', $this->{$meta['parentfield']});
        $this->{$meta['parentfield']} = $pid;
        $new = sprintf('%08s', $this->{$meta['parentfield']});
        $this->save();

        $children = $this->getChildren(true);
        foreach ($children as $child) {
            $child->{$meta['pathfield']} = str_replace($old, $new, $child->{$meta['pathfield']});
            $child->save();
        }
    }

    /*
      public function copyTo($pid)
      {

      $class = get_called_class();
      $meta = $class::getMetadata();
      }
     */

    public function getParent()
    {

        $class = get_called_class();
        $meta = $class::getMetadata();

        return;
        //$class::load($this->{$meta['parentfield']});
    }

    /**
     * Получение  дочерних  узлов
     *
     * @param mixed $all Если  false  получаем только  непостредственнвх потомков
     */
    public function getChildren($all = false)
    {
        $conn = DB::getConnect();
        $class = get_called_class();
        $meta = $class::getMetadata();

        if (!$all) {
            return self::find($meta['parentfield'] . '=' . $this->fields[$meta['keyfield']]);
        } else {
            return self::find($meta['keyfield'] . ' <> ' . $this->fields[$meta['keyfield']] . ' and ' . $meta['pathfield'] . " like " . $this->qstr('%' . sprintf('%08s', $this->fields[$meta['keyfield']]) . '%'));
        }
    }

    /**
     * Удаление  узла
     *
     * @param mixed $rec Ксли  true  дочерние  узлы  удаляются  рекурсивно,
     *  иначе  удаляются  одним  запросом  к  БД
     */
    public function deleteChildren($rec = true)
    {
        $conn = DB::getConnect();
        $class = get_called_class();
        $meta = $class::getMetadata();
        if ($rec) {
            $children = $this->getChildren();
            foreach ($children as $child) {
                $b = $class::delete($child->getID());
                if ($b == false) {
                    return false;
                }
            }
        } else {
            $id = $this->fields[$meta['keyfield']];
            $conn->Execute("delete from {$meta['table']}  where " . $meta['pathfield'] . " like " . qstr('%' . sprintf('%08s', $id) . '%') . " and {$meta['keyfield']} != " . $id);
            return true;
        }
        return true;
    }

    /**
     *
     * @see   Entity
     */
    protected function afterSave($update)
    {
        $meta = $this->getMetadata();
        if (strlen($this->{$meta['pathfield']}) > 0) {
            return;
        }

        $class = get_called_class();
 

        $this->{$meta['pathfield']} = sprintf('%08s', $this->{$meta['keyfield']});
        if ($this->{$meta['parentfield']} > 0) {
            $parent = $class::load($this->{$meta['parentfield']});
            $this->{$meta['pathfield']} = $parent->{$meta['pathfield']} . sprintf('%08s', $this->{$meta['keyfield']});
        }
        $conn = \ZDB\DB::getConnect();
        $conn->Execute("UPDATE {$meta['table']} set  {$meta['pathfield']} ='" . $this->{$meta['pathfield']} . "' where  {$meta['keyfield']} = " . $this->{$meta['keyfield']});
    }

}

?>

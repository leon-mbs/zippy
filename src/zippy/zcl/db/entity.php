<?php

namespace ZCL\DB;

/**
 * Базовый  класс  для  бизнес-сущностей
 * Реализует паттерн  Active Directory
 * Предназначен  для   автоматизации стандартных  над  записями  в  БД
 */
abstract class Entity implements \Zippy\Interfaces\DataItem
{

    
    protected $fields = array();  //список  полей

    /**
     * Конструктор
     * 
     * @param mixed $row  массив инициализирующий некторые 
     * или  все  поля объекта
     * 
     */

    function __construct($row = null)
    {
        $this->init();
        if (is_array($row)) {
            $this->fields = array_merge($this->fields, $row);
        }
    }

    /**
     * Инициализация полей  сущности
     * 
     */
    protected function init()
    {
        $meta = $this->getMetadata();
        $this->{$meta['keyfield']} = 0;
    }

    /**
     * Устанавливает  значение поля
     * 
     * @param mixed $name
     * @param mixed $value
     */
    public final function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    /**
     * Возвращает  значение  поля
     * 
     * @param mixed $name
     * @return mixed
     */
    public final function __get($name)
    {
        return $this->fields[$name];
    }

    /**
     * Возвращает поля сущности  в  виде  ассоциативного  массива
     * 
     */
    public final function getData()
    {
        return $this->fields;
    }

    /**
     * Возвращает значение  уникального  ключа  сущности
     * 
     */
    public final function getID()
    {
        $meta = $this->getMetadata();
        return $this->fields[$meta['keyfield']];
    }

    /**
     * Возвращает  сущность  из  БД по  ключу
     * @param mixed $param
     */
    public static function load($param)
    {

        $class = get_called_class();

        $meta = $class::getMetadata();
        $table = isset($meta['view']) ? $meta['view'] : $meta['table'];
        $conn = DB::getConnect();
        if (is_numeric($param)) {
            $row = $conn->GetRow("select * from {$table}  where {$meta['keyfield']} = " . $param);
        } else
        if (is_array($param)) {
            $row = $param;
        }

        if (count($row) == 0) {
            return false;
        }
        $obj = new $class();
        $obj->fields = array_merge($obj->fields, $row);
        $obj->afterLoad();
        return $obj;
    }

    /**
     * Возвращает  количество  сущностй  в  БД  по  критерию
     * 
     * @param mixed $where
     */
    public static function findCnt($where = "")
    {
        $class = get_called_class();
        $meta = $class::getMetadata();
        $conn = DB::getConnect();
        $list = array();
        $table = isset($meta['view']) ? $meta['view'] : $meta['table'];
        $sql = "select coalesce(count({$meta['keyfield']}),0) as  cnt from " . $table;


        if (strlen($where) > 0) {
            $sql .= " where " . $where;
        }


        return $conn->getOne($sql);
    }

    /**
     * Возвращает  массив  сущностей  из  БД  по  критерию
     * 
     * @param mixed $where   Условие  для предиката where
     * @param mixed $orderbyfield
     * @param mixed $orderbydir
     * @param mixed $count
     * @param mixed $offset
     */
    public static function find($where = '', $orderbyfield = null, $orderbydir = null, $count = -1, $offset = -1)
    {

        $class = get_called_class();
        $meta = $class::getMetadata();
        $table = isset($meta['view']) ? $meta['view'] : $meta['table'];
        $conn = DB::getConnect();
        $list = array();
        $sql = "select * from " . $table;

        if (strlen($where) > 0) {
            $sql .= " where " . $where;
        }

        if (strlen($orderbyfield) > 0) {
            $sql .= " order by " . $orderbyfield;
            if (strlen($orderbydir) > 0) {
                $sql .= " " . $orderbydir;
            }
        }

        if (offset >= 0 or $count >= 0) {
            $rs = $conn->SelectLimit($sql, $count, $offset);
        } else {
            $rs = $conn->Execute($sql);
        }

        foreach ($rs as $row) {
            $list[$row[$meta['keyfield']]] = new $class($row);
            $list[$row[$meta['keyfield']]]->afterLoad();
        }
        return $list;
    }

    public static function findBySql($sql)
    {

        $class = get_called_class();
        $meta = $class::getMetadata();
        $table = isset($meta['view']) ? $meta['view'] : $meta['table'];
        $conn = DB::getConnect();
        $list = array();

        $rs = $conn->Execute($sql);

        foreach ($rs as $row) {
            $list[$row[$meta['keyfield']]] = new $class($row);
            $list[$row[$meta['keyfield']]]->afterLoad();
        }
        return $list;
    }

    /**
     * Возвращает  массив ключ/имя  из  БД  по  критерию
     * Может  использоватся  для заполнения выпадающих списков 
     * 
     * @param string $fieldname   Имя  поля представляющее имя сущности.
     * @param mixed $where   Условие  для предиката where
     * @param mixed $orderbyfield
     * @param mixed $orderbydir
     * @param mixed $count
     * @param mixed $offset         */
    public static function findArray($fieldname, $where = '', $orderbyfield = null, $orderbydir = null, $count = -1, $offset = -1)
    {
        $entitylist = self::find($where, $orderbyfield, $orderbydir, $count, $offset);

        $list = array();
        foreach ($entitylist as $key => $value) {
            $list[$key] = $value->{$fieldname};
        }

        return $list;
    }

    /**
     * Возвращает  одну  строку  из набора
     *         
     * @param mixed $where
     * @param mixed $orderbyfield
     * @param mixed $orderbydir
     */
    public static function findOne($where = "", $orderbyfield = null, $orderbydir = null)
    {
        $list = self::find($where, $orderbyfield, $orderbydir);

        if (count($list) == 0) {
            return null;
        }
        if (count($list) == 1) {

            return array_pop($list);
        }
        if (count($list) > 1) {
            throw new \Zippy\Exception("Метод findOne вернул  больше  одной  записи. Условие: [{$where}]");
        }
    }

    /**
     * Сохраняет  сущность  в  БД
     * Если  сущность новая создает запись 
     * 
     */
    public function save()
    {

        if ($this->beforeSave() === false) {
            return;
        };
        $conn = DB::getConnect();
        $meta = $this->getMetadata();
        if ($this->fields[$meta['keyfield']] > 0) {
            $conn->AutoExecute($meta['table'], $this->fields, "UPDATE", "{$meta['keyfield']} = " . $this->fields[$meta['keyfield']]);
            $this->afterSave(true);
        } else {
            $conn->AutoExecute($meta['table'], $this->fields, "INSERT");
            $this->fields[$meta['keyfield']] = $conn->Insert_ID();
            $this->afterSave(false);
        }
    }

    /**
     * Удаление  сущности
     * возвращает  false  если   удаление неудачно  или  не  разрешено
     * @param mixed $id Может быть  числом (ключевое поле),  экземпляром  Entity или  срокой с  условием  для  where
     */
    public static function delete($id)
    {
        $class = get_called_class();
        $meta = $class::getMetadata();

        if (is_numeric($id)) {

            $obj = $class::load($id);
        } else {
            $obj = $id;
            $id = @$obj->{$meta['keyfield']};
        }
        $alowdelete = true;
        if ($obj instanceof Entity) {
            $alowdelete = $obj->beforeDelete();
            if ($alowdelete === false) {
                throw new \Zippy\Exception("Объект '{$meta['table']}' ({$id}) не может быть удален");
                return false;
            }

            $sql = "delete from {$meta['table']}  where {$meta['keyfield']} = " . $id;
        } else {   //если  строка
            $arr = $class::find($id);
            foreach ($arr as $obj) {
                $alowdelete = $obj->beforeDelete();
                if ($alowdelete === false) {
                    throw new \Zippy\Exception("Объект '{$meta['table']}' ({$id}) не может быть удален");
                    return false;
                }
            }
            $sql = "delete from {$meta['table']}  where " . $id;
        }

        $conn = DB::getConnect();
        $conn->Execute($sql);
        return true;
    }

    /**
     * Возврашает  метаданные для   выборки  из  БД
     * Реализуется  конкретными  сущностями имплементирующими  класс  Entity
     * Метаданные  содержат  имя  таблицы, имя  ключевого  поля
     * а  также  имя  представления  если  такое  существует  в  БД
     * Например  array('table' => 'system_users','view' => 'system_users_view', 'keyfield' => 'user_id')
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
            $view = trim(@$reg_arr['view']);
            $keyfield = trim($reg_arr['keyfield']);


            if (strlen($table) > 0 && strlen($keyfield) > 0) {
                $retarr = array();
                $retarr['table'] = $table;
                $retarr['keyfield'] = $keyfield;
                if (strlen($view) > 0)
                    $retarr['view'] = $view;

                return $retarr;
            }
        }



        throw new \Zippy\Exception('getMetadata должен  быть  перегружен');
    }

    /**
     * Вызывается  перед сохранением  сущности
     * Если  возвращает  false  сохранение  отменяется
     * 
     */
    protected function beforeSave()
    {
        return true;
    }

    /**
     * Вызывается  после  сохранения сущности
     * 
     * @param mixed $update - true  если обновление
     */
    protected function afterSave($update)
    {
        
    }

    /**
     * Вызывается   после  загрузки  сущности  из  БД
     * 
     */
    protected function afterLoad()
    {
        
    }

    /**
     * Выщывается перед  удалением  сущности
     * если  возвращает  false  удаление  отеняется
     * 
     */
    protected function beforeDelete()
    {
        return true;
    }

    /**
     * Обработка строки  перед вставкой   в  запрос
     * после  обработки  строка  не  требует кавыек
     * @param mixed $str
     */
    public static function qstr($str)
    {
        $conn = DB::getConnect();
        return $conn->qstr($str);
    }

    public static function escape($str)
    {
        $conn = DB::getConnect();
        return mysqli_real_escape_string($conn->_connectionID, $str);
    }

}

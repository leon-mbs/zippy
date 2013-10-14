<?php

namespace Zippy;

define('ZIPPY_DIR', __DIR__ . '/');

require_once(ZIPPY_DIR . "lang/ru.php");

$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_QUOTE_FIELDNAMES = true;

//автолоад  классов
function autoload($className)
{
        $className = ltrim($className, '\\');

        if (strpos($className, 'ZCL\\') === 0) {
                $path = ZIPPY_DIR . 'zcl/' . strtolower(str_replace('\\', DIRECTORY_SEPARATOR, str_replace('ZCL\\', '', $className))) . '.php';
        } else
        if (strpos($className, 'Zippy\\') === 0) {
                $path = ZIPPY_DIR . 'core/' . strtolower(str_replace('\\', DIRECTORY_SEPARATOR, str_replace('Zippy\\', '', $className))) . '.php';
        } else {
                return;
        }
        require_once $path;
}

spl_autoload_register('\Zippy\autoload');


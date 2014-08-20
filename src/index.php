<?php

define("_ROOT", __DIR__ . '/');

require_once _ROOT . 'vendor/autoload.php';
require_once _ROOT . 'zippy/zippy.inc.php';

//загрузка классов  страниц и  других  пользовательских  классов
function autoload($className)
{
    $className = ltrim($className, '\\');

    if (strpos($className, 'Pages\\') === 0) {
        $path = _ROOT . "pages/" . strtolower(str_replace("Pages\\", "", $className)) . ".php";
        require_once($path);
    }
}

spl_autoload_register('autoload');

class Application extends \Zippy\WebApplication
{

    public function getTemplate($name)
    {
        //загрузка  шаблонов  для  страниц
        $path = _ROOT . "templates/" . strtolower(str_replace("Pages\\", "", $name)) . ".html";

        $template = file_get_contents($path);
        if ($template == false) {
            new \Exception('Неверный путь к шаблону страницы: ' . $path);
        }

        return $template;
    }

}

session_start();

try {

    $app = new Application('Pages\Main');

    $app->Run();
} catch (\Zippy\Exception $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}


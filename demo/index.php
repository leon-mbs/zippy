<?php

define("_ROOT", __DIR__ . '/');

require_once _ROOT . 'vendor/autoload.php';
 


//загрузка классов  страниц и  других  пользовательских  классов
function autoload($className)
{
    $className = str_replace("\\", "/", ltrim($className, '\\'));

    if (strpos($className, 'Pages/') === 0) {
        $path = _ROOT . "pages/" . strtolower(str_replace("Pages/", "", $className)) . ".php";

        require_once($path);
    }
}

spl_autoload_register('autoload');

session_start();

class Application extends \Zippy\WebApplication
{
  
    public function getTemplate($name)
    {
        //загрузка  шаблонов  для  страниц
     
        $name = str_replace("Pages\\", "", ltrim($name, '\\'));

        $path = _ROOT ."templates/" .  strtolower($name) . ".html";
 

        $template = file_get_contents($path);
        if ($template == false) {
            new \Exception('Неверный путь к шаблону страницы: ' . $path);
        }

        return $template;
    }


    public function Route($uri){
         if($uri == '')  $uri = 'page1';
         
         $uria= explode("/",$uri);
         
         if($uria[0]=='page1'){
            $this->LoadPage("\\Pages\\Page1");
         }
         else if ($uria[0]=='page2'){    
            $this->LoadPage("\\Pages\\Page2",$uria[1]); //страница с параметром
         }
         else {
            $this->getResponse()->to404Page() ;   
         }
    }

}



try {

    $app = new Application('Pages\Main');

    $app->Run();

} catch (\Zippy\Exception $e) {
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}


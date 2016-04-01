<?php

namespace Zippy;

define('ZIPPY_DIR', __DIR__ . '/');

require_once(ZIPPY_DIR . "core/constants.php");

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

//Для Composer
class Installer 
{
    public static function postCreateProject($event){
      
    }
    public static function postUpdate($event){
        $params = $event->getComposer()->getPackage()->getExtra();
    }
    public static function postInstall($event){
       $package = $event->getOperation()->getPackage();
       $installationManager = $event->getComposer()->getInstallationManager();

       $originDir = $installationManager->getInstallPath($package);
       $localRepository = $repositoryManager->getLocalRepository();

        $packages = $localRepository->getPackages();
        foreach ($packages as $package) {
            if ($package->getName() === 'leon-mbs/zippy') {
                $installPath = $installationManager->getInstallPath($package);
                break;
            }
        }       
        $event->getIO()->write($originDir);
        $event->getIO()->write($installPath);
    }
    
}
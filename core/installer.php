<?php
namespace Zippy;

  
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

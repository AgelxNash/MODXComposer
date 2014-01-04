<?php namespace Modxcomposer\Script;
use Composer\Script\Event;
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 6:17
 */

class Install{
    public static function postPackageUpdate(Event $event)
    {
        /*$composer = $event->getComposer();
        echo "<pre>";
        print_r($composer->getPackage());
        echo "</pre>";
        die();*/
    }

    public static function postPackageInstall(Event $event)
    {
        /** @var \Composer\Package\Package $installedPackage */
        $installedPackage = $event->getOperation()->getPackage();
        $data = $installedPackage->getExtra();
        if(isset($data['modx-seed']) && file_exists($data['modx-seed'])){
            $className = include_once($data['modx-seed']);
            if(!empty($className)){
                global $modx;
                $className::run($modx, $event->getComposer(), $installedPackage);
            }
        }
    }

    public static function packageUninstall(Event $event)
    {
        /*$composer = $event->getComposer();
        echo "<pre>";
        print_r($composer->getPackage());
        echo "</pre>";
        die();*/
    }
}
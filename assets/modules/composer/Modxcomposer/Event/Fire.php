<?php namespace Modxcomposer\Event;
use Composer\Script\Event;

/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 3:42
 */

class Fire
{
    public static function postUpdate(Event $event)
    {
        $composer = $event->getComposer();
        // do stuff
    }

    public static function postPackageInstall(Event $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        // do stuff
    }

    public static function warmCache(Event $event)
    {
        // make cache toasty
    }
}
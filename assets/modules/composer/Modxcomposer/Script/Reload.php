<?php namespace Modxcomposer\Script;
use Composer\Script\Event;

class Reload{
    protected static $IO = null;

    public static function clearCache(Event $event){
        self::$IO = $event->getIO();
        $modx = self::getMODX();
        if($modx && $modx->clearCache()){
            self::$IO->write("<info>Кеш сайта удален</info>");
        }else{
            self::$IO->write("<error>Не удалось почистить кеш</error>");
        }
    }

    protected static function getMODX(){
        global $modx;
        if( ! $modx instanceof \DocumentParser){
            self::$IO->write("<error>Экземпляр класса DocumentParser не обнаружен</error>");
        }
        return $modx;
    }
}
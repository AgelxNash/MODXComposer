<?php namespace Modxcomposer\Helper;
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 6:04
 */

class FileSystem{
    public static function delete(array $path = array()){
        foreach($path as $item){
            switch(true){
                case is_dir($item):{
                    self::deleteDirectory($item);
                    break;
                }
                case is_file($item):{
                    self::deleteFile($item);
                    break;
                }
            }
        }
    }
    public static function deleteFile($file){
        if(file_exists($file) && is_readable($file)){
            unlink($file);
        }
    }
    public static function deleteDirectory($directory)
    {
        if(!$dh=opendir($directory))
        {
            return false;
        }

        while($file=readdir($dh))
        {
            if($file == "." || $file == "..")
            {
                continue;
            }

            if(is_dir($directory."/".$file))
            {
                FileSystem::deleteDirectory($directory."/".$file);
            }

            if(is_file($directory."/".$file))
            {
                unlink($directory."/".$file);
            }
        }

        closedir($dh);

        rmdir($directory);
    }
}
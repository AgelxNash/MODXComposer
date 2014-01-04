<?php namespace Modxcomposer\Install;
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 4:16
 */
class Unpack{
    public static function run(){
        $out = '';
        if ( ! file_exists(MODX_BASE_PATH.'composer/vendor/autoload.php')) {
            $composerPhar = new \Phar(MODX_BASE_PATH."Composer.phar");
            //php.ini setting phar.readonly must be set to 0
            $data = $composerPhar->extractTo(MODX_BASE_PATH.'/composer/');
            if($data){
                $out = 'composer.phar распакован в папку '.MODX_BASE_PATH.'/composer/';
                file_put_contents(MODX_BASE_PATH.'/composer/.htaccess', "Order deny,allow\r\nDeny from all");
            }else{
                $out = 'Не удалось распаковать composer.phar';
            }
        }
        return $out;
    }
}
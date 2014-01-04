<?php namespace Modxcomposer\Install;

class Download{
    public function __construct(){
        if( ! file_exists(MODX_BASE_PATH."/composer.phar")){
            ob_start();
            register_shutdown_function(function(){
                $data = ob_get_contents();
                ob_end_clean();
                if(stristr($data,"Composer successfully installed to:")){

                    parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $query);
                    $query['action'] = 'unpack';
                    $url = MODX_MANAGER_URL."?".http_build_query($query);

                    header("Location: ".$url);
                    if(file_exists(MODX_MANAGER_PATH."composer.phar")){
                        copy(MODX_MANAGER_PATH."composer.phar", MODX_BASE_PATH."composer.phar");
                        unlink(MODX_MANAGER_PATH."composer.phar");
                    }
                    $data = '';
                    if( file_exists(MODX_BASE_PATH.".htaccess")){
                        $data = file_get_contents(MODX_BASE_PATH.".htaccess");
                    }
                    if( ! stristr($data, '<FilesMatch "composer.(json|phar|lock)$">')){
                        file_put_contents(MODX_BASE_PATH.".htaccess", '
<FilesMatch "composer.(json|phar|lock)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>', FILE_APPEND);
                    }
                }else{
                    echo "<h1>Bad install</h1><pre>{$data}</pre>";
                }
            });

            eval('?>'.file_get_contents('https://getcomposer.org/installer'));
        }
    }
}
<?php namespace Modxcomposer;
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 3:44
 */

class Modxcomposer{
    protected $_modx = null;

    public function __construct(\DocumentParser $modx){
        $this->_modx = $modx;
        chdir(MODX_BASE_PATH);
        $this->_validateComposer();
    }
    protected function _validateComposer(){
        if( ! file_exists(MODX_BASE_PATH."composer.phar")){
            $this->selfUpdate();
        }

        if ( ! file_exists(MODX_BASE_PATH.'composer/vendor/autoload.php')) {
            $this->unpack();
        }

        if ( file_exists(MODX_BASE_PATH.'composer/vendor/autoload.php')) {
            require_once (MODX_BASE_PATH.'composer/vendor/autoload.php');
        }

        if ( ! file_exists(MODX_BASE_PATH."composer.json")){
            new Install\Init();
        }
    }
    public function getComposerVersion(){
        return @constant('\Composer\Composer::VERSION');
    }

    public function getComposerDate(){
        return @constant('\Composer\Composer::RELEASE_DATE');
    }

    protected function getOutput(){
        $styles = \Composer\Factory::createAdditionalStyles();
        $formatter = new \Composer\Console\HtmlOutputFormatter($styles);

        return new \Modxcomposer\Output\DisplayOutput(\Modxcomposer\Output\DisplayOutput::VERBOSITY_NORMAL, true, $formatter);
    }

    public function update(\Symfony\Component\Console\Output\StreamOutput $out = null){
        if(empty($out)){
            $out = $this->getOutput();
        }
        $input = new \Symfony\Component\Console\Input\ArrayInput(array('command' => 'update'));

        $application = new \Composer\Console\Application();
        $application->setAutoExit(false);

        $application->run($input, $out);
        return $out->getContent();
    }

    public function json($data){
        $out = $this->getOutput();
        file_put_contents(MODX_BASE_PATH."composer.json", $data);
        $out->writeln("<info>Файл composer.json обновлен</info>");

        $input = new \Symfony\Component\Console\Input\ArrayInput(array('command' => 'validate'));

        $application = new \Composer\Console\Application();
        $application->setAutoExit(false);

        $code = $application->run($input, $out);
        if($code==0){
            $out->writeln("<info>Теперь вы можете запустить комманду обновления компонентов</info>");
        }
        return $out->getContent();
    }

    public function unpack(){
        \Modxcomposer\Helper\FileSystem::delete(array(
            MODX_BASE_PATH."composer/"
        ));

        return Install\Unpack::run();
    }
    public function selfUpdate(){
        \Modxcomposer\Helper\FileSystem::delete(array(
            MODX_BASE_PATH."composer.phar"
        ));

        if( ! file_exists(MODX_BASE_PATH."composer.phar")){
            new Install\Download();
        }
    }

    public function uninstall($package, \Symfony\Component\Console\Output\StreamOutput $out = null){
        if(empty($out)){
            $out = $this->getOutput();
        }
        $out->writeln('<info>Удаление пакета '.$package.'</info>');

        if(file_exists(MODX_BASE_PATH."composer.json")){
            $main = file_get_contents(MODX_BASE_PATH."composer.json");
            $main = json_decode($main, true);
            if(isset($main['require'][$package])){
                $out->writeln('<info>Пакет исключен из composer.json</info>');
                unset($main['require'][$package]);
            }else{
                $out->writeln('<error>Пакет не обнаружен в composer.json</error>');
            }
            if(empty($main['require'])){
                unset($main['require']);
            }
            file_put_contents(MODX_BASE_PATH."composer.json", \Modxcomposer\Helper\Json::toJSON($main));

            $this->update($out);
        }else{
            $out->writeln('<error>Файл composer.json не обнаружен</error>');
        }
        return $out->getContent();
    }
    public function prepareRequire($string){
        $out = "";
        if(!is_scalar($string)){
            $string = '';
        }else{
            $string = trim($string);
        }
        if(stristr($string,'"')){
            $string = "{".$string."}";
            $string = json_decode($string, true);
            if(is_array($string)){
                $key = array_keys($string);
                $out = $key[0].":".$string[$key[0]];
            }
        }else{
            if(preg_match('#^((\w+)/(\w+)$)|((\w+)/(\w+)\:(?:\s*)([a-zA-Z0-9\.\*\@\-]+)$)#', $string, $match)){
                $out = $string;
            }
        }
        return $out;
    }
    public function requires(array $packages, \Symfony\Component\Console\Output\StreamOutput $out = null){
        if(empty($out)){
            $out = $this->getOutput();
        }
        $input = new \Symfony\Component\Console\Input\ArrayInput(array(
            'command' => 'require',
            'packages'=> $packages
        ));
        $application = new \Composer\Console\Application();
        $application->setAutoExit(false);

        $application->run($input, $out);
        return $out->getContent();
    }
}
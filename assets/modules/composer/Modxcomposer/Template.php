<?php namespace Modxcomposer;
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 04.01.14
 * Time: 1:43
 */

class Template{
    protected $_modx = null;
    protected $_tplFolder = null;

    const TPL_EXT = 'html';

    public $vars = array(
        'modx_lang_attribute',
        'modx_textdir',
        'manager_theme',
        'modx_manager_charset',
        '_lang',
        '_style',
        'e',
        'SystemAlertMsgQueque',
        'incPath',
        'content'
    );
    protected $_ajax = false;

    public function __construct(\DocumentParser $modx, $ajax = false){
        $this->_modx = $modx;
        $this->_ajax = (boolean) $ajax;
        $this->loadVars();
        $this->_tplFolder = dirname(dirname(__FILE__))."/template/";
    }
    public function isAjax(){
        return $this->_ajax;
    }
    public function showHeader(){
        return $this->_getMainTpl('header.inc.php');
    }
    protected function _getMainTpl($name){
        $content = '';
        if( ! $this->isAjax()){
            ob_start();
            extract($this->vars);
            if(file_exists($incPath . $name)){
                include($incPath . $name);
                $content = ob_get_contents();
            }
            ob_end_clean();
        }
        return $content;
    }
    public function loadVars(){
        $vars = array();
        foreach($this->vars as $item){
            global $$item;
            $vars[$item] = $$item;
        }
        $this->vars = $vars;
        $this->vars['tplClass'] = $this;
        $this->vars['modx'] = $this->_modx;
    }
    public function showFooter(){
        return $this->_getMainTpl('footer.inc.php');
    }

    public function showBody($TplName, array $tplParams = array()){
        ob_start();
        if(file_exists($this->_tplFolder.$TplName.".".self::TPL_EXT)){
            extract($this->vars);
            include($this->_tplFolder.$TplName.".".self::TPL_EXT);
        }
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function getParam($key, array $param = array(), $default = null){
        return isset($param[$key]) ? $param[$key] : $default;
    }

    public function ListActive(){
        $out = array();
        if(file_exists(MODX_BASE_PATH."vendor/composer/installed.json")){
            $json = file_get_contents(MODX_BASE_PATH."vendor/composer/installed.json");
            $json = json_decode($json, true);
        }else{
            $json = array();
        }

        if(file_exists(MODX_BASE_PATH."composer.json")){
            $main = file_get_contents(MODX_BASE_PATH."composer.json");
            $main = json_decode($main, true);
            $main = isset($main['require']) ? $main['require'] : array();
        }else{
            $main = array();
        }

        foreach($json as $num=>$item){
            $name = isset($item['name']) ? $item['name'] : '';
            $isMain = (!empty($name) && isset($main[$name]));
            $tmp = array(
                'id' => $num,
                'main' => $isMain,
            );
            $out[] = $this->showBody("componentRow", array_merge($item, $tmp));
        }
        if(!empty($out)){
            $out = $this->showBody("component", array('grid'=>implode("", $out)));
        }else{
            $out = $this->showBody("noComponent");
        }
        return $out;
    }
    public function makeUrl($action, array $data = array()){
        $action = is_scalar($action) ? $action : '';
        $content = $this->getParam('content', $this->vars, array());
        $data = array_merge($data, array(
            'a' => 112,
            'action' => $action,
            'id' => $this->getParam('id', $content, 0)
        ));
        return implode("?", array($this->_modx->getManagerPath(), http_build_query($data)));
    }
    public function InstallForm(){
        return $this->showBody("installForm", array());
    }

    public function showLog(){
        return $this->isAjax() ? 'log' : 'main';
    }

    public function configEdit(){
        if(file_exists(MODX_BASE_PATH."composer.json")){
            $out = trim(file_get_contents(MODX_BASE_PATH."composer.json"));
        }else{
            $out = "Файл composer.json не существует";
        }
        return $this->showBody("editor", array('content'=>$out));
    }
}
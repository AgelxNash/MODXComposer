<?php
/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 3:23
 */
if(IN_MANAGER_MODE!="true" || empty($modx) || !($modx instanceof DocumentParser)){
    die("<b>INCLUDE_ORDERING_ERROR</b><br /><br />Please use the MODX Content Manager instead of accessing this file directly.");
}
if(!$modx->hasPermission('exec_module')){
    header("location: ".$modx->getManagerPath()."?a=106");
}

set_time_limit(0);

include_once(dirname(__FILE__)."/SplClassLoader.class.php");
$classLoader = new SplClassLoader('Modxcomposer', dirname(__FILE__));
$classLoader->register();

$ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$MComposer = new \Modxcomposer\Modxcomposer($modx);
$TPL = new \Modxcomposer\Template($modx, $ajax);

$data = array(
    'composer-version' => $MComposer->getComposerVersion(),
    'composer-date' => $MComposer->getComposerDate()
);

$out = $TPL->showHeader();
$tpl = $TPL->showLog();

switch($action){
    case 'install':{
        if(!empty($_POST['package'])){
            $packages = is_scalar($_POST['package']) ? array($_POST['package']) : $_POST['package'];
            $install = array();
            foreach($packages as $item){
                if($package=$MComposer->prepareRequire($item)){
                    $install[] = $package;
                }
            }
            if(!empty($install)){
                $data['log'] = $MComposer->requires($install);
            }else{
                $data['log'] = 'Не удалось распознать устанавливаемый пакет';
            }
        }else{
            $data['log'] = 'Необходимо указать пакет который следует установить';
        }
        break;
    }
    case 'delete':{
        if(!empty($_GET['package'])){
            $data['log'] = $MComposer->uninstall($_GET['package']);
        }else{
            $data['log'] = 'Не указан пакет который необходимо удалить';
        }
        break;
    }
    case 'json':{
        if(!empty($_POST['json'])){
            $data['log'] = $MComposer->json($_POST['json']);
        }else{
            $data['log'] = 'Пустой пакет';
        }
        break;
    }
    case 'unpack':{
        $data['log'] = $MComposer->unpack();
        break;
    }
    case 'self-update':{
        $data['log'] = $MComposer->selfUpdate();
        break;
    }
    case 'update':{
        $data['log'] = $MComposer->update();
        break;
    }
}

$out .= $TPL->showBody($tpl, $data);
$out .= $TPL->showFooter();

echo $out;
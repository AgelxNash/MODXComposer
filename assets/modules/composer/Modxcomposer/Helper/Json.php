<?php namespace Modxcomposer\Helper;
if (!defined("JSON_ERROR_UTF8")) define("JSON_ERROR_UTF8", 5); //PHP < 5.3.3

class Json {
    protected static $_error = array(
        JSON_ERROR_NONE => 'error_none',
        JSON_ERROR_DEPTH => 'error_depth',
        JSON_ERROR_STATE_MISMATCH => 'error_state_mismatch',
        JSON_ERROR_CTRL_CHAR => 'error_ctrl_char',
        JSON_ERROR_SYNTAX => 'error_syntax',
        JSON_ERROR_UTF8 => 'error_utf8'
    );

    /**
     * Разбор JSON строки при помощи json_decode
     *
     * @param $json string строка c JSON
     * @param array $config ассоциативный массив с настройками для json_decode
     * @param bool $nop создавать ли пустой объект запрашиваемого типа
     * @return array|mixed|xNop
     */
    public static function jsonDecode($json, $config = array(), $nop = false){
        if(isset($config['assoc'])){
            $assoc = (boolean)$config['assoc'];
        }else{
            $assoc = false;
        }

        if(isset($config['depth']) && (int)$config['depth']>0){
            $depth = (int)$config['depth'];
        }else{
            $depth = 512;
        }

        $out = json_decode($json, $assoc, $depth);
        if($nop && is_null($out)){
            if($assoc){
                $out = array();
            }else{
                $out = new Xnop();
            }
        }
        return $out;
    }

    public static function toJSON(array $data = array()){
        if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            $out = json_encode($data);
            $out = str_replace('\\/', '/', $out);
        }else{
            $out = json_encode($data, JSON_UNESCAPED_SLASHES);
        }
        return self::json_format($out);
    }

    /**
     * Получение кода последенй ошибки
     * @see http://www.php.net/manual/ru/function.json-last-error-msg.php
     * @return string
     */
    public static function json_last_error_msg(){
        $error = json_last_error();
        return isset(self::$_error[$error]) ? self::$_error[$error] : 'other';
    }

    public static function json_format($json){
        $tab = "  ";
        $new_json = "";
        $indent_level = 0;
        $in_string = false;

        $json_obj = json_decode($json);
        if($json_obj === false) return false;

        $len = strlen($json);
        for($c = 0; $c < $len; $c++){
            $char = $json[$c];
            switch($char){
                case '{':
                case '[':
                    if(!$in_string){
                        $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    }
                    else{
                        $new_json .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if(!$in_string){
                        $indent_level--;
                        $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                    }else{
                        $new_json .= $char;
                    }
                    break;
                case ',':
                    if(!$in_string){
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    }else{
                        $new_json .= $char;
                    }
                    break;
                case ':':
                    if(!$in_string){
                        $new_json .= ": ";
                    }else{
                        $new_json .= $char;
                    }
                    break;
                case '"':
                    if($c > 0 && $json[$c-1] != '\\'){
                        $in_string = !$in_string;
                    }
                default:
                    $new_json .= $char;
                    break;
            }
        }

        return $new_json;
    }
}
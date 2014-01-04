<?php namespace Modxcomposer\Install;

/**
 * Created by PhpStorm.
 * User: Agel_Nash
 * Date: 03.01.14
 * Time: 4:43
 */

class Init{
    public function __construct(){
        $composer = array(
            "name" => "root/modx",
            "description" => "Description project",
            "license" => "MIT",
            "authors" => array(
                array(
                    "name" => "Agel_Nash",
                    "email" => "modx@agel-nash.ru"
                )
            ),
            "repositories" => array(
                array(
                    "type" => "package",
                    "package" => array(
                        "type" => "modxevo-snippet",
                        "name" => "agelxnash/doclister",
                        "version" => "1.2.0",
                        "dist" => array(
                            "type" => "zip",
                            "url" => "https://github.com/AgelxNash/DocLister/archive/master.zip"
                        ),
                        "require" => array(
                            "agelxnash/resource" => "*"
                        ),
                        "extra" => array(
                            "installer-name" => "DocLister"
                        )
                    )
                )
            )
        );
        file_put_contents(MODX_BASE_PATH."composer.json", \Modxcomposer\Helper\Json::toJSON($composer));
    }
}
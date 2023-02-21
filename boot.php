<?php
/*
    Copyright (c) 2021-2031, All rights reserved.
    This is NOT a freeware
    LICENSE: https://github.com/thefunpower/core/blob/main/LICENSE.md 
    Connect Email: sunkangchina@163.com 
    Code Vesion: v1.0.x
*/ 
//版本号
if(!defined('DEBUG')) define('VERSION', "1.0.1");
//线上可以关闭PHP错误提示
if(!defined('DEBUG')) define('DEBUG', true);
if(!defined('ADMIN_DIR_NAME'))define("ADMIN_DIR_NAME", 'admin-dev');
if(!defined('ADMIN_COOKIE_NAME'))define("ADMIN_COOKIE_NAME", 'user_id'); 
//定义一些路径常量一般不用修改
if(!defined('PATH')) define('PATH', realpath(__DIR__ . '/../../../').'/');
if(!defined('THE_FUN_PATH')) define('THE_FUN_PATH', realpath(PATH . 'vendor/thefunpower').'/');  
if(!defined('SYS_PATH')) define('SYS_PATH', THE_FUN_PATH . 'core/');  
/**
 * 错误提示 
 */
if (DEBUG) {
  ini_set('display_errors', 'on');
  //error_log() 函数，记录具体错误
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
  ini_set('error_log', PATH . 'data/phplog.log');
} else {
  ini_set('display_errors', 'off');
  error_reporting(0); 
}
/**
* 数据库配置
*/
include PATH.'config.ini.php';
/**
* 启动数据库连接
*/
$medoo_db_config = $config;
if(!file_exists('db_active_main')){
  include THE_FUN_PATH.'db_medoo/inc/db/boot.php';
}  
/**
* 加载autoload
*/
global $autoload;
if(!$autoload){
  $autoload = include PATH.'vendor/autoload.php';  
}
$autoload->addPsr4('app\\',PATH.'app/');
$autoload->addPsr4('service\\',PATH.'service/'); 
$autoload->addPsr4('helper\\',PATH.'helper/'); 
$autoload->addPsr4('plugins\\',PATH.'plugins/');
$autoload->addPsr4('dump\\',PATH.'dump/');
/**
 * 路由
 * https://github.com/bramus/router
 * 
location / {
  if (!-e $request_filename){
    rewrite ^(.*)$ /index.php last;
  }
}
 */
global $router;
$router = new \Bramus\Router\Router(); 
//加载常用功能
include SYS_PATH . '/boot.php'; 
//开始
do_action('init');
global $input;
$input  = g();
/**
* 加载语言包
*/
$lang = $config['lang']?:'zh-cn';
lib\Validate::lang($lang); 
/**
* 加载dump目录下的*.helper.php
*/
$dir = PATH . 'dump';  
if(is_dir($dir)){
  $all = glob($dir.'/*.helper.php'); 
  foreach($all as $v){
    include $v;
  }
}

/**
* 加载app下控制器
*/
$router->set404(function() { 
    auto_load_app_router('app');
});
<?php

namespace martist;
/**
 * Created by PhpStorm.
 * User: machuang
 * Date: 2020/5/19
 * Time: 18:58
 */

class router{

    protected $mode = 1;
    protected $controllerNamespace = 'controllers';
    protected $app_path;


    function __construct()
    {
        $this->app_path = dirname(dirname(__DIR__));
    }


    function handle(){
        if($this->mode == 1){
            //get传参方式解析方式
            $controllerName = empty($_GET['c'])?'index':$_GET['c'];
            $actionName = empty($_GET['a'])?'index':$_GET['a'];
            $ucController = ucfirst($controllerName);
            $controllerNameAll = $this->controllerNamespace . '\\' . $ucController . 'Controller';
            $controllerFile = $this->app_path.'/'.$this->controllerNamespace.'/'.$ucController . 'Controller.php';
            include_once $controllerFile;
            $controller = new $controllerNameAll();
            return call_user_func([$controller,  ucfirst($actionName)]);
        }elseif ($this->mode == 2){
            //斜杠分割路由的解析方式
            $controllerName = 'Index';
            $actionName = 'index';
            $param = array();
            $url = $_SERVER['REQUEST_URI'];
            $position = strpos($url, '?');
            $url = $position === false ? $url : substr($url, 0, $position);
            $query_str = str_replace($url,'',$_SERVER['REQUEST_URI']);
            $query_str = trim($query_str, '?');
            parse_str($query_str,$query_arr);//get方式传递参数

            $url = trim($url, '/');
            if ($url) {
                $urlArray = explode('/', $url);
                $urlArray = array_filter($urlArray);
                $controllerName = ucfirst($urlArray[0]);
                array_shift($urlArray);
                $actionName = $urlArray ? $urlArray[0] : $actionName;
                array_shift($urlArray);
//                $param = $urlArray ? $urlArray : array();
            }
            $controllerFile = $this->app_path.'/'.$this->controllerNamespace.'/'.$controllerName . 'Controller.php';
            include_once $controllerFile;
            $controller = $this->controllerNamespace . '\\' . $controllerName . 'Controller';
            if (!class_exists($controller)) {
                exit($controller . '控制器不存在');
            }
            if (!method_exists($controller, $actionName)) {
                exit($actionName . '方法不存在');
            }
            $dispatch = new $controller();
            call_user_func_array(array($dispatch, $actionName),$query_arr);

        }
    }

}
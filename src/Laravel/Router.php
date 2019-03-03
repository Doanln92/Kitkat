<?php

/**
 * @author DoanLN
 * @date 2019-03-01
 * @description 
 * cho phép lay thong tin cac route cua laravel
 * 
 */

namespace Kitkat\Laravel;

use Route;

class Router{
    //
    protected static $routes = [];
    protected static $route_methods = [];
    protected static $route_names = [];
    protected static $route_prefixes = [];
    
    /**
     * lay thong tin route
     * @param Route
     */
    public static function getRouteInfo($route)
    {
        $routeArr = ['uri'=>'','methods'=>null,'name'=>null, 'prefix'=>null];
        $inarr = ['uri','methods'];
        foreach($route as $k => $v){
            if (in_array($k,$inarr)) {
                $routeArr[$k] = $v;
            }elseif($k=='action'){
                if(array_key_exists('prefix',$v)){
                    $routeArr['prefix'] = $v['prefix'];
                }
                if(array_key_exists('as',$v)){
                    $routeArr['name'] = $v['as'];
                }
                
            }
        }
        return($routeArr);
    }

    protected static function getRouteFromLaravel(){
        //
        if(count(self::$routes)> 0) return self::$routes;
        $routes = Route::getRoutes();

        foreach($routes as  $route){
            $routeArr = self::getRouteInfo($route);
            self::addRoute($routeArr);
        }
        return self::$routes;
    }

    protected static function addRoute($route)
    {
        foreach($route['methods'] as $method){
            if (array_key_exists($method, self::$route_methods)) {
                self::$route_methods[$method] = [];
            }
            self::$route_methods[$method][$route['uri']] = $route;
        }
        self::$routes[$route['uri']] = $route;
        if($route['name']){
            self::$route_names[$route['name']] = $route['uri'];
        }
        if($route['prefix']){
            self::$route_prefixes[$route['prefix']] = $route['prefix'];
        }
    }

    public static function getRoutes($method=null)
    {
        $route = self::getRouteFromLaravel();
        if($method){
            if (array_key_exists($method, self::$route_methods)) {
                return self::$route_methods[$method];
            }
            return null;
        }
        return $route;
    }

    public static function all()
    {
        return self::getRouteFromLaravel();
    }

    public static function getByName($name=null){
        if(!$name) return null;
        self::getRouteFromLaravel();
        if (array_key_exists($name, self::$route_names)) {
            $route_uri = self::$route_names[$name];
            return self::$routes[$route_uri];
        }
        return null;
    }

    
    public static function getSelectUri(){
        $arr = [];
        if($routes = self::getRouteFromLaravel()){
            foreach($routes as $route){
                $arr[$route['uri']] = $route['uri'];
            }
        }
        return $arr;
    }

    public static function getSelectName(){
        $arr = [];
        self::getRouteFromLaravel();
        $routes = self::$route_names;
        if($routes){
            foreach($routes as $name => $uri){
                $arr[$name] = $name;
            }
        }
        return $arr;
    }

    public static function getSelectPrefix(){
        return self::$route_prefixes;
    }

    public static function getSelectNameAndUri(){
        $arr = [];
        self::getRouteFromLaravel();
        $routes = self::$route_names;
        if($routes){
            foreach($routes as $name => $uri){
                $arr[$name] = $name.' --> URI: '.$uri;
            }
        }
        return $arr;
    }
    
    public static function getSelectNameAndUriMenu(){
        $arr = [];
        self::getRouteFromLaravel();
        $routes = self::$route_names;
        if($routes){
            foreach($routes as $name => $uri){
                if(!preg_match('/^(admin\.|user\.|api\.)/i', $name))
                $arr[$name] = $uri;
            }
        }
        return $arr;
    }

    public static function getByUri($uri=null){
        if(!$uri) return null;
        self::getRouteFromLaravel();
        if (array_key_exists($uri, self::$routes)) {
            return self::$routes[$uri];
        }
        return null;
    }

    public static function checkUri($uri=null){
        if(!$uri) return null;
        self::getRouteFromLaravel();
        if (array_key_exists($uri, self::$routes)) {
            return true;;
        }
        return false;
    }
    public static function checkName($name=null){
        if(!$name) return null;
        self::getRouteFromLaravel();
        if (array_key_exists($name, self::$route_names)) {
            return true;;
        }
        return false;
    }
    public static function checkPrefix($name=null){
        if(!$name) return null;
        self::getRouteFromLaravel();
        if (array_key_exists($name, self::$route_prefixes)) {
            return true;;
        }
        return false;
    }
    
}
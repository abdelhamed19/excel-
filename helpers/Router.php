<?php
namespace helpers;
class Router{
    public array $routes;
    public function register($method ,$route, callable|array $action)
    {
        $this->routes[$method][$route] = $action;
        return $this;
    }
    public function get($route, callable|array $action)
    {
        return $this->register('get', $route, $action);
    }
    public function post($route, callable|array $action)
    {
        return $this->register('post', $route, $action);
    }
    public function routes()
    {
        return $this->routes;
    }
    public function resolve ($uri , $method)
    {
        $route = explode('?', $uri)[0];
        $action = $this->routes[$method][$route] ?? null;
        if (!$action) {
            echo '404';
            return;
        }
        
        if(is_callable($action))
        {
            return call_user_func($action);
        }
        if (is_array($action)) {
            [$class , $method] = $action;
            if (class_exists($class)) {
                $class = new $class();
                if (method_exists($class, $method)) {
                    return  call_user_func_array(([$class, $method]),[]);
                }
            }
            
        }
    }
}
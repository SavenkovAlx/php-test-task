<?php

namespace ishop;

class Router
{
    protected static $routes = [];
    protected static $route = [];

    public static function add($regexp, $route = [])
    {
        self::$routes[$regexp] = $route;
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    public static function getRoute()
    {
        return self::$route;
    }

    public static function dispatch($url)
    {
        if (self::matchRoute($url)) {
            $controller = 'app\controllers\\' . self::$route['prefix'] . self::$route['controller'] . 'Controller';
            if (class_exists($controller)) {
                $controllerObject = new $controller(self::$route);
                $action = self::lowerCamelCase(self::$route['action']) . 'Action';
                if (method_exists($controllerObject, $action)) {
                    $controllerObject->$action();
                } else {
                    throw new \Exception("Method $controller::$action not found", 404);

                }
            } else {
                throw new \Exception("Controller $controller not found", 404);

            }
        } else {
            throw new \Exception("Page not found", 404);
        }

    }

    public static function matchRoute($url)
    {
        foreach (self::$routes as $patten => $route) {
            if (preg_match("#{$patten}#", $url, $matches)) {
                foreach ($matches as $k => $v) {
                    if (is_string($k)) {
                        $route[$k] = $v;
                    }
                }
                if (empty($route['action'])) {
                    $route['action'] = 'index';
                }
                if (!isset($route['prefix'])) {
                    $route['prefix'] = '';
                } else {
                    $route['prefix'] .= '\\';
                }
                $route['controller'] = self::upperCamelCase($route['controller']);
                self::$route = $route;
                return true;
            }
        }
        return false;
    }

    // CamelCase
    protected static function upperCamelCase($name)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $name)));
    }

    // camelCase
    protected static function lowerCamelCase($name)
    {
        return lcfirst(self::upperCamelCase($name));
    }

}
<?php namespace core;

final class Route {

    private static $routes = [];
    private $controller = null;
    private $method = null;

    public function controller($routeName, $controller)
    {
        if ($this->checkRouteName($routeName))
        {
            if (!$this->isControllerMethod($controller))
            {
                if (!isset(self::$routes[$routeName]))
                {
                    self::$routes[$routeName] = $controller;
                }
            }
        }
    }

    public function controllerMethod($routeName, $controllerMethod)
    {
        if ($this->checkRouteName($routeName))
        {
            if ($this->isControllerMethod($controllerMethod))
            {
                if (!isset(self::$routes[$routeName]))
                {
                    self::$routes[$routeName] = $controllerMethod;
                }
            }
        }
    }

    private function setControllerMethod($controllerMethod)
    {
        $segments = $this->isControllerMethod($controllerMethod);
        if ($segments) {
            $this->controller = $segments[0];
            $this->method = $segments[1];
        } else {
            $this->controller = self::$routes[Get::controller()];
            $this->method = null;
        }
    }

    private function isControllerMethod($controllerMethod)
    {
        $segments = explode('@', $controllerMethod);
        if (is_array($segments) and count($segments) === 2) {
            return $segments;
        }
        return false;
    }

    public function getController()
    {
        $routeName = Get::controller();
        if (isset(self::$routes[$routeName])) {
            $this->setControllerMethod(self::$routes[$routeName]);
        } else {
            $routeName = Get::url();
            if (isset(self::$routes[$routeName])) {
                $this->setControllerMethod(self::$routes[$routeName]);
            } else {
                return null;
            }
        }
        return $this->controller;
    }

    public function getMethod()
    {
        if ($this->getController()) {
            return $this->method;
        }
        return null;
    }

    public function getParams()
    {
        if ($this->getMethod())
        {
            $routeName = array_search($this->controller . '@' . $this->method, self::$routes);
            $segments = str_replace($routeName, '', Get::url());
            if (substr($segments, 0, 1) == '/') {
                $segments = substr($segments, 1, strlen($segments)-1);
            }
            $params = explode('/', $segments);
            $params = array_values($params);
            return $params;
        }
        return null;
    }

    public static function getRoutes()
    {
        return self::$routes;
    }

    private static function checkRouteName($routeName)
    {
        $pattern = '/^([a-z]+[0-9]*[_|-]*)+([a-z0-9]*[_|-]*)*$/';
        $result = preg_match($pattern, $routeName);
        if ($result) {
            return true;
        } else {
            trigger_error('The route name "<b>'.$routeName.'</b>" is not a valid name. Follow the examples bellow: <br>1) user<br>2) user_edit<br>3) user-edit', E_USER_ERROR);
        }
    }
}
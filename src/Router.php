<?php

namespace JMolinas\SimpleRouter;

use JMolinas\SimpleRouter\HttpInterface;

class Router
{
    protected $routes = [];
    protected $arguments;
    protected $http;

    public function __construct(HttpInterface $http)
    {
        $this->http = $http;
        $this->view = $view;
    }

    private function parameters($function, $arguments)
    {
        $values = [];
        $reflector = new \ReflectionFunction($function);
        $parameters = $reflector->getParameters();
        foreach ($parameters as $key => $parameter) {
            $name = $parameter->getName();
            $isArgumentGiven = array_key_exists($name, $arguments);

            if (!$isArgumentGiven && !$param->isDefaultValueAvailable()) {
                throw new \Exception('Parameter $name is mandatory but was not provided');
            }

            $values[$param->getPosition()] = $isArgumentGiven ? $arguments[$name] : $param->getDefaultValue();
        }

        return $values;
    }

    public function route()
    {
        $numargs = func_num_args();
        $route = rtrim(func_get_arg(0), '/') . '/';
        preg_match_all('#:([\w]+)\+?#', $route, $match);
        $this->arguments = array_values($match[1]);
        $route = preg_replace('#:([\w]+)\+?#', '([a-zA-Z0-9_-]+)', $route);
        $route = str_replace('/', '\/', $route);
        $route = '/^' . $route . '?$/';
        $argumentList = func_get_args();
        $this->routes[$route] = array();

        if ($numargs >= 2) {
            for ($i = 1; $i < $numargs; $i++) {
                $this->routes[$route][] = $argumentList[$i];
            }
        }
        return $this;
    }

    public function execute()
    {
        $uri = $this->http->getUrl;
        $match = false;

        foreach ($this->routes as $pattern => $callback) {
            if (preg_match($pattern, $uri, $args) === 1) {
                $match = true;
                $arguments = [];
                array_shift($args);

                foreach ($this->arguments as $key => $value) {
                    $arguments[$value] = $args[$key];
                }

                foreach ($callback as $value) {
                    $values = $this->params($value, $arguments);
                    $function = call_user_func_array($value, $values);

                    if ($function === false) {
                        break;
                    }
                }

                $this->message()->clear();
                return true;
            }
        }

        if (!$match) {
            header('HTTP/1.0 404 Not Found');
            echo 'Page Not Found';
        }
    }
}

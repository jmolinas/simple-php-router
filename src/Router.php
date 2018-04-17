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
    }

    private function parameters($function, $arguments)
    {
        $values = [];
        $reflector = new \ReflectionFunction($function);
        $parameters = $reflector->getParameters();
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $isArgumentGiven = array_key_exists($name, $arguments);

            if (!$isArgumentGiven && !$param->isDefaultValueAvailable()) {
                throw new \Exception("Parameter {$name} is mandatory but was not provided");
            }

            $values[$param->getPosition()] = $isArgumentGiven ? $arguments[$name] : $param->getDefaultValue();
        }

        return $values;
    }

    private function setArguments($url)
    {
        preg_match_all('#:([\w]+)\+?#', $url, $match);
        $this->arguments = array_values($match[1]);
    }

    private function pattern($url)
    {
        $route = rtrim($url, '/') . '/';
        $this->setArguments($route);
        $route = preg_replace('#:([\w]+)\+?#', '([a-zA-Z0-9_-]+)', $route);
        $route = str_replace('/', '\/', $route);
        return '/^' . $route . '?$/';
    }

    public function route($url, $callback)
    {
        $pattern = $this->pattern($url);
        $this->routes[$pattern] = $callback;
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

                $values = $this->parameters($callback, $arguments);
                $function = call_user_func_array($callback, $values);

                if ($function === false) {
                    break;
                }
                return true;
            }
        }

        if (!$match) {
            header('HTTP/1.0 404 Not Found');
            echo 'Page Not Found';
        }
    }
}

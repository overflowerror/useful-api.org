<?php

require_once(ROOT . "/utils/arrays.php");

const GET = "GET";
const POST = "POST";
const PUT = "PUT";
const DELETE = "DELETE";
const HEAD = "HEAD";
const CONNECT = "CONNECT";
const OPTIONS = "OPTIONS";
const TRACE = "TRACE";
const PATCH = "PATCH";

class Router {
    private $routes = [];
    public $notFoundHandler;

    function __construct() {
        $this->notFoundHandler = function($context) {
            header("HTTP/", true, 404);
            require(ROOT . "/templates/404.php");
        };
    }

    private function findRoute(string $method, string $url) {
        if (!key_exists($method, $this->routes)) {
            return null;
        }

        $paths = $this->routes[$method];

        foreach ($paths as $path => $handler) {
            if (preg_match("/^" . str_replace("/", "\/", $path, ) . "$/", $url)) {
                return $handler;
            }
        }

        return null;
    }

    private function getPath($uri) {
        if (($i = strpos($uri, "?")) !== false) {
            return substr($uri, 0, $i);
        } else {
            return $uri;
        }
    }

    public function addRoute(string $method, string $path, $handler) {
        array_get_or_add($method, $this->routes, [])[$path] = $handler;
    }

    public function execute() {
        //var_dump($this->routes);

        $path = $this->getPath($_SERVER["REQUEST_URI"]);
        $route = $this->findRoute($_SERVER["REQUEST_METHOD"], $path);

        if (!$route) {
            $route = $this->notFoundHandler;
        }

        $context = [
          "REQUEST_PATH" => $path,
        ];

        $route($context);
    }
}

return new Router();
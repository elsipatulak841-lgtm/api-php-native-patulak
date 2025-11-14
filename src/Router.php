<?php

class Router
{
    private $routes = [];

    public function add($method, $path, $callback)
    {
        $this->routes[] = [
            "method" => $method,
            "path" => trim($path, "/"),
            "callback" => $callback
        ];
    }

    public function dispatch($method, $uri)
    {
        $uri = trim(parse_url($uri, PHP_URL_PATH), "/");

        foreach ($this->routes as $route) {

            $pattern = "@^" . preg_replace('/\{([\w]+)\}/', '([\w-]+)', $route['path']) . "$@";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                return call_user_func_array($route['callback'], $matches);
            }
        }

        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Route not found"]);
    }
}

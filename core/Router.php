<?php
class Router {
    private $routes = [];

    public function add($pattern, $controller, $method = 'GET') {
        $this->routes[] = [
            'pattern' => '#^' . $pattern . '$#',
            'controller' => $controller,
            'method' => strtoupper($method)
        ];
    }

    public function dispatch() {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // detect base path (example: /sistem_monitoring_udara/public)
    $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    $path = str_replace($basePath, '', $path);

    if ($path === '' || $path === '/') {
        $path = '/login'; // default page
    }

    $requestMethod = $_SERVER['REQUEST_METHOD'];

    foreach ($this->routes as $route) {
        if ($route['method'] === $requestMethod && preg_match($route['pattern'], $path, $matches)) {
            array_shift($matches);

            list($controllerName, $action) = explode('@', $route['controller']);

            require_once __DIR__ . "/../app/Controllers/$controllerName.php";
            $controller = new $controllerName();

            return call_user_func_array([$controller, $action], $matches);
        }
    }

    http_response_code(404);
    echo "Route not found";
}

}
?>
<?php

namespace App\Services;

class Router
{

    private static $_instance = null;
    private        $routes    = [];

    private function __construct() {}

    /**
     * @return Router
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * @param string         $name
     * @param string         $route
     * @param callable|array $action
     *
     * @return $this
     */
    public function get($name, $route, $action)
    {
        return $this->register($name, 'get', $route, $action);
    }

    /**
     * @param string         $name
     * @param string         $requestMethod
     * @param string         $route
     * @param callable|array $action
     *
     * @return $this
     */
    public function register($name, $requestMethod, $route, $action)
    {
        $requestMethod = mb_strtolower($requestMethod);

        if (isset($router_routes[$requestMethod][$name]) === true) {
            throw new DomainException('Route ' . $name . ' already exists.');
        }

        $this->routes[$requestMethod][$name] = [
            'link'   => '/' . trim($route, '/'),
            'action' => $action,
            'method' => $requestMethod,
        ];

        return $this;
    }

    /**
     * @param string         $name
     * @param string         $route
     * @param callable|array $action
     *
     * @return $this
     */
    public function post($name, $route, $action)
    {
        return $this->register($name, 'post', $route, $action);
    }

    /**
     * @return void
     */
    public function run()
    {
        $request_uri = $this->getRequestUri();

        $method = $this->getRequestMethod();

        $route_name = $this->findRequestUriInRoutes($request_uri, $this->routes, $method);

        // Проверяем наличие параметров в сессии
        if (isset($_SESSION['$_POST']) === true) {
            $_POST = array_merge($_POST, $_SESSION['$_POST']);
            unset($_SESSION['$_POST']);
        }

        $this->resolve($method, $route_name);
    }

    /**
     * Возвращает URI адрес запрашиваемой страницы без строки запроса
     *
     * @return string
     */
    protected function getRequestUri()
    {
        return (empty($_SERVER['QUERY_STRING']) === false)
            ? str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'])
            : rtrim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Возвращает метод запроса
     *
     * @return string GET, POST
     */
    protected function getRequestMethod()
    {
        $request_method = mb_strtolower($_SERVER['REQUEST_METHOD']);

        // разрешенные методы
        $allowed_methods = ['get', 'post'];

        // если неизвестный метод
        if (! in_array($request_method, $allowed_methods)) {
            return 'get';
        }

        // если пришел POST, проверяем наличие PUT, PATCH, DELETE
        if (mb_strtolower($request_method) === 'post') {
            $sub_method = isset($_POST['_method'])
                ? mb_strtolower($_POST['_method'])
                : false;
            if ($sub_method and in_array($sub_method, $allowed_methods)) {
                return $sub_method;
            }
        }

        return $request_method;
    }

    /**
     * Поиск маршрута по ссылке
     *
     * @param        $request_uri
     * @param        $routes
     * @param string $request_method
     *
     * @return string
     */
    protected function findRequestUriInRoutes($request_uri, $routes, $request_method = 'get')
    {
        $request_uri = rtrim($request_uri, '/');

        if (! isset($routes[$request_method])) {
            return 'home';
        }

        foreach ($routes[$request_method] as $name => $route) {
            if ($route['link'] == $request_uri and $route['method'] == $request_method) {
                return $name;
            }
        }

        return 'home';
    }

    /**
     * @param $method
     * @param $route_name
     *
     * @return void
     */
    private function resolve($method, $route_name)
    {
        // Проверка существования маршрута по имени
        if (! isset($this->routes[$method][$route_name])) {
            $this->error404();
        }

        // Данные маршрута
        $route = $this->routes[$method][$route_name];

        //
        $action = $route['action'];

        if ($action instanceof \Closure) {
            call_user_func($action);

            return;
        }

        $controller        = $action[0];
        $controller_method = $action[1];

        if (class_exists($controller)) {
            $controller_object = new $controller();
            if (method_exists($controller_object, $controller_method)) {
                $controller_object->$controller_method();

                return;
            }
        }
        $this->error404();
    }

    /**
     * Страница 404
     *
     * @param null $message
     */
    private function error404($message = null)
    {
        http_response_code(404);
        if ($message !== null and DEBUG) {
            dump($message);
        }
        die('404. Page not found.');
    }

    public function getByName($name)
    {
        if (isset($this->routes['get'][$name])) {
            return $this->routes['get'][$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    protected function __clone() {}
}

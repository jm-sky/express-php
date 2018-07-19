<?php
namespace Core;

require_once('./vendor/autoload.php');
require_once __DIR__ . '/request.php';
require_once __DIR__ . '/response.php';

class App {

    private static $instance;
    public $options = [];
    public $groups = [];
    public $middlewares = [];
    public $middlewares_queue = [];
    public $routes = [];
    public $request;
    public $response;
    public $controller;
    public $defaultController;

    /**
     * __construct
     *
     * @param mixed array
     * @return void
     */
    public function __construct(array $options = [])
    {
        \session_start();
        self::$instance =& $this;
        $this->logMsg('Creating App...');
        $this->options = $options;

        $this->options['views'] = $this->options['views'] ?? [];
        $this->options['views']['view_path'] = $this->options['views']['view_path'] ?? __DIR__ . '/../views/';
        $this->options['views']['layout'] = $this->options['views']['layout'] ?? 'layout.hbs';
    }

	/**
	 * Get the APP singleton
	 *
	 * @static
	 * @return	object
	 */
	public static function &get_instance()
	{
		return self::$instance;
	}

    /**
     * logMsg
     *
     * @param mixed $message
     * @return void
     */
    public function logMsg($message)
    {
        return;
        echo '<pre>', print_r($message, true), '</pre>', PHP_EOL;
    }

    /**
     * add
     * 
     */
    public function add(string $method, string $path, $controller)
    {
        if (is_callable($controller)) {
            $this->routes[$path][$method] = $controller;

        } else if (is_array($controller)) {
            foreach ($controller as $path => $ctrl) {
                $this->add($method, "/{$path}", $ctrl);
            }
        }
    }

    /**
     * any
     *
     * @param string $path
     * @param mixed $function
     * @return void
     */
    public function any(string $path, callable $function)
    {
        $this->add('any', $path, $function);
    }

    /**
     * post
     *
     * @param string $path
     * @param mixed $function
     * @return void
     */
    public function post(string $path, callable $function)
    {
        $this->add('post', $path, $function);
    }

    /**
     * get
     *
     * @param string $path
     * @param mixed $function
     * @return void
     */
    public function get(string $path, callable $function)
    {
        $this->add('get', $path, $function);
    }

    /**
     * use
     *
     * @param callable $function
     * @return void
     */
    public function use(callable $function)
    {
        $this->middlewares[] = $function;
    }

    /**
     * group
     * 
     * @return 
     */
    public function group(string $path, callable $function)
    {
        $this->groups[$path] = [
            $middlewares = [ $function ],
            'routes' => []
        ];

        return $this->groups[$path];
    }

    /**
     * listen
     *
     * @return void
     */
    public function listen()
    {
        // $this->logMsg('Routes:');
        // $this->logMsg($this->routes);
        // $this->logMsg('Middlewares:');
        // $this->logMsg($this->middlewares);

        $this->defaultController = $this->defaultController ?? $this->routes['/']['get'];
        $this->request = new Request();

        // $this->logMsg($this->routes);

        if (false == $route = $this->routes[$this->request->path]) {
            $this->logMsg("Missing controller for [{$this->request->method}] {$this->request->path}");
        }


        if ($route && false == $this->controller = $route[$this->request->method]) {
            $this->controller = $route['any'];
        }

        if (is_callable($this->controller) === false) {
            $this->logMsg("Missing controller for [{$this->request->method}] {$this->request->path}");
            $this->controller = $this->defaultController;
        }

        // $this->logMsg($route);

        $this->response = new Response();
        // $end = function() {};

        if (sizeof($this->middlewares)) {
            $this->middlewares_queue = array_values($this->middlewares);
            $middleware = $this->middlewares_queue[0];
            array_shift($this->middlewares_queue);

            // $next = $this->get_next($middlewares, $response, $controller);
            
            return $middleware($this->request, $this->response, $this->next());
        }

        return $this->controller($this->request, $this->response);

    }
    //------------------------------------------
    /**
     * next
     * 
     * @return callable
     */
    public function next()
    {
        // echo 'NEXT() Left: '.sizeof($this->middlewares_queue).'<br>';

        if (is_array($this->middlewares_queue) && sizeof($this->middlewares_queue)) {
            $middleware = $this->middlewares_queue[0];
            array_shift($this->middlewares_queue);
            $next =  function() use ($middleware) {
                return $middleware($this->request, $this->response, $this->next());
            };

        } else {
            $next = function() use ($middleware) {
                return ($this->controller)($this->request, $this->response);
            };
        }

        return $next;

        // print_r($next->go);
    }
    //------------------------------------------
    // private function get_next(&$middlewares, &$response, $controller)
    // {
    //     if (!is_array($middlewares) || sizeof($middlewares) === 0) {
    //         return false;
    //     }

    //     return function()
    //     {
    //         $next = $this->get_next($middlewares, $response, $controller);
    //         echo '...next() left: '.sizeof($middlewares).'<br>';
    //         $middleware = $middlewares[0];
    //         array_shift($middlewares);
    //         if ($middleware) {
    //             return $middleware($this->request, $response, $next);
    //         } else {
    //             return $controller($this->request, $response);
    //         }
    //     };
    // }
}
<?php
namespace Core;

require_once('./vendor/autoload.php');
require_once __DIR__ . '/request.php';
require_once __DIR__ . '/response.php';

class App {

    private static $instance;
    public $options = [];
    public $middlewares = [];
    public $middlewares_queue = [];
    public $routes = [];
    public $request;
    public $response;
    public $controller;

    /**
     * __construct
     *
     * @param mixed array
     * @return void
     */
    public function __construct(array $options = [])
    {
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
     * post
     *
     * @param string $path
     * @param callable $function
     * @return void
     */
    public function post(string $path, callable $function)
    {
        $this->routes[$path]['post'] = $function;
    }

    /**
     * get
     *
     * @param string $path
     * @param callable $function
     * @return void
     */
    public function get(string $path, callable $function)
    {
        $this->routes[$path]['get'] = $function;
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

        $this->request = new Request();

        // $this->logMsg($this->routes);

        if (false == $route = $this->routes[$this->request->path]) {
            $this->logMsg("Missing controller for [{$this->request->method}] {$this->request->path}");
        }


        if ($route && false == $this->controller = $route[$this->request->method]) {
            $this->logMsg("Missing controller for [{$this->request->method}] {$this->request->path}");
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
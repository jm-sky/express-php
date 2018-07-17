<?php
namespace Core;

class Request {

    protected $app;

    public $method;
    public $path;

    public function __construct()
    {
        $this->app =& App::get_instance();

        $this->path = strtolower($_SERVER['REQUEST_URI']);
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);

        // TODO
        if (stripos($this->path, $this->app->options['base_dir']) === 0) {
            $this->path = substr($this->path, strlen($this->app->options['base_dir']));
            $this->path = $this->path == '' ? '/' : $this->path;
        }

        // echo '<pre>', print_r($this->app, true), '</pre>';
        // echo '<pre>', print_r($this, true), '</pre>';
        // echo 'base_dir: ', $this->app->options['base_dir'], '<BR>';
        // echo 'PATH: ', $this->path, '<BR>';
    }

}

class App {

    private static $instance;
    public $options = [];
    public $middlewares = [];
    public $routes = [];

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
    private function logMsg($message)
    {
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

        $request = new Request();


        if (false == $route = $this->routes[$request->path]) {
            $this->logMsg("Missing controller for [{$request->method}] {$request->path}");
        }

        if ($route && false == $controller = $route[$request->method]) {
            $this->logMsg("Missing controller for [{$request->method}] {$request->path}");
        }

        $this->logMsg($route);
    }
    //------------------------------------------
}
<?php
namespace Core;

class Request {

protected $app;

public $uri;
public $method;
public $path;

public function __construct()
{
    $this->app =& App::get_instance();

    $this->uri = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);

    $this->path = strtolower($this->uri['path']);
    $this->method = strtolower($_SERVER['REQUEST_METHOD']);

    // // TODO
    // if (stripos($this->path, $this->app->options['base_dir']) === 0) {
    //     $this->path = substr($this->path, strlen($this->app->options['base_dir']));
    //     $this->path = $this->path == '' ? '/' : $this->path;
    // }

    // CodeIgniter URI
    if (isset($_SERVER['SCRIPT_NAME'][0]))
    {
        if (strpos($this->path, $_SERVER['SCRIPT_NAME']) === 0)
        {
            $this->path = (string) substr($this->path, strlen($_SERVER['SCRIPT_NAME']));
        }
        elseif (strpos($this->path, dirname($_SERVER['SCRIPT_NAME'])) === 0)
        {
            $this->path = (string) substr($this->path, strlen(dirname($_SERVER['SCRIPT_NAME'])));
        }
    }
    // $this->path = '/'
    $this->app->logMsg($_SERVER['SCRIPT_NAME']);
    // echo '<pre>', print_r($this->app, true), '</pre>';
    // echo '<pre>', print_r($this, true), '</pre>';
    // echo 'base_dir: ', $this->app->options['base_dir'], '<BR>';
    // echo 'PATH: ', $this->path, '<BR>';
}

}
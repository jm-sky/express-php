<?php
namespace Core;

require_once('./vendor/autoload.php');
use LightnCandy\LightnCandy;

class Response {

    public $use_cache = false;
    protected $app;
    private static $instance;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        self::$instance =& $this;
        $this->app =& App::get_instance();
    }

    /**
     * get_instance
     */
	public static function &get_instance()
	{
		return self::$instance;
    }
    
    /**
     * json
     *
     * @param mixed $data
     * @return void
     */
    public function json($data = null)
    {
        echo json_encode($data);
    }

    /**
     * render
     *
     * @param mixed $data
     * @return void
     */
    public function render(string $template, array $data = [], $return = false, $no_layout = false)
    {
        if ($template == '' || $template == false) {
            return;
        }

        $path = $this->app->options['views']['view_path'] . $template;
        $md5 = @md5_file($path) ?? '__not_found';
        $compiled_path = __DIR__ . '/../temp/views/' . $md5 . '.php';

        if (false === is_file($compiled_path) || false === $this->use_cache) {
            $template = file_get_contents($path);
            $phpStr = LightnCandy::compile($template, [
                'flags' => LightnCandy::FLAG_HANDLEBARS,
                'helpers' => [
                    'render' => function($template, $data) {
                        return $this->render($template, $data, true, true);
                    }
                ]
            ]);
            file_put_contents($compiled_path, '<?php ' . $phpStr . '?>');
        }
    
        $data = array_merge(get_object_vars($this), $data);
        $renderer = include($compiled_path);

        if (is_callable($renderer) == false) {
            echo "ERROR: {$template} is not callable!<br>";
            unlink($compiled_path);
            var_dump($renderer);
            return;
        }

        $html = $renderer($data);
        $layout = $this->app->options['views']['layout'];
        
        if ($layout && $layout != $template && $no_layout === false) {
            $data = array_merge($data, ['yield' => $html]);
            $html = $this->render($layout, $data, true, true);
        }

        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

}
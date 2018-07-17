# express-php
Express-like PHP framework

-  Routes for get and post requests.
-  Middlewares.
-  Views with Handlebars engine.



```php
<?php
require_once('./vendor/autoload.php');

require_once __DIR__ . '/core/app.php';
require_once __DIR__ . '/core/utils.php';

$app = new Core\App([
    'base_dir' => '/express-php/'
]);

$routes = _import('./routes');
$middlewares = _import('./middlewares');

$app->use($middlewares['log']);
$app->use($middlewares['auth']);

$app->get('/', $routes['index']);
$app->get('/home', $routes['home']);
$app->post('/login', $routes['login']);

$app->listen();
```

TODO: 
-  Some database connections.
-  Some authorization.
-  Nested module-like mechanism for middlewares.

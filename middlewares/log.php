<?php
$export = function($req, $resp, $next) {

    $app = Core\App::get_instance();

    $resp->title = 'Strona '.$req->path;
    $req->time = date('Y-m-d H:i:s');

    $resp->adresy = array_keys($app->routes);

    ($next)();
};

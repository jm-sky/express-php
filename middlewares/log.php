<?php
$export = function($req, $resp, $next) {

    $resp->title = 'Strona '.$req->path;
    
    $req->time = date('Y-m-d H:i:s');

    ($next)();
};

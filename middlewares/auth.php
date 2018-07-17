<?php
$export = function($req, $resp, $next) {

    echo 'I am auth middleware!<br>', PHP_EOL;

    $next();

};

<?php
$export = function($req, $resp, $next) {

    $req->user = (object) [
        'login' => 'janek'
    ];

    ($next)();
};

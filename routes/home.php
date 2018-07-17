<?php
$export = function($req, $resp, $next = null) {

    $resp->render('home.hbs', [
        'message' => 'Hello from home!',
        'current_user' => $req->user->login
    ]);

};

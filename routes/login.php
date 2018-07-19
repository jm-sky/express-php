<?php
$export = function($req, $resp, $next = null) {

    $resp->render('login.hbs', [
        'message' => 'Hello from index!',
        'current_user' => $req->user->login
    ]);

};

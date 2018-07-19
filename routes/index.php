<?php
$export = function($req, $resp, $next = null) {

    $resp->render('index.hbs', [
        'message' => $_GET['message'] ?? 'Hello from index!',
        'current_user' => $req->user->login
    ]);

};

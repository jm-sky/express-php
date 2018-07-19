<?php
$export = function($req, $resp, $next = null) {

    $resp->render('profile.hbs', [
        'message' => 'Hello profile!',
        'current_user' => $req->user->login
    ]);

};

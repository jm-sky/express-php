<?php
$export = function($req, $resp, $next = null) {

    $resp->render('not_found.hbs', [
        'title' => 'Error!',
        'message' => $_GET['message'] ?? 'You can not view this page!'
    ]);

};

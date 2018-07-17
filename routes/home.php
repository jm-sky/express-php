<?php
$export = function($req, $resp, $next) {

    // $resp->render('index.php', [

    // ]);

    $resp->json(['message' => 'Hello from home!']);

};

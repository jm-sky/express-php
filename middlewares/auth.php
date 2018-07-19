<?php
$export = function($req, $resp, $next) {

    $app = Core\App::get_instance();

    if ($_POST['login'] && $_POST['password']) {
        $_SESSION['user'] = (object) [
            'login' => $_POST['login'],
            'login_date' => date('Y-m-d H:i:s')
        ];
        header('Location: '.$app->options['base_dir'].'?message=Logged+in');
    }

    if ($req->path === '/logout') {
        $_SESSION['user'] = null;
        header('Location: '.$app->options['base_dir'].'?message=Logged+out');
    }

    $req->user = $_SESSION['user'];
    $resp->user = $req->user;
    
    $allowed_paths = ['/login', '/not_found', '/'];

    if ($req->user == null && in_array($req->path, $allowed_paths) === false) {
        header('Location: '.$app->options['base_dir'].'not_found?message=You+have+to+login');
    }

    ($next)();
};

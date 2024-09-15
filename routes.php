<?php
use app\controllers\indexController;

$router = new helpers\Router();
$router->get('/', [indexController::class, 'index']);
$router->get('/index.php', [indexController::class, 'index']);
$router->post('/upload/file', [indexController::class, 'upload']);

echo $router->resolve($_SERVER['REQUEST_URI'], strtolower($_SERVER['REQUEST_METHOD']));
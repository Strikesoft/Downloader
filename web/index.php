<?php
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Downloader\Router;

$app = new Silex\Application();
$router = new Router($app);
$request = Request::createFromGlobals();
$router->load();
$app->run($request);

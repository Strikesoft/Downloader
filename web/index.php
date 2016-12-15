<?php
require_once __DIR__.'/../vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Downloader\Router;

$app = new Silex\Application();
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$router = new Router($app);
$request = Request::createFromGlobals();
$router->load();
$app->run($request);

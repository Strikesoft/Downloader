<?php
require_once __DIR__.'/../vendor/autoload.php';
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Downloader\Router;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$router = new Router($app);
$request = Request::createFromGlobals();
$router->load();
$app->run($request);

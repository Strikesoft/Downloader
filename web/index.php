<?php
require_once __DIR__.'/../vendor/autoload.php';
use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Downloader\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

// load config
$config = Yaml::parse(file_get_contents(__DIR__ . '/../config/param.yml'));
foreach ($config as $key => $value) {
  $app[$key] = $value;
}

$router = new Router($app);
$request = Request::createFromGlobals();
$router->load();
$app->run($request);

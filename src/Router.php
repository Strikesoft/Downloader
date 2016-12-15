<?php
namespace Downloader;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manage Downloader routes
 * @author: Johann-S
 */
class Router
{
  private $app;
  private $tabGetRoute;

  public function __construct(Application $app) {
    $this->app = $app;
    $this->tabGetRoute = array(
        '/' => 'indexController:indexAction'
    );
  }

  public function load() {
    $this->registerController();
    foreach ($this->tabGetRoute as $path => $controller) {
      $this->app->get($path, $controller)->method('GET');
    }

    $app->error(function (\Exception $e, $code) {
      return new Response();
    });
  }

  private function registerController() {
    $tabController = array(
        'indexController' => new Controller\IndexController($app)
    );

    foreach ($tabController as $key => $value) {
        $this->app[$key] = $this->app->share(function () use ($value) {
            return $value;
        });
    }
  }
}

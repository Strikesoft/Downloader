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

    $this->app->error(function (\Exception $e, $code) {
      return $this->app['twig']->render('base/error.twig', array(
        'code'      => $code,
        'exceptMsg' => $e->getMessage()
      ));
    });
  }

  private function registerController() {
    $tabController = array(
        'indexController' => new Controller\IndexController($this->app)
    );

    foreach ($tabController as $key => $value) {
        $this->app[$key] = $value;
    }
  }
}

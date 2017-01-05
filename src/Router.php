<?php
namespace Downloader;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Response;

/**
 * Manage Downloader routes
 * @author: Johann-S
 */
class Router
{
    private $app;
    private $tabGetRoute;
    private $tabPostRoute;

    public function __construct(SilexApplication $app) {
        $this->app = $app;
        $this->tabGetRoute = array(
            '/' => 'indexController:indexAction'
        );
        $this->tabPostRoute = array(
            '/' => 'indexController:indexPostAction'
        );
    }

    public function load() {
        $this->registerController();
        foreach ($this->tabGetRoute as $path => $controller) {
            $this->app->get($path, $controller)->method('GET');
        }
        foreach ($this->tabPostRoute as $path => $controller) {
            $this->app->get($path, $controller)->method('POST');
        }

        $this->app->error(function (\Exception $e, $code) {
            return $this->app['twig']->render('base/error.twig', array(
                'debug'     => $this->app['debug'],
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

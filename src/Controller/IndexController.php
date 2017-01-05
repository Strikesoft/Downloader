<?php
namespace Downloader\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Downloader\BaseController;
use Downloader\Util\Downloader;

/**
 * Index controller
 * @author: Johann-S
 */
class IndexController extends BaseController
{
    public function __construct(Application $_app) {
        parent::__construct($_app);
    }

    public function indexAction() {
        return $this->render('index/index.twig');
    }

    public function indexPostAction(Request $request) {
        $downloader = new Downloader($this->app['downloadfolder'], $this->app['downloadurl']);
        return $this->app->json($downloader->download($request));
    }
}

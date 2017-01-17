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
    /**
     * @var Downloader $downloader
     */
    private $downloader;
    
    public function __construct(Application $_app) {
        parent::__construct($_app);
        $this->downloader = new Downloader($this->app['downloadfolder'], $this->app['downloadurl']);
    }

    public function indexAction() {
        return $this->render('index/index.twig', array(
            'downloadInformation' => $this->downloader->getDownloadInformation()
        ));
    }

    public function indexPostAction(Request $request) {
        return $this->app->json($this->downloader->download($request));
    }
}

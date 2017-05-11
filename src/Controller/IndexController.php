<?php
namespace Downloader\Controller;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Downloader\BaseController;
use Downloader\Util\Downloader;
use Downloader\Application;

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

    public function __construct(SilexApplication $_app) {
        parent::__construct($_app);
        $this->downloader = new Downloader($this->app['downloadfolder'], $this->app['downloadurl']);
    }

    public function indexAction() {
        $isSecure = Application::isSecure();
        if ($isSecure) {
            $this->app['session']->set('downloaderUser', array(
                'allowDownload' => false
            ));
        }
        return $this->render('index/index.twig', array(
            'downloadInformation' => $this->downloader->getDownloadInformation(),
            'debug' => $this->app['debug'],
            'isSecure' => $isSecure
        ));
    }

    public function indexPostAction(Request $request) {
        if (Application::isSecure()) {
            $downloaderUser = $this->app['session']->get('downloaderUser');

            // User not allowed to download
            if (!$downloaderUser['allowDownload']) {
                return $this->app->json(array('error' => 'Not authorized !'));
            }
        }
        return $this->app->json($this->downloader->download($request));
    }
}

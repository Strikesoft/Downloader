<?php
namespace Downloader\Util;

use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * A class to download everything
 * @author: Johann-S
 */
class Downloader {
    private $pathDownload;
    private $downloadUrl;
    private $allowedFiles = array('zip', 'exe', 'rar', 'msi');
    private $allowedImages = array('png', 'jpg', 'jpeg', 'gif', 'bmp');

    public function __construct($_pathDownload, $_downloadUrl) {
        $this->pathDownload = $_pathDownload;
        $this->downloadUrl = $_downloadUrl;
    }

    public function download(Request $request) {
        $url = $request->request->get('url', null);
        $checkFile = $this->checkFile($url);
        if ($checkFile['error']) {
            return array('error' => $checkFile['message']);
        }
        $filename = $this->getFileName($url);
        $pathFile = $this->pathDownload . $filename;
        $handle = fopen($pathFile, 'w');
        $client = new Client();
        $client->request('GET', $url, ['sink' => $handle]);

        // Security about fake images
        if ($checkFile['isImage']) {
            list($width, $height) = getimagesize($pathFile);
            if ($width === 0 && $height === 0) {
                unlink($pathFile);
                return array('error' => 'Not a valid image');
            }
        }
        return array(
            'downloadLink' => $this->downloadUrl . $filename,
            'filename'     => $filename
        );
    }

    private function checkFile($url) {
        $error = false;
        $message = null;
        if ($url === null) {
          $error = true;
          $message = 'Please provide a valid url';
        }
        if (!$this->url_exist($url)) {
          $error = true;
          $message = 'this url doesn\'t exist';
        }
        $ext = strtolower(substr(strrchr($url, '.'), 1));
        if (!in_array($ext, $this->allowedFiles) && !in_array($ext, $this->allowedImages)) {
          $error = true;
          $message = 'file not allowed';
        }
        $isImage = in_array($ext, $this->allowedImages);
        return array(
          'error' => $error,
          'isImage' => $isImage,
          'message' => $message
        );
    }

    private function url_exist($url) {
        try {
          $client = new Client();
          $client->request('HEAD', $url);
          return true;
        }
        catch (ClientException $e) {
          return false;
        }
    }

    private function getFileName($url) {
        return @end(explode('/', $url));
    }
}

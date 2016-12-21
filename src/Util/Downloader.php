<?php
namespace Downloader\Util;

use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\;

/**
 * A class to download everything
 * @author: Johann-S
 */
class Downloader {
  private $pathDownload;
  private $downloadUrl;
  private $allowedFiles = array('zip', 'exe', 'rar');
  private $allowedImages = array('png', 'jpg', 'jpeg', 'gif', 'bmp');
  private $guzzle;

  public function __construct($_pathDownload, $_downloadUrl) {
    $this->pathDownload = $_pathDownload;
    $this->downloadUrl = $_downloadUrl;
    $this->guzzle = new Client();
  }

  public function download(Request $request) {
    $url = $request->request->get('url', null);
    $checkFile = $this->checkFile($url);
    if ($checkFile['error']) {
      return array('error' => $checkFile['message']);
    }
    return array('downloadLink' => '');
  }

  private function checkFile($url) {
    $error = false;
    $message = null;
    $isImage = false;
    if ($url === null) {
      $error = true;
      $message = 'Please provide a valid url';
    }
    if (!$this->url_exist($url)) {
      $error = true;
      $message = 'this url doesn\'t exist';
    }
    $ext = strtolower(substr(strrchr($url, '.'), 1));
    if (!in_array($this->allowedFiles, $ext) && !in_array($this->allowedImages, $ext)) {
      $error = true;
      $message = 'file not allowed';
    }
    $isImage = in_array($this->allowedImages, $ext);
    return array(
      'error' => $error,
      'isImage' => $isImage,
      'message' => $message
    );
  }

  private function url_exist($url) {
    try {
      $this->guzzle->request('HEAD', $url);
      return true;
    }
    catch (ClientException $e) {
      return false;
    }
  }
}

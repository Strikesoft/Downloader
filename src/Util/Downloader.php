<?php
namespace Downloader\Util;

use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Downloader\Application;

/**
 * A class to download everything
 * @author: Johann-S
 */
class Downloader {
    private $pathDownload;
    private $downloadUrl;
    private $allowedFiles;
    private $allowedImages;

    public function __construct($_pathDownload, $_downloadUrl) {
        $this->pathDownload = $_pathDownload;
        $this->downloadUrl = $_downloadUrl;

        // Get parameters from param.yml
        $tmpAllowedFiles = Application::getParam(array('allowedExtensions', 'file'));
        $tmpAllowedImages = Application::getParam(array('allowedExtensions', 'image'));
        $this->allowedFiles = $tmpAllowedFiles !== null ? $tmpAllowedFiles : array();
        $this->allowedImages = $tmpAllowedImages !== null ? $tmpAllowedImages : array();
    }

    public function getDownloadInformation() {
        $bytes = $this->getMemoryLimit();
        // Convert bytes to a human value
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        }
        elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        }
        else {
            $bytes = '0 bytes';
        }
        return array(
            'fileSizeLimit' => $bytes,
            'allowedExt' => implode(', ', array_merge($this->allowedFiles, $this->allowedImages))
        );
    }

    public function download(Request $request) {
        $url = $request->request->get('url', null);
        if (strpos($url, 'https://mega.nz/') === 0) {
            return $this->megaDownload($url);
        }
        $checkFile = $this->checkFile($url);
        if ($checkFile['error']) {
            return array('error' => $checkFile['message']);
        }
        Application::log('info', 'Download : ' . $url);
        $filename = $this->getFileName($url);
        $pathFile = $this->pathDownload . $filename;
        $handle = fopen($pathFile, 'w');
        $client = new Client();
        $client->request('GET', $url, array(
            'sink' => $handle,
            'verify' => false // TODO : allow to pass ssl certificate
        ));

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

    private function megaDownload($url) {
        $mega = new Mega();
        $fileInfo = $mega->public_file_info_from_link($url);
        $filename = null;
        if (isset($fileInfo['at']['n']) && !empty($fileInfo['at']['n'])) {
            $filename = $fileInfo['at']['n'];
        }
        else {
            return array(
                'error' => true,
                'message' => 'No file name'
            );
        }
        if (isset($fileInfo['s']) && !empty($fileInfo['s'])) {
            $fileSize = intval($fileInfo['s']);
            Application::log('info', 'Mega Download size : ' . $fileSize);
            if ($fileSize > $this->getMemoryLimit()) {
                return array(
                    'error' => true,
                    'message' => 'The requested file is too heavy'
                );
            }
        }
        $ext = strtolower(substr(strrchr($filename, '.'), 1));
        if (!in_array($ext, $this->allowedFiles) && !in_array($ext, $this->allowedImages)) {
            return array(
                'error' => true,
                'message' => 'file not allowed'
            );
        }

        $fileInfo = array_merge($fileInfo, Mega::parse_link($url));
        $pathFile = $this->pathDownload . $filename;
        Application::log('info', 'Download Mega link : ' . $url);
        $fp = fopen($pathFile, 'wb');
        $mega->public_file_download($fileInfo['ph'], $fileInfo['key'], $fp);
        fclose($fp);

        // Security about fake images
        if (in_array($ext, $this->allowedImages)) {
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

        // File size
        $client = new Client();
        $response = $client->head($url, array(
            'verify' => false // TODO : allow to pass ssl certificate
        ));
        $fileSize = $response->getHeader('Content-Length');
        $fileSize = intval($fileSize[0]);
        Application::log('info', 'Download size : ' . $fileSize);
        if ($fileSize > $this->getMemoryLimit()) {
            $error = true;
            $message = 'The requested file is too heavy';
        }

        return array(
            'error' => $error,
            'isImage' => $isImage,
            'message' => $message
        );
    }

    private function url_exist($url) {
        try {
            $client = new Client();
            $client->request('HEAD', $url, array(
                'verify' => false // TODO : allow to pass ssl certificate
            ));
            return true;
        }
        catch (ClientException $e) {
            Application::log('warning', 'This url do not exist : ' . $url);
            return false;
        }
    }

    private function getFileName($url) {
        return @end(explode('/', $url));
    }

    private function getMemoryLimit() {
        $bytes = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $bytes, $matches)) {
            if ($matches[2] == 'M') {
                $bytes = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $bytes = $matches[1] * 1024; // nnnK -> nnn KB
            }
            return $bytes;
        }
        return 0;
    }
}

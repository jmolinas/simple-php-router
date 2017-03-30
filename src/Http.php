<?php
namespace JMolinas\SimpleRouter;

use JMolinas\SimpleRouter\HttpInterface;

class Http implements HttpInterface
{

    protected $url;

    public function __construct()
    {
        $this->request = $_SERVER;
        if (!empty($_SERVER['REQUEST_URL'])) {
            $url = $_SERVER['REQUEST_URL'];
        } else {
            $url = $_SERVER['REQUEST_URI'];
        }
        // Store the dirty version of the URL
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->getCleanUrl($this->url);
    }

    protected function getCleanUrl($url)
    {
        // The request url might be /project/index.php, this will remove the /project part
        $url = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $url);
        // Remove the query string if there is one
        $queryString = strpos($url, '?');
        if ($query_string !== false) {
            $url = substr($url, 0, $queryString);
        }
        // If the URL looks like http://localhost/index.php/path/to/folder remove /index.php
        if (substr($url, 1, strlen(basename($_SERVER['SCRIPT_NAME']))) == basename($_SERVER['SCRIPT_NAME'])) {
            $url = substr($url, strlen(basename($_SERVER['SCRIPT_NAME'])) + 1);
        }
        // Make sure the URI ends in a /
        $url = rtrim($url, '/') . '/';
        // Replace multiple slashes in a url, such as /my//dir/url
        $url = preg_replace('/\/+/', '/', $url);
        return $url;
    }

    public function redirect($uri)
    {
        header("Location: {$uri}"); /* Redirect browser */
        exit();
    }
}

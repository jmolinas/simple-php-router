<?php
namespace JMolinas\SimpleRouter;

use JMolinas\SimpleRouter\HttpInterface;

class Http implements HttpInterface
{

    protected $url;

    public function __construct(array $server)
    {
        $this->request = $server;
        $this->url = !empty($server['REQUEST_URL']) ? $server['REQUEST_URL'] : $server['REQUEST_URI'];
    }

    public function getUrl()
    {
        // The request url might be /project/index.php, this will remove the /project part
        $url = str_replace(dirname($this->request['SCRIPT_NAME']), '', $this->url);
        // Remove the query string if there is one
        $queryString = strpos($url, '?');
        if ($queryString !== false) {
            $url = substr($url, 0, $queryString);
        }
        // If the URL looks like http://localhost/index.php/path/to/folder remove /index.php
        if (substr($url, 1, strlen(basename($this->request['SCRIPT_NAME']))) == basename($this->request['SCRIPT_NAME'])) {
            $url = substr($url, strlen(basename($this->request['SCRIPT_NAME'])) + 1);
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
    }
}

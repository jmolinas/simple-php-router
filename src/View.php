<?php
namespace JMolinas\SimpleRouter;

class View
{
    protected $path;

    public function __construct($templatePath)
    {
        $this->path = $templatePath;
    }

    protected function getTemplatePathname($file)
    {
        $vendorDir = dirname(dirname(__FILE__));
        $baseDir = dirname($vendorDir);
        return $baseDir . DIRECTORY_SEPARATOR . $this->path . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
    }

    private function display($template, $data = null)
    {
        $templatePathname = $this->getTemplatePathname($template);
        if (!is_file($templatePathname)) {
            throw new \RuntimeException("View cannot render `$template` because the template does not exist");
        }

        if (isset($_SESSION['msg'])) {
            $data = array_merge($data, $_SESSION['msg']);
        }

        extract($data);
        ob_start();
        require $templatePathname;
        return ob_get_clean();
    }

    public function render($template, $data = array())
    {
        echo $this->display($template, $data);
    }
}

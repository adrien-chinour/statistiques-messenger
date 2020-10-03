<?php


namespace App\Service;


use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class Renderer
{

    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);
    }

    /**
     * @param string $template name of template to use
     * @param string $output name of output file path from root application folder
     * @param array $options options for template rendering
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function output(string $template, string $output, array $options = [])
    {
        if (!is_dir(dirname($output))) {
            mkdir(dirname($output), 0777, true);
        }

        file_put_contents($output, $this->twig->render($template, $options));
    }


}

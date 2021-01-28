<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);
    }

    public function output(string $template, string $output, array $options = [])
    {
        if (!is_dir(dirname($output))) {
            mkdir(dirname($output), 0777, true);
        }

        file_put_contents($output, $this->write($template, $options));
    }

    public function write(string $template, array $options = []): string
    {
        return $this->twig->render($template, $options);
    }
}

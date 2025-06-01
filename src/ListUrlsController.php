<?php

namespace App;

use Slim\Views\PhpRenderer;

class ListUrlsController
{
    public function __construct(private PhpRenderer $renderer)
    {
    }

    public function __invoke($request, $response)
    {
        $data = [
            'urls' => $this->fetcher->getAllDetail(),
        ];

        return $this->twig->render($response, 'app/urls/list.html.twig', $data);
    }
}
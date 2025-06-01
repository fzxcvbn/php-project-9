<?php

namespace App;

use App\UrlCheckRepository;
use App\UrlRepository;
use Exception;
use Slim\Flash\Messages;
use Slim\Views\PhpRenderer;
use App\UrlNotFoundException;

class ShowUrlsController
{
    public function __construct(
        private Messages $flash,
        private UrlRepository $urlRepository,
        /*private UrlCheckRepository $urlCheckRepository,*/
        private PhpRenderer $renderer
    ) {
    }

    public function __invoke($request, $response, array $args)
    {
        try {
            $url = $this->urlRepository->getOne($args['id']);
            /*$checks = $this->urlCheckRepository->get((string) $url['id']);*/

            $data = [
                'url'     => $url,
                /*'checks'  => $checks,*/
                'flashes' => $this->flash->getMessages(),
            ];
            $templatePath = __DIR__ . '/../templates/show.phtml';
            return $this->renderer->render($response, 'show.phtml', $data);
        } catch (UrlNotFoundException) {
            return $this->renderer->render($response->withStatus(404), '404.phtml');
        } /*catch (Exception) {
            return $this->renderer->render($response->withStatus(500), '500.phtml');
        }*/
    }
}
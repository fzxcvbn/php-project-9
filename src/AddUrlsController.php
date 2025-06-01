<?php

namespace App;

use App\UrlNormal;
use App\UrlValidator;
use App\UrlRepository;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Views\PhpRenderer;

class AddUrlsController
{
    public function __construct(
        private Messages $flash,
        private UrlNormal $urlNormal,
        private UrlRepository $urlRepository,
        private UrlValidator $urlValidator,
        private PhpRenderer $renderer,
        private RouteCollectorInterface $routeCollector
    ) {
    }

    public function __invoke($request, $response)
    {
        $url = $request->getParsedBodyParam('url');

        if ($this->urlValidator->validate($url)) {
            $url['name'] = $this->urlNormal->normal($url['name']);

            if (!$existingUrl = $this->urlRepository->findOneByName($url['name'])) {
                $url['created_at'] = date('Y-m-d H:i:s');

                $id = $this->urlRepository->add($url);

                $this->flash->addMessage('success', 'Страница успешно добавлена');
            } else {
                $id = $existingUrl['id'];

                $this->flash->addMessage('success', 'Страница уже существует');
            }

            return $response->withRedirect(
                $this->routeCollector->getRouteParser()->urlFor('url', ['id' => $id])
            );
        }

        $params = [
            'url'     => $url,
            'errors'  => $this->urlValidator->getErrors(),
            'flashes' => $this->flash->getMessages(),
        ];

        return $this->renderer->render($response->withStatus(422), 'home.phtml', $params);
    }
}
<?php

namespace App;

use App\UrlNormal;
use App\UrlValidator;
use App\UrlRepository;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteCollectorInterface;

readonly class AddUrlsController
{
    public function __construct(
        private Messages $flash,
        private UrlNormal $urlNormal,
        private UrlRepository $urlRepository,
        private RouteCollectorInterface $routeCollector,
        private UrlValidator $urlValidator
    ) {
    }

    public function __invoke($request, $response)
    {
        $url = $request->getParsedBodyParam('url');

        if ($this->urlValidator->validate($url)) {
            $url['name'] = $this->urlNormal->normal($url['name']);

            if (!$existingUrl = $this->urlRepository->findOneByName($url['name'])) {
                $url['created_at'] =  $url['created_at'] = date('d.m.Y');

                $id = $this->urlRepository->create($url);

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

        return $this->get('renderer')->render($response->withStatus(422), 'home.phtml', $params);
    }
}
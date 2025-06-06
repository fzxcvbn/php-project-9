<?php

namespace App;

use App\UrlNotFoundException;
use App\UrlCheckRepository;
use App\UrlRepository;
use App\UrlChecker;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Slim\Flash\Messages;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Views\PhpRenderer;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

class CheckUrlsHandler
{
    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private PhpRenderer $renderer,
        private Messages $flash,
        private UrlRepository $urlRepository,
        private UrlCheckRepository $urlCheckRepository,
        private UrlChecker $urlChecker
    ) {
    }

    public function __invoke(ServerRequest $request, Response $response, array $args)
    {
        try {
            $urlId = $args['id'];
            $url = $this->urlRepository->getOne($urlId);
            try {
                $checkResult = $this->urlChecker->check($url['name']);
                $check = $this->buildNewCheck($urlId, $checkResult);

                $this->urlCheckRepository->add($check);

                $this->flash->addMessage('success', 'Страница успешно проверена');
            } catch (ConnectException) {
                $this->flash->addMessage('error', 'Произошла ошибка при проверке, не удалось подключиться');
            } catch (RequestException) {
                $this->flash->addMessage('error', 'Произошла ошибка при проверке. Ошибка при выполнении запроса');
            }

            return $response->withRedirect($this->routeCollector->getRouteParser()->urlFor('url', ['id' => $urlId]));
        } catch (UrlNotFoundException) {
            return $this->renderer->render($response->withStatus(404), '404.phtml');
        } catch (Exception $e) {
            return $this->renderer->render($response->withStatus(500), '500.phtml');
        }
    }

    private function buildNewCheck(string $urlId, array $checkResult): array
    {
        return [
            'url_id'      => $urlId,
            'created_at'  => date('Y-m-d H:i:s'),
            'status_code' => $checkResult['status_code'],
            'h1'          => mb_substr($checkResult['h1'] ?? '', 0, 255),
            'title'       => mb_substr($checkResult['title'] ?? '', 0, 255),
            'description' => mb_substr($checkResult['description'] ?? '', 0, 255),
        ];
    }
}

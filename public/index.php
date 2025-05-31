<?php

require __DIR__ . '/../vendor/autoload.php';

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Middleware\MethodOverrideMiddleware;
use App\UrlRepository;
use App\UrlValidator;
use App\UrlNormal;
use App\AddUrlsController;
use App\DbConnect;

session_start();

$container = new Container();

$container->set('renderer', function () {
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$container->set('flash', function () {
    return new Messages();
});

$container->set(\PDO::class, function () {
    return DbConnect::get()->connect();
});

try {
    DbConnect::get()->connect();
    echo 'A connection to the PostgreSQL database sever has been established successfully.';
} catch (\PDOException $e) {
    echo $e->getMessage();
}

$app = AppFactory::createFromContainer($container);

$router = $app->getRouteCollector()->getRouteParser();

$app->addErrorMiddleware(true, true, true);
$app->add(MethodOverrideMiddleware::class);


$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'home.phtml');
})->setName('home');


$app->post('/urls', AddUrlsController::class)->setName('addUrl');

$app->get('/urls', function ($request, $response) {
    $urlRepository = $this->get(UrlRepository::class);
    $urls = $urlRepository->getEntities();

    $params = [
        'urls' => $urls,
    ];

    return $this->get('renderer')->render($response, 'list.phtml', $params);
})->setName('urls');


$app->get('/urls/{id}', function ($request, $response, $args) {
    $urlRepository = $this->get(UrlRepository::class);
    $id = $args['id'];
    $url = $urlRepository->find($id);

    if (is_null($url)) {
        return $this->get('renderer')->render($response->withStatus(404), '404.phtml');
    }

    $messages = $this->get('flash')->getMessages();

    $params = [
        'flash' => $messages
    ];
    return $this->get('renderer')->render($response, 'show.phtml', $params);
})->setName('url');

$app->run(); 
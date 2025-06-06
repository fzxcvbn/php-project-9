<?php

require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Slim\Flash\Messages;
use Slim\Middleware\MethodOverrideMiddleware;
use App\UrlRepository;
use App\UrlValidator;
use App\UrlNormal;
use App\AddUrlsHandler;
use DI\ContainerBuilder;
use Slim\Interfaces\RouteCollectorInterface;
use App\ShowUrlsHandler;
use App\ListUrlsHandler;
use App\CheckUrlsHandler;
use App\UrlCheckRepository;
use App\UrlChecker;

session_start();

$container = (new ContainerBuilder())
    ->useAutowiring(true)
    ->addDefinitions(__DIR__ . '/../config/container.php')
    ->build();

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$container->set(RouteCollectorInterface::class, fn() => $app->getRouteCollector());

$container->set(AddUrlsHandler::class, function ($container) {
    return new AddUrlsHandler(
        $container->get('flash'),
        $container->get(UrlNormal::class),
        $container->get(UrlRepository::class),
        $container->get(UrlValidator::class),
        $container->get('renderer'),
        $container->get(RouteCollectorInterface::class)
    );
});

$container->set(CheckUrlsHandler::class, function ($container) {
    return new CheckUrlsHandler(
        $container->get(RouteCollectorInterface::class),
        $container->get('renderer'),
        $container->get('flash'),
        $container->get(UrlRepository::class),
        $container->get(UrlCheckRepository::class),
        $container->get(UrlChecker::class)
    );
});

$app->get('/', function ($request, $response) {
    return $this->get('renderer')->render($response, 'home.phtml');
})->setName('home');
$app->post('/urls', AddUrlsHandler::class)->setName('addUrl');
$app->get('/urls', ListUrlsHandler::class)->setName('urls');
$app->get('/urls/{id}', ShowUrlsHandler::class)->setName('url');
$app->post('/urls/{id}/checks', CheckUrlsHandler::class)->setName('checkUrl');
$app->get('/urls/{id}/checks', CheckUrlsHandler::class)->setName('checkUrl');

$app->run();
